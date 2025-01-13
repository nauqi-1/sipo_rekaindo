<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Forgot Password</title>
  <link rel="stylesheet" href="{{ asset('css/forgot-pw.css') }}">
</head>
<body>
  <div class="container">
    <div class="card">
      <div class="back-button">
        <a href="{{ route('login') }}"><</a>
      </div>
      <div class="logo">
        <img src="/img/logo-reka.png" alt="Reka Inka Group">
      </div>
      <h1>Forgot Password</h1>
      <p>Enter your email for the verification process, we will send 4 digits code for your email.</p>
      <form action="{{ route('verif-email') }}" method="GET">
        <label for="email">Email</label>
        <input type="email" id="email" name="email" placeholder="Enter username" required>
        <button type="submit">CONTINUE</button>
      </form>
    </div>
  </div>
</body>
</html>
