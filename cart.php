<?php
session_start();

// استقبال الإضافة للسلة
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id'])) {
    $id = intval($_POST['product_id']);
    if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];
    if (!in_array($id, $_SESSION['cart'])) {
        $_SESSION['cart'][] = $id;
    }
    header("Location: cart.php");
    exit;
}

// جلب المنتجات من قاعدة البيانات
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "digital_store";
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("فشل الاتصال: " . $conn->connect_error);
}

// عرض المنتجات في السلة
$cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
$products = [];
if (!empty($cart)) {
    $ids = implode(',', array_map('intval', $cart));
    $sql = "SELECT * FROM products WHERE id IN ($ids)";
    $result = $conn->query($sql);
    while($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>سلة المشتريات</title>
    <link rel="stylesheet" href="mine.css">
    <style>
        .cart-container {
            max-width: 700px;
            margin: 40px auto;
            background: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px #0001;
        }
        .cart-item {
            display: flex;
            align-items: center;
            border-bottom: 1px solid #eee;
            padding: 18px 0;
        }
        .cart-item img {
            width: 90px;
            border-radius: 8px;
            margin-left: 20px;
        }
        .cart-item .info {
            flex: 1;
        }
        .cart-item .price {
            color: #e74c3c;
            font-weight: bold;
            font-size: 1.1em;
        }
        .empty-cart {
            text-align: center;
            color: #888;
            padding: 40px 0;
        }
        .back-btn {
            display: inline-block;
            margin-top: 30px;
            padding: 8px 20px;
            background: #2c3e50;
            color: #fff;
            border-radius: 5px;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="cart-container">
        <h2>سلة المشتريات</h2>
        <?php if (empty($products)): ?>
            <div class="empty-cart">السلة فارغة</div>
        <?php else: ?>
            <?php foreach ($products as $product): ?>
                <div class="cart-item">
                    <img src="<?php echo $product['file_path']; ?>" alt="<?php echo $product['title']; ?>">
                    <div class="info">
                        <div><strong><?php echo $product['title']; ?></strong></div>
                        <div><?php echo $product['description']; ?></div>
                    </div>
                    <div class="price"><?php echo $product['price']; ?> ر.س</div>
                </div>
            <?php endforeach; ?>
            <?php 
            $total = 0;
            foreach ($products as $product) {
                $total += $product['price'];
            }
            ?>
            <?php if (!empty($products)): ?>
                <div style="text-align:left; font-size:1.2em; margin-top:20px;">
                    <strong>المجموع الكلي:</strong>
                    <span style="color:#e74c3c;"><?php echo number_format($total, 2); ?> ر.س</span>
                </div>
            <?php endif; ?>
        <?php endif; ?>
        <a href="mine.php" class="back-btn">العودة للمتجر</a>
    </div>
</body>
</html>