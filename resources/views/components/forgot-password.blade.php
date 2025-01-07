<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body>
    <div class="forgot-password-container">
        <div class="forgot-password-box">
            <a href="/" class="back-button">&larr;</a>
            <div class="logo">
                <img src="{{ asset('img/logo2.png') }}" alt="Reka INKA Group Logo">
            </div>
            <h2>Forgot Password</h2>
            <p>Enter your email for the verification process, we will send a 4-digit code to your email.</p>
            <form action="#">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" placeholder="Enter username" required>
                <a href="/index"><button type="submit" class="continue-button">CONTINUE</button></a>
            </form>
        </div>
    </div>
</body>
</html>
