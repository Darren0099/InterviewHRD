<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="theme-color" content="#1b1432">
    <title>Masuk · Interview HRD</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}?v=2">
</head>
<body class="login-body">
<main class="login-card">
    <div class="brand-logo">HR</div>
    <h1>Interview HRD</h1>
    <p class="sub">Masuk untuk mengelola penilaian kandidat Youth Ranger Indonesia.</p>

    <form method="post" action="{{ route('login.attempt') }}">
        @csrf

        @if($errors->any())
            <div class="form-error">{{ $errors->first() }}</div>
        @endif

        <label class="field">
            <span>Username</span>
            <input type="text" name="username" class="input" value="{{ old('username') }}" autocomplete="username" autocapitalize="none" required autofocus>
        </label>

        <label class="field">
            <span>Password</span>
            <input type="password" name="password" class="input" autocomplete="current-password" required>
        </label>

        <button type="submit" class="btn btn-dark btn-block btn-lg">Masuk</button>
    </form>
</main>
</body>
</html>
