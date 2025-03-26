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
      <h1>Kata Sandi Baru</h1>
      <p>Tetapkan kata sandi baru untuk akun Anda sehingga Anda dapat masuk dan mengakses semua fitur.</p>
      <form action="{{ route('reset-password.update') }}" method="POST">
      @csrf
        <input type="hidden" name="email" value="{{ $email }}">
        <label for="password">Masukkan Kata Sandi Baru</label>
        <input type="password" id="password" name="password" placeholder="Enter new password" required autocomplete="new-password">
        <label for="password_confirmation">Konfirmasi Kata Sandi</label>
        <input type="password" id="password_confirmation" name="password_confirmation" placeholder="Confirm password" required autocomplete="new-password">
        <button type="submit">PERBARUI KATA SANDI</button>
      </form>
    </div>
  </div>
</body>
</html>
