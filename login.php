<?php
include 'config.php';  

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
   
    $email = $_POST['email'];
    $password = $_POST['password'];

   
    $sql = "SELECT * FROM users WHERE email = '$email'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
     
        $row = $result->fetch_assoc();
        if (password_verify($password, $row['password'])) {
          header('Location: mine.php');
         
        } else {
            echo "كلمة المرور غير صحيحة!";
        }
    } else {
        echo "البريد الإلكتروني غير موجود!";
    }
}
?>

<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <title>تسجيل الدخول</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="form-container">
        <h2>تسجيل الدخول</h2>
        <form method="POST">
            <input type="email" name="email" placeholder="البريد الإلكتروني" required>
            <input type="password" name="password" placeholder="كلمة المرور" required>
            <button type="submit">دخول</button>
        </form>
        <p>ليس لديك حساب؟ <a href="register.php">سجل الآن</a></p> <!-- الرابط لصفحة التسجيل -->
    </div>
</body>
</html>
