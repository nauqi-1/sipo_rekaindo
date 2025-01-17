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
        <a href="{{ route('forgot-pw') }}"><img src="/img/user-manage/Vector_back.png" alt="back"></a>
      </div>
      <div class="logo">
        <img src="/img/logo-reka.png" alt="Reka Inka Group">
      </div>
      <h1>Verification</h1>
      <p>Enter your 4 digits code that you received on your email</p>
      <form action="{{ route('new-pw') }}" method="GET">
        <div class="input-verifemail">
          <input type="text" maxlength="1" name="digit1" class="code-input" required>
          <input type="text" maxlength="1" name="digit2" class="code-input" required>
          <input type="text" maxlength="1" name="digit3" class="code-input" required>
          <input type="text" maxlength="1" name="digit4" class="code-input" required>
        </div>
        <p id="timer" class="timer">15s</p>
        <button type="submit">VERIFY</button>
      </form>
      <p class="resend">If you didn't receive a code! <a href="#" class="resend-link">Resend</a></p>
    </div>
  </div>

  <script>
        // Menambahkan countdown timer
        let countdown = 15; // Set initial time (15 seconds)
        const timerElement = document.getElementById('timer');

        const timerInterval = setInterval(() => {
            if (countdown > 0) {
            timerElement.textContent = `${countdown--}s`;
            } else {
            clearInterval(timerInterval); // Hentikan timer setelah 0
            timerElement.textContent = 'Time is up!';
            }
        }, 1000); // Update timer setiap detik (1000ms)

        // Pindah fokus ke input berikutnya setelah mengetik angka
        document.querySelectorAll('.code-input').forEach((input, index, inputs) => {
            input.addEventListener('input', () => {
            // Jika input sudah penuh, fokuskan ke input berikutnya
            if (input.value.length === input.maxLength) {
                const nextInput = inputs[index + 1];
                if (nextInput) {
                nextInput.focus();
                }
            }
            });

            // Jika user menghapus digit, fokuskan ke input sebelumnya
            input.addEventListener('keydown', (e) => {
            if (e.key === 'Backspace' && input.value === '') {
                const prevInput = inputs[index - 1];
                if (prevInput) {
                prevInput.focus();
                }
            }
            });
        });
        </script>

</body>
</html>

