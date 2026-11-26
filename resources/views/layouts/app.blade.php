<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Task Manager')</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
    <div class="layout">
        <aside class="sidebar">
            <div class="brand">Task Manager</div>
            <nav class="nav">
                <a href="{{ route('tasks.index') }}" class="nav-item {{ request()->is('tasks*') ? 'active' : '' }}">My Tasks</a>
            </nav>
            <form method="POST" action="{{ route('logout') }}" class="logout-form">
                @csrf
                <button type="submit" class="logout-button">Log out</button>
            </form>
        </aside>

        <main class="main-content">
            @yield('content')
        </main>
    </div>

    <script src="{{ asset('js/tasks.js') }}"></script>
</body>
</html>
