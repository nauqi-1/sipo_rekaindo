<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>New Password</title>
  <link rel="stylesheet" href="{{ asset('css/forgot-pw.css') }}">
</head>
<body>
  <div class="container">
    <div class="card">
      <div class="back-button">
        <a href="{{ route('verif-email') }}"><</a>
      </div>
      <div class="logo">
        <img src="/img/logo-reka.png" alt="Reka Inka Group">
      </div>
      <h1>New Password</h1>
      <p>Set the new password for your account so you can login and access 
      all featuress.</p>
      <form action="{{ route('reset-password.update') }}" method="POST">
      @csrf
        <input type="hidden" name="email" value="{{ $email }}">
        <label for="password">Enter New Password</label>
        <input type="password" id="password" name="password" placeholder="Enter new password" required autocomplete="new-password">
        <label for="password_confirmation">Confirm Password</label>
        <input type="password" id="password_confirmation" name="password_confirmation" placeholder="Confirm password" required autocomplete="new-password">
        <button type="submit">UPDATE PASSWORD</button>
      </form>
    </div>
  </div>
</body>
</html>
