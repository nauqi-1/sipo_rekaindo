<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Verification</title>
  <link rel="stylesheet" href="{{ asset('css/forgot-pw.css') }}">
</head>
<body>
  <div class="container">
    <div class="card">
      <div class="back-button">
        <a href="#"><img src="/img/user-manage/Vector_back.png" alt="back"></a>
      </div>
      <div class="logo">
        <img src="/img/logo-reka.png" alt="Reka Inka Group">
      </div>
      <h1>Verification</h1>
      <p>Enter your 4 digits code that you received on your email</p>
      <form action="{{ route('verify-code.check') }}" method="POST">
        @csrf
        <div class="input-verifemail">
          <input type="hidden" name="email" value="{{ $email }}">
          <input type="text" maxlength="1" name="digit1" class="code-input" required>
          <input type="text" maxlength="1" name="digit2" class="code-input" required>
          <input type="text" maxlength="1" name="digit3" class="code-input" required>
          <input type="text" maxlength="1" name="digit4" class="code-input" required>
        </div>
        <p id="timer" class="timer">60s</p>
        <button type="submit">VERIFY</button>
      </form>
      <p class="resend">If you didn't receive a code! <a href="#" class="resend-link">Resend</a></p>
    </div>
  </div>


  <script>
    // Automatically focus the next input field
    const inputs = document.querySelectorAll('.code-input');
    inputs.forEach((input, index) => {
      input.addEventListener('input', () => {
        if (input.value.length === 1 && index < inputs.length - 1) {
          inputs[index + 1].focus();
        }
      });

      input.addEventListener('keydown', (e) => {
        if (e.key === 'Backspace' && input.value === '' && index > 0) {
          inputs[index - 1].focus();
        }
      });
    });

    let timeLeft = 60;
    const timerElement = document.getElementById('timer');
    const countdown = setInterval(() => {
      if (timeLeft > 0) {
        timeLeft--;
        timerElement.textContent = `${timeLeft}s`;
      } else {
        clearInterval(countdown);
        timerElement.textContent = "Expired!";
        // Optionally disable the form
        document.querySelector('button').disabled = true;
      }
    }, 1000);
        
        
       
        </script>

</body>
</html>

