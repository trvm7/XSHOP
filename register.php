<?php
include 'config.php';

$secretKey = "6LdLhYIrAAAAAL1upWy0PGk701YV5lDLcP3VzCML";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $captcha = $_POST['g-recaptcha-response'];

    if (!$captcha) {
        echo "يرجى التحقق من أنك لست روبوتًا.";
        exit;
    }

    $response = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=$secretKey&response=$captcha");
    $responseKeys = json_decode($response, true);

    if(intval($responseKeys["success"]) !== 1) {
        echo "فشل التحقق من CAPTCHA. يرجى المحاولة مرة أخرى.";
    } else {
        $username = $_POST['username'];
        $email = $_POST['email'];
        $password = $_POST['password'];
        $confirmPassword = $_POST['confirm_password'];

        if ($password !== $confirmPassword) {
            echo "كلمة المرور وتأكيد كلمة المرور غير متطابقتين.";
        } else {
            $password_hashed = password_hash($password, PASSWORD_DEFAULT);

            $sql = "INSERT INTO users (username, email, password) VALUES ('$username', '$email', '$password_hashed')";

            if ($conn->query($sql) === TRUE) {
                header("Location: login.php");
                exit;
            } else {
                echo "حدث خطأ: " . $conn->error;
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <title>تسجيل مستخدم</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="form-container">
        <h2>تسجيل</h2>
        <form method="POST">
            <input type="text" name="username" placeholder="اسم المستخدم" required>
            <input type="email" name="email" placeholder="البريد الإلكتروني" required>
            <input type="password" name="password" id="password" placeholder="كلمة المرور" required onkeyup="checkPasswordStrength();">
            <input type="password" name="confirm_password" id="confirm_password" placeholder="تأكيد كلمة المرور" required>
            
            <div id="password-strength-status"></div>
            <progress id="password-strength-bar" value="0" max="100"></progress>
            
            <div class="g-recaptcha" data-sitekey="6LdLhYIrAAAAAENSEuvZnkIN9RHFLxvx9ZxhCJlP"></div>
            <button type="submit">تسجيل</button>
        </form>
    </div>
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <script>
  function checkPasswordStrength() {
    var password = document.getElementById("password").value;
    var strengthBar = document.getElementById("password-strength-bar");
    var strengthText = document.getElementById("password-strength-status");
    var strengthIndicator = 0;

    
    if (password.length >= 8) {
        strengthIndicator = 3; // قوي
    } else if (password.length >= 5) {
        strengthIndicator = 2; // متوسط
    } else {
        strengthIndicator = 1; // ضعيف
    }

   
    var strengthPercentage = (strengthIndicator / 3) * 100;
    strengthBar.value = strengthPercentage;

    
    if (strengthIndicator === 1) {
        strengthText.innerHTML = "ضعيف";
        strengthBar.style.backgroundColor = "red";
    } else if (strengthIndicator === 2) {
        strengthText.innerHTML = "متوسط";
        strengthBar.style.backgroundColor = "orange";
    } else {
        strengthText.innerHTML = "قوي";
        strengthBar.style.backgroundColor = "green";
    }
}

    </script>
</body>
</html>