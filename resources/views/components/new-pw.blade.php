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
        <a href="#"><</a>
      </div>
      <div class="logo">
        <img src="/img/logo-reka.png" alt="Reka Inka Group">
      </div>
      <h1>New Password</h1>
      <p>Set the new password for your account so you can login and access 
      all featuress.</p>
      <form action="{{ route('confirm-success') }}" method="GET">
        <label for="new_pw">Enter New Password</label>
        <input type="password" id="new_pw" name="new-pw" placeholder="Enter new password" required>
        <label for="confirm_pw">Enter New Password</label>
        <input type="password" id="confirm_pw" name="confirm_pw" placeholder="Confirm password" required>
        <button type="submit">UPDATE PASSWORD</button>
      </form>
    </div>
  </div>
</body>
</html>
