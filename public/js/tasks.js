document.addEventListener('DOMContentLoaded', () => {
    const csrfMeta = document.querySelector('meta[name="csrf-token"]');
    const csrfToken = csrfMeta ? csrfMeta.getAttribute('content') : '';

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text ?? '';
        return div.innerHTML;
    }

    function formatDate(dateString) {
        if (!dateString) {
            return '';
        }

        const parts = dateString.split('-');
        if (parts.length !== 3) {
            return dateString;
        }

        const date = new Date(Number(parts[0]), Number(parts[1]) - 1, Number(parts[2]));
        return date.toLocaleDateString('en-US', {
            month: 'short',
            day: '2-digit',
            year: 'numeric',
        });
    }

    function isValidDateString(value) {
        if (!/^\d{4}-\d{2}-\d{2}$/.test(value)) {
            return false;
        }

        const [year, month, day] = value.split('-').map(Number);
        const date = new Date(year, month - 1, day);

        return (
            date.getFullYear() === year &&
            date.getMonth() === month - 1 &&
            date.getDate() === day
        );
    }

    function showAlert(container, type, messages) {
        if (!container) {
            return;
        }

        container.className = type === 'success' ? 'alert-success' : 'alert-error';

        if (Array.isArray(messages)) {
            const listItems = messages.map((msg) => `<li>${escapeHtml(msg)}</li>`).join('');
            container.innerHTML = `<ul>${listItems}</ul>`;
        } else {
            container.textContent = messages;
        }
    }

    function clearAlert(container) {
        if (!container) {
            return;
        }

        container.className = 'hidden';
        container.innerHTML = '';
    }

    function showFieldError(form, field, message) {
        const errorEl = form.querySelector(`[data-error-for="${field}"]`);
        const inputEl = form.querySelector(`[name="${field}"]`);

        if (errorEl) {
            errorEl.textContent = message;
            errorEl.classList.remove('hidden');
        }

        if (inputEl) {
            inputEl.classList.add('input-error');
        }
    }

    function clearFieldError(form, field) {
        const errorEl = form.querySelector(`[data-error-for="${field}"]`);
        const inputEl = form.querySelector(`[name="${field}"]`);

        if (errorEl) {
            errorEl.textContent = '';
            errorEl.classList.add('hidden');
        }

        if (inputEl) {
            inputEl.classList.remove('input-error');
        }
    }

    function clearFieldErrors(form) {
        form.querySelectorAll('.field-error').forEach((el) => {
            el.textContent = '';
            el.classList.add('hidden');
        });

        form.querySelectorAll('.input-error').forEach((el) => {
            el.classList.remove('input-error');
        });
    }

    function validateDueDate(form) {
        const dueDateInput = form.querySelector('#due_date');
        if (!dueDateInput) {
            return true;
        }

        const value = dueDateInput.value.trim();

        if (value === '') {
            showFieldError(form, 'due_date', 'The due date field is required.');
            return false;
        }

        if (!isValidDateString(value)) {
            showFieldError(form, 'due_date', 'Please enter a valid due date.');
            return false;
        }

        clearFieldError(form, 'due_date');
        return true;
    }

    function attachDueDateValidation(form) {
        const dueDateInput = form.querySelector('#due_date');
        if (!dueDateInput) {
            return;
        }

        dueDateInput.addEventListener('invalid', (event) => {
            event.preventDefault();
            validateDueDate(form);
            dueDateInput.focus();
        });

        dueDateInput.addEventListener('input', () => {
            if (dueDateInput.value.trim() !== '') {
                validateDueDate(form);
            }
        });
    }

    function showFormErrors(form, alertBox, data, fallbackMessage) {
        if (data && data.errors) {
            const unmapped = [];

            Object.entries(data.errors).forEach(([field, messages]) => {
                const errorEl = form.querySelector(`[data-error-for="${field}"]`);

                if (errorEl) {
                    showFieldError(form, field, messages[0]);
                } else {
                    unmapped.push(...messages);
                }
            });

            if (unmapped.length > 0) {
                showAlert(alertBox, 'error', unmapped);
            } else {
                showAlert(alertBox, 'error', 'Please review the highlighted fields below.');
            }
            return;
        }

        const message = data && data.message ? data.message : fallbackMessage;
        showAlert(alertBox, 'error', message);
    }

    async function loadCategories(selectElement, placeholderText) {
        const response = await fetch('/api/categories', {
            headers: {
                'Accept': 'application/json',
            },
            credentials: 'same-origin',
        });

        if (!response.ok) {
            throw new Error('Failed to load categories.');
        }

        const categories = await response.json();
        selectElement.innerHTML = `<option value="">${escapeHtml(placeholderText)}</option>`;

        categories.forEach((category) => {
            const option = document.createElement('option');
            option.value = category.id;
            option.textContent = category.name;
            selectElement.appendChild(option);
        });
    }

    const dashboard = document.getElementById('tasks-dashboard');
    if (dashboard) {
        const alertBox = document.getElementById('dashboard-alert');
        const searchInput = document.getElementById('search-input');
        const categoryFilter = document.getElementById('category-filter');
        const loadingBox = document.getElementById('tasks-loading');
        const emptyBox = document.getElementById('tasks-empty');
        const emptyText = document.getElementById('tasks-empty-text');
        const tableWrapper = document.getElementById('tasks-table-wrapper');
        const tableBody = document.getElementById('tasks-table-body');
        const paginationControls = document.getElementById('pagination-controls');
        const prevBtn = document.getElementById('prev-page-btn');
        const nextBtn = document.getElementById('next-page-btn');
        const paginationInfo = document.getElementById('pagination-info');
        const deleteModal = document.getElementById('delete-modal');
        const deleteModalMessage = document.getElementById('delete-modal-message');
        const cancelDeleteBtn = document.getElementById('cancel-delete-btn');
        const confirmDeleteBtn = document.getElementById('confirm-delete-btn');

        let currentPage = 1;
        let lastPage = 1;
        let searchTimer = null;
        let pendingDeleteId = null;

        const savedMessage = sessionStorage.getItem('task_success');
        if (savedMessage) {
            showAlert(alertBox, 'success', savedMessage);
            sessionStorage.removeItem('task_success');
        }

        function openDeleteModal(taskId, taskTitle) {
            pendingDeleteId = taskId;
            deleteModalMessage.textContent = taskTitle
                ? `Are you sure you want to delete "${taskTitle}"? This action cannot be undone.`
                : 'Are you sure you want to delete this task? This action cannot be undone.';
            confirmDeleteBtn.disabled = false;
            deleteModal.classList.remove('hidden');
            cancelDeleteBtn.focus();
        }

        function closeDeleteModal() {
            pendingDeleteId = null;
            confirmDeleteBtn.disabled = false;
            deleteModal.classList.add('hidden');
        }

        async function loadTasks(page = 1) {
            currentPage = page;
            loadingBox.classList.remove('hidden');
            emptyBox.classList.add('hidden');
            tableWrapper.classList.add('hidden');
            paginationControls.classList.add('hidden');

            const params = new URLSearchParams();
            params.set('page', String(currentPage));

            const searchValue = searchInput.value.trim();
            if (searchValue !== '') {
                params.set('search', searchValue);
            }

            if (categoryFilter.value !== '') {
                params.set('category_id', categoryFilter.value);
            }

            try {
                const response = await fetch(`/api/tasks?${params.toString()}`, {
                    headers: {
                        'Accept': 'application/json',
                    },
                    credentials: 'same-origin',
                });

                if (response.status === 401) {
                    window.location.href = '/login';
                    return;
                }

                if (!response.ok) {
                    throw new Error('Failed to load tasks.');
                }

                const result = await response.json();
                const tasks = result.data || [];
                currentPage = result.current_page || 1;
                lastPage = result.last_page || 1;

                loadingBox.classList.add('hidden');

                if (tasks.length === 0) {
                    if (currentPage > 1) {
                        loadTasks(currentPage - 1);
                        return;
                    }

                    const hasFilters = searchValue !== '' || categoryFilter.value !== '';
                    emptyText.textContent = hasFilters
                        ? 'No tasks match your search or category filter.'
                        : 'No tasks yet. Click "New Task" to create one.';
                    emptyBox.classList.remove('hidden');
                    return;
                }

                tableBody.innerHTML = tasks.map((task) => {
                    const categoryName = task.category ? task.category.name : 'Uncategorized';
                    const statusClass = task.status === 'done' ? 'badge-done' : 'badge-pending';
                    const statusLabel = task.status.charAt(0).toUpperCase() + task.status.slice(1);
                    const descHtml = task.description
                        ? `<div class="task-desc">${escapeHtml(task.description)}</div>`
                        : '';

                    return `
                        <tr>
                            <td>
                                <div class="task-title">${escapeHtml(task.title)}</div>
                                ${descHtml}
                            </td>
                            <td>
                                <span class="badge badge-category">${escapeHtml(categoryName)}</span>
                            </td>
                            <td>
                                <span class="badge ${statusClass}">${escapeHtml(statusLabel)}</span>
                            </td>
                            <td class="cell-nowrap">${escapeHtml(formatDate(task.due_date))}</td>
                            <td class="cell-nowrap">
                                <div class="table-actions">
                                    <a href="/tasks/${task.id}/edit" class="btn-link">Edit</a>
                                    <button type="button" class="btn-delete" data-delete-id="${task.id}" data-delete-title="${escapeHtml(task.title)}">Delete</button>
                                </div>
                            </td>
                        </tr>
                    `;
                }).join('');

                tableWrapper.classList.remove('hidden');
                paginationControls.classList.remove('hidden');
                paginationInfo.textContent = `Page ${currentPage} of ${lastPage}`;
                prevBtn.disabled = currentPage <= 1;
                nextBtn.disabled = currentPage >= lastPage;
            } catch (error) {
                loadingBox.classList.add('hidden');
                showAlert(alertBox, 'error', 'Could not load tasks. Please try again.');
            }
        }

        async function deleteTask(taskId) {
            clearAlert(alertBox);

            try {
                const response = await fetch(`/api/tasks/${taskId}`, {
                    method: 'DELETE',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                    credentials: 'same-origin',
                });

                const data = await response.json().catch(() => ({}));

                if (!response.ok) {
                    showAlert(alertBox, 'error', data.message || 'Failed to delete task.');
                    return;
                }

                showAlert(alertBox, 'success', data.message || 'Task deleted successfully.');
                loadTasks(currentPage);
            } catch (error) {
                showAlert(alertBox, 'error', 'Could not delete task. Please try again.');
            }
        }

        tableBody.addEventListener('click', (event) => {
            const deleteBtn = event.target.closest('[data-delete-id]');
            if (!deleteBtn) {
                return;
            }

            const taskId = deleteBtn.getAttribute('data-delete-id');
            const taskTitle = deleteBtn.getAttribute('data-delete-title') || '';
            openDeleteModal(taskId, taskTitle);
        });

        cancelDeleteBtn.addEventListener('click', closeDeleteModal);

        confirmDeleteBtn.addEventListener('click', async () => {
            if (!pendingDeleteId) {
                return;
            }

            const taskId = pendingDeleteId;
            confirmDeleteBtn.disabled = true;
            await deleteTask(taskId);
            closeDeleteModal();
        });

        deleteModal.addEventListener('click', (event) => {
            if (event.target === deleteModal) {
                closeDeleteModal();
            }
        });

        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape' && !deleteModal.classList.contains('hidden')) {
                closeDeleteModal();
            }
        });

        searchInput.addEventListener('input', () => {
            clearTimeout(searchTimer);
            searchTimer = setTimeout(() => {
                loadTasks(1);
            }, 300);
        });

        categoryFilter.addEventListener('change', () => {
            loadTasks(1);
        });

        prevBtn.addEventListener('click', () => {
            if (currentPage > 1) {
                loadTasks(currentPage - 1);
            }
        });

        nextBtn.addEventListener('click', () => {
            if (currentPage < lastPage) {
                loadTasks(currentPage + 1);
            }
        });

        loadCategories(categoryFilter, 'All Categories').catch(() => {
            showAlert(alertBox, 'error', 'Could not load categories.');
        });
        loadTasks(1);
    }

    const createForm = document.getElementById('task-create-form');
    if (createForm) {
        const alertBox = document.getElementById('form-alert');
        const categorySelect = document.getElementById('category_id');
        const submitBtn = document.getElementById('submit-btn');

        attachDueDateValidation(createForm);

        loadCategories(categorySelect, 'Select a category').catch(() => {
            showAlert(alertBox, 'error', 'Could not load categories. Please refresh the page.');
        });

        createForm.addEventListener('submit', async (event) => {
            event.preventDefault();
            clearAlert(alertBox);
            clearFieldErrors(createForm);

            if (!validateDueDate(createForm)) {
                showAlert(alertBox, 'error', 'Please review the highlighted fields below.');
                return;
            }

            submitBtn.disabled = true;

            const payload = {
                title: document.getElementById('title').value.trim(),
                description: document.getElementById('description').value.trim() || null,
                category_id: categorySelect.value,
                status: document.getElementById('status').value,
                due_date: document.getElementById('due_date').value,
            };

            try {
                const response = await fetch('/api/tasks', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                    credentials: 'same-origin',
                    body: JSON.stringify(payload),
                });

                const data = await response.json().catch(() => ({}));

                if (!response.ok) {
                    showFormErrors(createForm, alertBox, data, 'Failed to create task.');
                    submitBtn.disabled = false;
                    return;
                }

                sessionStorage.setItem('task_success', data.message || 'Task created successfully.');
                window.location.href = '/tasks';
            } catch (error) {
                showAlert(alertBox, 'error', 'Something went wrong. Please try again.');
                submitBtn.disabled = false;
            }
        });
    }

    const editForm = document.getElementById('task-edit-form');
    if (editForm) {
        const taskId = editForm.getAttribute('data-task-id');
        const alertBox = document.getElementById('form-alert');
        const categorySelect = document.getElementById('category_id');
        const submitBtn = document.getElementById('submit-btn');

        attachDueDateValidation(editForm);

        async function initEditForm() {
            try {
                await loadCategories(categorySelect, 'Select a category');

                const response = await fetch(`/api/tasks/${taskId}`, {
                    headers: {
                        'Accept': 'application/json',
                    },
                    credentials: 'same-origin',
                });

                const task = await response.json().catch(() => ({}));

                if (!response.ok) {
                    showAlert(alertBox, 'error', task.message || 'Could not load task details.');
                    submitBtn.disabled = true;
                    return;
                }

                document.getElementById('title').value = task.title || '';
                document.getElementById('description').value = task.description || '';
                categorySelect.value = task.category_id || '';
                document.getElementById('status').value = task.status || 'pending';
                document.getElementById('due_date').value = task.due_date || '';
            } catch (error) {
                showAlert(alertBox, 'error', 'Could not load task details. Please refresh the page.');
                submitBtn.disabled = true;
            }
        }

        editForm.addEventListener('submit', async (event) => {
            event.preventDefault();
            clearAlert(alertBox);
            clearFieldErrors(editForm);

            if (!validateDueDate(editForm)) {
                showAlert(alertBox, 'error', 'Please review the highlighted fields below.');
                return;
            }

            submitBtn.disabled = true;

            const payload = {
                title: document.getElementById('title').value.trim(),
                description: document.getElementById('description').value.trim() || null,
                category_id: categorySelect.value,
                status: document.getElementById('status').value,
                due_date: document.getElementById('due_date').value,
            };

            try {
                const response = await fetch(`/api/tasks/${taskId}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                    credentials: 'same-origin',
                    body: JSON.stringify(payload),
                });

                const data = await response.json().catch(() => ({}));

                if (!response.ok) {
                    showFormErrors(editForm, alertBox, data, 'Failed to update task.');
                    submitBtn.disabled = false;
                    return;
                }

                sessionStorage.setItem('task_success', data.message || 'Task updated successfully.');
                window.location.href = '/tasks';
            } catch (error) {
                showAlert(alertBox, 'error', 'Something went wrong. Please try again.');
                submitBtn.disabled = false;
            }
        });

        initEditForm();
    }
});
