<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login</title>
  <link rel="stylesheet" href="{{ asset('css/login.css') }}">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body>
    <div class="container">
        <div class="card login-card">
            <div class="card-header">
                <img src="/img/backgroundLogin.png" alt="background" class="background-image">
                <div class="overlay">
                    <div class="logo">
                        <img src="/img/logo-reka.png" alt="logo reka">
                    </div>
                    <h1>SISTEM INFORMASI PERSURATAN ONLINE</h1>
                </div>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('login') }}">
                    @csrf
                    
                    <!-- Session Status -->
                    <x-auth-session-status class="mb-4" :status="session('status')" />

                    <!-- Email Address -->
                    <div class="input-group">
                        <i class="bi bi-person"></i>
                        <input type="email" id="email" name="email" placeholder="Enter email" value="{{ old('email') }}" required autofocus autocomplete="email">
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    <!-- Password -->
                    <div class="input-group">
                        <i class="bi bi-lock"></i>
                        <input type="password" id="password" name="password" placeholder="Enter password" required autocomplete="current-password">
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>

                    <!-- Forgot Password -->
                    <div class="col">
                        @if (Route::has('forgot-password'))
                            <a href="{{ route('forgot-password') }}">Lupa Password?</a>
                        @endif
                    </div>

                    <!-- Remember Me -->
                    <div class="custom-control custom-checkbox small">
                        <input type="checkbox" class="custom-control-input" id="customCheck" name="remember">
                        <label class="custom-control-label" for="customCheck">Ingatkan Saya</label>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit">MASUK</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
