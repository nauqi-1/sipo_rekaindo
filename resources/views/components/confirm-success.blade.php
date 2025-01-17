<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Successfully</title>
  <link rel="stylesheet" href="{{ asset('css/forgot-pw.css') }}">
</head>
<body>
  <div class="container">
    <div class="card">
      <div class="back-button">
        <a href="{{ route('new-pw') }}"><img src="/img/user-manage/Vector_back.png" alt="back"></a>
      </div>
      <div class="logo">
        <img src="/img/logo-reka.png" alt="Reka Inka Group">
      </div>
      <img src="/img/success.png" alt="success">
      <h1 class="success">Successfully</h1>
      <p class="success_pw">Your password has been reset successfully</p>
      <form action="#">
        <button type="submit">CONTINUE</button>
      </form>
    </div>
  </div>
</body>
</html>
