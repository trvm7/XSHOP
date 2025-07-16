<?php
include 'config.php';  

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
   
    $email = $_POST['email'];
    $password = $_POST['password'];

   
    $sql = "SELECT * FROM users WHERE email = '$email' and password = '$password'";
   $result = mysqli_query($conn, $sql);
   if (mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_array($result);
        session_start();
        $_SESSION['user'] = $row['username'];
        
        header("Location: mine.php");
        exit;
    } else {
        echo "البريد الإلكتروني أو كلمة المرور غير صحيحة.";
    }
   }

?>

<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <title>تسجيل الدخول</title>
    <link rel="stylesheet" href="css/Logsun.css">
</head>
<!-- login.php أو صفحة تسجيل الدخول -->
<body>
    <div class="login-container">
        <div class="login-box">
            <h2>تسجيل الدخول</h2>
            <form class="login-form" method="POST">
                <input type="email" name="email" placeholder="البريد الإلكتروني" required>
                <input type="password" name="password" placeholder="كلمة المرور" required>
                <button type="submit">دخول</button>
            </form>
            <div class="login-links">
                ليس لديك حساب؟ <a href="register.php">سجل الآن</a>
            
            </div>
        </div>
    </div>
</body>
</html>
