<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Task Manager')</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
    <div class="layout">
        <aside class="sidebar">
            <div class="brand">Task Manager</div>
            <nav class="nav">
                <a href="{{ route('tasks.index') }}" class="nav-item active">My Tasks</a>
            </nav>
        </aside>

        <main class="main-content">
            @yield('content')
        </main>
    </div>
</body>
</html>
