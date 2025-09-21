<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@picocss/pico@2/css/pico.min.css">
    <style>main{max-width:480px;margin-top:10vh}</style>
    </head>
<body>
<main class="container">
    <h3>Admin Login</h3>
    <form action="{{ route('admin.login.post') }}" method="POST">
        @csrf
        <label>Login (username)
            <input type="text" name="login" value="{{ old('login') }}" required />
        </label>
        <label>Password
            <input type="password" name="password" required />
        </label>
        <label>
            <input type="checkbox" name="remember" /> Remember me
        </label>
        <button type="submit">Login</button>
    </form>
    @if ($errors->any())
        <article class="contrast" style="margin-top:1rem">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </article>
    @endif
</main>
</body>
</html>


