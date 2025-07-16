<?php
session_start();
include 'config.php';

// معالجة طلبات السلة
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['product_id'])) {
        $id = intval($_POST['product_id']);
        
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }
        
        // حذف المنتج من السلة
        if (isset($_POST['remove_item'])) {
            unset($_SESSION['cart'][$id]);
            header("Location: cart.php");
            exit;
        }
        
        // تحديث الكمية
        if (isset($_POST['update_qty']) && isset($_POST['quantity'])) {
            $qty = max(1, min(99, intval($_POST['quantity'])));
            $_SESSION['cart'][$id] = $qty;
        } else {
            // إضافة منتج جديد للسلة
            $_SESSION['cart'][$id] = isset($_SESSION['cart'][$id]) ? $_SESSION['cart'][$id] + 1 : 1;
        }
        
        header("Location: cart.php");
        exit;
    }
}

// جلب بيانات المنتجات في السلة
$cart = $_SESSION['cart'] ?? [];
$products = [];
$total = 0;

if (!empty($cart)) {
    $ids = implode(',', array_map('intval', array_keys($cart)));
    $sql = "SELECT * FROM products WHERE id IN ($ids)";
    $result = $conn->query($sql);
    
    while($row = $result->fetch_assoc()) {
        $row['quantity'] = $cart[$row['id']];
        $row['subtotal'] = $row['price'] * $row['quantity'];
        $total += $row['subtotal'];
        $products[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>سلة المشتريات</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/cart.css">
</head>
<body>
    <div class="container">
        <div class="cart-header">
            <h1><i class="fas fa-shopping-cart"></i> سلة المشتريات</h1>
        </div>
        
        <?php if(isset($_SESSION['user'])): ?>
            <div class="cart-container">
                <?php if (empty($products)): ?>
                    <div class="empty-cart">
                        <i class="fas fa-shopping-basket"></i>
                        <p>سلة المشتريات فارغة</p>
                        <a href="mine.php" class="back-btn">
                            <i class="fas fa-arrow-right"></i> العودة للمتجر
                        </a>
                    </div>
                <?php else: ?>
                    <div class="cart-items">
                        <?php foreach ($products as $product): ?>
                            <div class="cart-item">
                                <img src="<?php echo htmlspecialchars($product['file_path']); ?>" alt="<?php echo htmlspecialchars($product['title']); ?>" class="item-image">
                                
                                <div class="item-info">
                                    <div class="item-title"><?php echo htmlspecialchars($product['title']); ?></div>
                                    <div class="item-description"><?php echo htmlspecialchars($product['description']); ?></div>
                                    <div class="item-price"><?php echo number_format($product['price'], 2); ?> ر.س</div>
                                </div>
                                
                                <form method="post" action="cart.php" class="quantity-control">
                                    <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                    <button type="button" class="quantity-btn" onclick="updateQuantity(<?php echo $product['id']; ?>, -1)">-</button>
                                    <input type="number" name="quantity" class="quantity-input" value="<?php echo $product['quantity']; ?>" min="1" max="99" onchange="submitQuantity(<?php echo $product['id']; ?>, this.value)">
                                    <button type="button" class="quantity-btn" onclick="updateQuantity(<?php echo $product['id']; ?>, 1)">+</button>
                                </form>
                                
                                <form method="post" action="cart.php" style="margin-right:15px;">
                                    <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                    <input type="hidden" name="remove_item" value="1">
                                    <button type="submit" class="remove-btn" title="إزالة من السلة">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <div class="cart-summary">
                        <div class="summary-row">
                            <span>عدد المنتجات:</span>
                            <span><?php echo count($products); ?></span>
                        </div>
                        <div class="summary-row">
                            <span>المجموع الجزئي:</span>
                            <span><?php echo number_format($total, 2); ?> ر.س</span>
                        </div>
                        <div class="summary-row">
                            
                            <span>تكلفة الشحن:</span>
                            <span>مجاناً</span>
                        </div>
                        <div class="summary-row">
                            <span>المجموع الكلي:</span>
                            <span style="color: var(--accent-color);"><?php echo number_format($total, 2); ?> ر.س</span>
                        </div>
                    </div>
                    
                    <a href="checkout.php" class="checkout-btn">
                        <i class="fas fa-credit-card"></i> إتمام الشراء
                    </a>
                    
                    <a href="mine.php" class="back-btn">
                        <i class="fas fa-arrow-right"></i> العودة للمتجر
                    </a>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <div class="login-required">
                <i class="fas fa-exclamation-circle"></i>
                <h2>يجب تسجيل الدخول لعرض سلة المشتريات</h2>
                <p>سجل الدخول لمشاهدة المنتجات التي أضفتها إلى سلة التسوق</p>
                <a href="login.php">
                    <i class="fas fa-sign-in-alt"></i> تسجيل الدخول
                </a>
            </div>
        <?php endif; ?>
    </div>
    
    <script>
        function updateQuantity(productId, change) {
            const input = document.querySelector(`input[name="quantity"][onchange*="${productId}"]`);
            let newValue = parseInt(input.value) + change;
            
            if (newValue < 1) newValue = 1;
            if (newValue > 99) newValue = 99;
            
            input.value = newValue;
            submitQuantity(productId, newValue);
        }
        
        function submitQuantity(productId, quantity) {
            const form = document.querySelector(`form[action="cart.php"] input[value="${productId}"]`).parentElement;
            const hiddenInput = document.createElement('input');
            hiddenInput.type = 'hidden';
            hiddenInput.name = 'update_qty';
            hiddenInput.value = '1';
            form.appendChild(hiddenInput);
            form.submit();
        }
    </script>
</body>
</html>