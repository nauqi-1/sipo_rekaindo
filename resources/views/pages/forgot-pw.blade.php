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
        <a href="{{ url('/') }}"><img src="/img/user-manage/Vector_back.png" alt="back"></a>
      </div>
      <div class="logo">
        <img src="/img/logo-reka.png" alt="Reka Inka Group">
      </div>
      <h1>Forgot Password</h1>
      <p>Enter your email for the verification process, we will send 4 digits code for your email.</p>
      <form action="{{ route('forgot-password.send') }}" method="POST">
      @csrf
        <label for="email">Email</label>
        <input type="email" id="email" name="email" placeholder="Enter email" required>
        <button type="submit">CONTINUE</button>
      </form>
    </div>
  </div>
</body>
</html>
