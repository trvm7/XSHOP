<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "digital_store";

if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "<div style='text-align:center; padding:30px;'>معرف المنتج غير موجود</div>";
    exit;
}

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("فشل الاتصال: " . $conn->connect_error);
}

$id = intval($_GET['id']);
$sql = "SELECT * FROM products WHERE id = $id AND is_active = 1";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    ?>
    <!DOCTYPE html>
    <html lang="ar" dir="rtl">
    <head>
        <meta charset="UTF-8">
        <title>تفاصيل المنتج</title>
        <link rel="stylesheet" href="mine.css">
        <style>
            .details-container {
                max-width: 500px;
                margin: 40px auto;
                background: #fff;
                padding: 30px;
                border-radius: 10px;
                box-shadow: 0 2px 10px #0001;
                text-align: center;
            }
            .details-container img {
                max-width: 100%;
                margin-bottom: 20px;
            }
            .details-container .price {
                color: #e74c3c;
                font-size: 1.3em;
                margin: 15px 0;
            }
            .back-btn {
                display: inline-block;
                margin-top: 20px;
                padding: 8px 20px;
                background: #2c3e50;
                color: #fff;
                border-radius: 5px;
                text-decoration: none;
            }
        </style>
    </head>
    <body>
        <div class="details-container">
            <img src="<?php echo $row['file_path']; ?>" alt="<?php echo $row['title']; ?>">
            <h2><?php echo $row['title']; ?></h2>
            <div class="price"><?php echo $row['price']; ?> ر.س</div>
            <p><?php echo $row['description']; ?></p>
            <form method="post" action="cart.php" style="margin-top:20px;">
    <input type="hidden" name="product_id" value="<?php echo $row['id']; ?>">
    <button type="submit" class="back-btn" style="background:#e67e22;">إضافة للسلة</button>
</form>
            <a href="mine.php" class="back-btn">العودة للمتجر</a>
        </div>
    </body>
    </html>
    <?php
} else {
    echo "<div style='text-align:center; padding:30px;'>المنتج غير موجود أو غير متاح</div>";
}
$conn->close();