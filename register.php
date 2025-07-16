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
          

            $sql = "INSERT INTO users (username, email, password) VALUES ('$username', '$email', '$password')";

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
    <link rel="stylesheet" href="css/Logsun.css">
</head>
<body>
    <div class="login-container">
        <div class="login-box">
            <h2>تسجيل</h2>
            <form class="login-form" method="POST">
                <input type="text" name="username" placeholder="اسم المستخدم" required>
                <input type="email" name="email" placeholder="البريد الإلكتروني" required>
                <input type="password" name="password" placeholder="كلمة المرور" required>
                <input type="password" name="confirm_password" placeholder="تأكيد كلمة المرور" required>
                <hr style="margin: 24px 0;">
                <div class="g-recaptcha" data-sitekey="6LdLhYIrAAAAAENSEuvZnkIN9RHFLxvx9ZxhCJlP"></div>
                <button type="submit">تسجيل</button>
            </form>
            <div class="login-links">
                لديك حساب بالفعل؟ <a href="login.php">تسجيل الدخول</a>
            </div>
        </div>
    </div>
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
</body>
</html>