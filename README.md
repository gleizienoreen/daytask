# Task Manager (DayTask)

A simple Task Manager web application built with Laravel 13, PostgreSQL, Blade, and native JavaScript Fetch.

## Main Features

- Session-based login and logout with authentication middleware
- Task management (create, view, edit, and delete)
- Task ownership protection so users can only access their own tasks
- Same-origin JSON API for tasks and categories
- Native JavaScript Fetch integration on Blade pages with CSRF protection
- Search by task title, filter by category, and paginated task list
- Reusable Blade components for buttons, inputs, alerts, and tables

## Technologies Used

- PHP 8.5 / Laravel 13
- PostgreSQL
- Blade Templates
- Vanilla JavaScript (Fetch API)
- Plain CSS

## Installation and Setup

1. Clone or open the project directory and install PHP dependencies:
   ```bash
   composer install
   ```
2. Create your environment file and generate an application key:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
3. Configure your PostgreSQL database connection in `.env`:
   ```env
   DB_CONNECTION=pgsql
   DB_HOST=127.0.0.1
   DB_PORT=5432
   DB_DATABASE=your_database_name
   DB_USERNAME=your_postgres_username
   DB_PASSWORD=your_postgres_password
   ```
   Make sure the PostgreSQL service is running and the target database exists before running migrations.
4. Run migrations and seed sample data:
   ```bash
   php artisan migrate
   php artisan db:seed
   ```
5. Start the development server:
   ```bash
   php artisan serve
   ```
6. Open `http://127.0.0.1:8000` in your browser.

## Sample Login Credentials (Local Testing)

Created by `UserSeeder`:

- **Email:** `user@example.com`
- **Password:** `password`

## API Endpoints

All endpoints require an authenticated session and use CSRF protection for write requests:

- `GET /api/categories` — Retrieve available categories
- `GET /api/tasks` — Retrieve the logged-in user's tasks (supports `search`, `category_id`, and `page`)
- `GET /api/tasks/{id}` — Retrieve a single task owned by the logged-in user
- `POST /api/tasks` — Create a new task
- `PUT /api/tasks/{id}` — Update an existing task
- `DELETE /api/tasks/{id}` — Delete a task

## Design Credits

- Delete confirmation dialog layout adapted from the [Flowbite Pop-up Modal](https://flowbite.com/docs/components/modal/) component (MIT License)
- Remaining layout and styling were created specifically for this project using custom plain CSS.
