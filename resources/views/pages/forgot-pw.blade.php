<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Lupa Kata Sandi</title>
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
      <h1>Lupa Kata Sandi</h1>
      <p>Masukkan email Anda untuk proses verifikasi, kami akan mengirimkan kode 4 digit ke email Anda.</p>
      <form action="{{ route('forgot-password.send') }}" method="POST">
      @csrf
        <label for="email">Email</label>
        <input type="email" id="email" name="email" placeholder="Enter email" required>
        <button type="submit">MELANJUTKAN</button>
      </form>
    </div>
  </div>
</body>
</html>
