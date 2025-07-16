<?php
include 'config.php';

// التحقق من وجود معرف المنتج
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: mine.php?error=missing_id");
    exit;
}

// تنقية المدخلات
$id = intval($_GET['id']);
$sql = "SELECT * FROM products WHERE id = ? AND is_active = 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    // معالجة مسار الصورة
    $image_path = !empty($row['file_path']) ? $row['file_path'] : 'images/default-product.png';
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($row['title']); ?> - تفاصيل المنتج</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #3498db;
            --secondary-color: #2c3e50;
            --accent-color: #e74c3c;
            --light-color: #f8f9fa;
            --dark-color: #343a40;
            --success-color: #28a745;
            --border-radius: 8px;
            --box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            --transition: all 0.3s ease;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Tajawal', sans-serif;
            background-color: #f5f5f5;
            color: #333;
            line-height: 1.6;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .product-details {
            display: flex;
            flex-wrap: wrap;
            background: #fff;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            overflow: hidden;
            margin: 30px 0;
        }
        
        .product-gallery {
            flex: 1;
            min-width: 300px;
            padding: 20px;
            background: #f9f9f9;
            text-align: center;
        }
        
        .main-image {
            width: 100%;
            max-height: 400px;
            object-fit: contain;
            border-radius: var(--border-radius);
            margin-bottom: 15px;
            transition: var(--transition);
        }
        
        .thumbnail-container {
            display: flex;
            gap: 10px;
            justify-content: center;
            flex-wrap: wrap;
        }
        
        .thumbnail {
            width: 70px;
            height: 70px;
            object-fit: cover;
            border-radius: 5px;
            cursor: pointer;
            border: 2px solid transparent;
            transition: var(--transition);
        }
        
        .thumbnail:hover {
            border-color: var(--primary-color);
        }
        
        .product-info {
            flex: 1;
            min-width: 300px;
            padding: 30px;
            position: relative;
        }
        
        .product-title {
            font-size: 28px;
            margin-bottom: 10px;
            color: var(--dark-color);
        }
        
        .product-category {
            display: inline-block;
            background: #eee;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 14px;
            margin-bottom: 15px;
            color: #666;
        }
        
        .product-price {
            font-size: 28px;
            font-weight: bold;
            color: var(--accent-color);
            margin: 20px 0;
        }
        
        .old-price {
            text-decoration: line-through;
            color: #999;
            font-size: 18px;
            margin-left: 10px;
        }
        
        .product-description {
            margin: 25px 0;
            color: #555;
            line-height: 1.8;
        }
        
        .product-meta {
            margin: 20px 0;
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
        }
        
        .meta-item {
            display: flex;
            align-items: center;
            gap: 5px;
            color: #666;
        }
        
        .quantity-selector {
            display: flex;
            align-items: center;
            margin: 20px 0;
        }
        
        .quantity-btn {
            width: 40px;
            height: 40px;
            background: #f0f0f0;
            border: none;
            font-size: 18px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: var(--border-radius) 0 0 var(--border-radius);
        }
        
        .quantity-input {
            width: 60px;
            height: 40px;
            text-align: center;
            border: 1px solid #ddd;
            border-left: none;
            border-right: none;
        }
        
        .add-to-cart {
            background-color: var(--primary-color);
            color: white;
            border: none;
            padding: 12px 30px;
            font-size: 16px;
            border-radius: var(--border-radius);
            cursor: pointer;
            transition: var(--transition);
            display: flex;
            align-items: center;
            gap: 10px;
            margin: 20px 0;
        }
        
        .add-to-cart:hover {
            background-color: #2980b9;
            transform: translateY(-2px);
        }
        
        .wishlist-btn {
            background: none;
            border: 1px solid #ddd;
            padding: 10px 15px;
            border-radius: var(--border-radius);
            cursor: pointer;
            transition: var(--transition);
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .wishlist-btn:hover {
            background: #f8f8f8;
        }
        
        .back-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: var(--secondary-color);
            text-decoration: none;
            margin-top: 20px;
            padding: 8px 15px;
            border-radius: var(--border-radius);
            transition: var(--transition);
        }
        
        .back-btn:hover {
            background: #f0f0f0;
        }
        
        .badge {
            position: absolute;
            top: 20px;
            left: 20px;
            background: var(--accent-color);
            color: white;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 14px;
        }
        
        .social-share {
            display: flex;
            gap: 10px;
            margin: 20px 0;
        }
        
        .social-icon {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            background: #f0f0f0;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #555;
            transition: var(--transition);
        }
        
        .social-icon:hover {
            background: var(--primary-color);
            color: white;
        }
        
        .tabs {
            margin: 30px 0;
        }
        
        .tab-header {
            display: flex;
            border-bottom: 1px solid #ddd;
        }
        
        .tab-btn {
            padding: 10px 20px;
            background: none;
            border: none;
            cursor: pointer;
            border-bottom: 3px solid transparent;
            transition: var(--transition);
        }
        
        .tab-btn.active {
            border-bottom-color: var(--primary-color);
            color: var(--primary-color);
            font-weight: bold;
        }
        
        .tab-content {
            padding: 20px 0;
            display: none;
        }
        
        .tab-content.active {
            display: block;
        }
        
        .related-products {
            margin: 50px 0;
        }
        
        .section-title {
            font-size: 24px;
            margin-bottom: 20px;
            position: relative;
            padding-bottom: 10px;
        }
        
        .section-title::after {
            content: '';
            position: absolute;
            bottom: 0;
            right: 0;
            width: 50px;
            height: 3px;
            background: var(--primary-color);
        }
        
        @media (max-width: 768px) {
            .product-details {
                flex-direction: column;
            }
            
            .product-gallery, .product-info {
                padding: 15px;
            }
        }
        
        /* تحسينات للعرض على الهواتف */
        @media (max-width: 480px) {
            .product-title {
                font-size: 22px;
            }
            
            .product-price {
                font-size: 24px;
            }
            
            .add-to-cart {
                padding: 10px 20px;
                font-size: 14px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="product-details">
            <div class="product-gallery">
                <img src="<?php echo htmlspecialchars($image_path); ?>" alt="<?php echo htmlspecialchars($row['title']); ?>" class="main-image" id="mainImage">
                <div class="thumbnail-container">
                    <img src="<?php echo htmlspecialchars($image_path); ?>" alt="صورة مصغرة 1" class="thumbnail" onclick="changeImage(this)">
                    <!-- يمكن إضافة المزيد من الصور المصغرة هنا -->
                </div>
            </div>
            
            <div class="product-info">
                <?php
                
                //نقدر نضيغف الحصة إذا كان هناك خصم
                //if($row['discount'] > 0): ?>
                    <span class="badge">خصم <?php //echo $row['discount']; ?>%</span>
                <?php //endif; ?>
                
                <h1 class="product-title"><?php echo htmlspecialchars($row['title']); ?></h1>
                <span class="product-category"><?php echo htmlspecialchars($row['category'] ?? 'فئة عامة'); ?></span>
                
                <div class="product-price">
                    <?php 
                    $price = $row['price'];
                    if(isset($row['discount']) && $row['discount'] > 0) {
                        $discounted_price = $price - ($price * $row['discount'] / 100);
                        echo number_format($discounted_price, 2) . ' ر.س';
                        echo '<span class="old-price">' . number_format($price, 2) . ' ر.س</span>';
                    } else {
                        echo number_format($price, 2) . ' ر.س';
                    }
                    ?>
                </div>
                
                <div class="product-meta">
                    <div class="meta-item">
                        <i class="fas fa-check-circle" style="color: var(--success-color);"></i>
                        <span>متوفر في المخزن</span>
                    </div>
                    <div class="meta-item">
                        <i class="fas fa-truck"></i>
                        <span>شحن سريع</span>
                    </div>
                    <div class="meta-item">
                        <i class="fas fa-shield-alt"></i>
                        <span>ضمان العودة</span>
                    </div>
                </div>
                
                <p class="product-description"><?php echo nl2br(htmlspecialchars($row['description'])); ?></p>
                
                <form method="post" action="cart.php" class="cart-form">
                    <input type="hidden" name="product_id" value="<?php echo $row['id']; ?>">
                    
                    <div class="quantity-selector">
                        <button type="button" class="quantity-btn" onclick="decreaseQuantity()">-</button>
                        <input type="number" name="quantity" class="quantity-input" value="1" min="1" max="10">
                        <button type="button" class="quantity-btn" onclick="increaseQuantity()">+</button>
                    </div>
                    
                    <button type="submit" class="add-to-cart">
                        <i class="fas fa-shopping-cart"></i>
                        إضافة إلى السلة
                    </button>
                </form>
                
                <button class="wishlist-btn">
                    <i class="far fa-heart"></i>
                    إضافة إلى المفضلة
                </button>
                
                <div class="social-share">
                    <span>مشاركة:</span>
                    <a href="#" class="social-icon"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" class="social-icon"><i class="fab fa-twitter"></i></a>
                    <a href="#" class="social-icon"><i class="fab fa-whatsapp"></i></a>
                </div>
                
                <a href="mine.php" class="back-btn">
                    <i class="fas fa-arrow-right"></i>
                    العودة للمتجر
                </a>
            </div>
        </div>
        
        <div class="tabs">
            <div class="tab-header">
                <button class="tab-btn active" onclick="openTab('description')">الوصف</button>
                <button class="tab-btn" onclick="openTab('reviews')">التقييمات</button>
                <button class="tab-btn" onclick="openTab('shipping')">الشحن والتوصيل</button>
            </div>
            
            <div id="description" class="tab-content active">
                <h3>تفاصيل المنتج</h3>
                <p><?php echo nl2br(htmlspecialchars($row['description'])); ?></p>
                <!-- يمكن إضافة المزيد من التفاصيل هنا -->
            </div>
            
            <div id="reviews" class="tab-content">
                <h3>تقييمات العملاء</h3>
                <p>لا توجد تقييمات حتى الآن.</p>
                <!-- يمكن إضافة نظام التقييمات هنا -->
            </div>
            
            <div id="shipping" class="tab-content">
                <h3>سياسة الشحن والتوصيل</h3>
                <p>نقدم شحن سريع لجميع أنحاء المملكة خلال 2-5 أيام عمل. الشحن مجاني للطلبات فوق 200 ر.س.</p>
            </div>
        </div>
        
        <div class="related-products">
            <h2 class="section-title">منتجات ذات صلة</h2>
            <!-- يمكن إضافة منتجات ذات صلة هنا -->
            <div style="text-align: center; padding: 20px; background: #f9f9f9; border-radius: var(--border-radius);">
                <p>لا توجد منتجات ذات صلة معروضة حالياً</p>
            </div>
        </div>
    </div>
    
    <script>
        // تغيير الصورة الرئيسية عند النقر على الصورة المصغرة
        function changeImage(element) {
            document.getElementById('mainImage').src = element.src;
        }
        
        // إدارة الكمية
        function increaseQuantity() {
            const input = document.querySelector('.quantity-input');
            if(parseInt(input.value) < 10) {
                input.value = parseInt(input.value) + 1;
            }
        }
        
        function decreaseQuantity() {
            const input = document.querySelector('.quantity-input');
            if(parseInt(input.value) > 1) {
                input.value = parseInt(input.value) - 1;
            }
        }
        
        // إدارة التبويبات
        function openTab(tabId) {
            const tabContents = document.querySelectorAll('.tab-content');
            const tabButtons = document.querySelectorAll('.tab-btn');
            
            tabContents.forEach(content => {
                content.classList.remove('active');
            });
            
            tabButtons.forEach(button => {
                button.classList.remove('active');
            });
            
            document.getElementById(tabId).classList.add('active');
            event.currentTarget.classList.add('active');
        }
        
        // يمكن إضافة المزيد من الوظائف هنا
    </script>
</body>
</html>
<?php
} else {
    // إذا لم يتم العثور على المنتج
    echo '<div class="container" style="text-align: center; padding: 50px 20px;">
            <div style="background: white; padding: 30px; border-radius: var(--border-radius); box-shadow: var(--box-shadow);">
                <i class="fas fa-exclamation-circle" style="font-size: 50px; color: var(--accent-color); margin-bottom: 20px;"></i>
                <h2 style="margin-bottom: 15px;">المنتج غير موجود</h2>
                <p style="margin-bottom: 20px;">عذراً، المنتج الذي تبحث عنه غير متوفر أو تم إزالته.</p>
                <a href="mine.php" style="display: inline-block; padding: 10px 20px; background: var(--primary-color); color: white; text-decoration: none; border-radius: var(--border-radius);">
                    العودة إلى المتجر
                </a>
            </div>
          </div>';
}

$stmt->close();
$conn->close();
?>