<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Task Manager</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body class="auth-page">
    <div class="auth-card">
        <div class="auth-brand">Task Manager</div>
        <h1 class="auth-heading">Login</h1>

        @if ($errors->any())
            <x-alert type="error">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </x-alert>
        @endif

        <form method="POST" action="{{ route('login') }}" class="auth-form" novalidate>
            @csrf

            <x-input
                label="Email"
                name="email"
                type="email"
                :value="old('email')"
                required
                autofocus
            />

            <x-input
                label="Password"
                name="password"
                type="password"
                required
            />

            <x-button type="submit" class="auth-submit">Login</x-button>
        </form>
    </div>
</body>
</html>
