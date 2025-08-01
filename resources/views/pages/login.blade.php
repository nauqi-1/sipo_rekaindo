<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-LN+7fdVzj6u52u30Kp6M/trliBMCMKTyK833zpbD+pXdCLuTusPj697FH4R/5mcr" crossorigin="anonymous">
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

                <form method="POST" action="{{ route('login') }}" novalidate>
                    @csrf
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <!-- Session Status -->
                    <x-auth-session-status class="mb-4" :status="session('status')" />

                    <div class="form-group mt-3 position-relative">
                        <span>
                            <i class="fas fa-user"></i>
                        </span>
                        <input type="email" class="form-control ps-5" name="email" placeholder="Enter email" required
                            autofocus>
                    </div>

                    <div class="form-group mt-3 position-relative">
                        <span>
                            <i class="fas fa-lock"></i>
                        </span>
                        <input type="password" class="form-control ps-5 pe-5" name="password"
                            placeholder="Enter password" required>
                        <span class="position-absolute top-50 end-0 translate-middle-y me-3 text-muted"
                            style="cursor: pointer;" onclick="togglePassword(this)">
                            <i class="fas fa-eye"></i>
                        </span>
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-ndDqU0Gzau9qJ1lfW4pNLlhNTkCfHzAVBReH9diLvGRem5+R9g2FzA8ZGN954O5Q"
        crossorigin="anonymous"></script>
    <script>
    function togglePassword(el) {
        const input = el.previousElementSibling;
        if (input.type === "password") {
            input.type = "text";
            el.querySelector('i').classList.replace('fa-eye', 'fa-eye-slash');
        } else {
            input.type = "password";
            el.querySelector('i').classList.replace('fa-eye-slash', 'fa-eye');
        }
    }
</script>






</body>

</html>