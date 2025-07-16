<?php
session_start();
include 'config.php';
$cart_count = isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0;

// استعلام لعرض المنتجات المتاحة مع تحسين الأداء
$sql = "SELECT id, title, description, price, file_path FROM products WHERE is_active = 1";
$result = $conn->query($sql);
$products_exist = ($result->num_rows > 0);
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="متجر إلكتروني متخصص في بيع المنتجات عالية الجودة">
    <title>متجر إلكتروني - الصفحة الرئيسية</title>
    <link rel="stylesheet" href="css/mine.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;900&display=swap" rel="stylesheet">
</head>
<body>
    <header class="main-header">
        <div class="header-container">
            <div class="logo">
                <h1><a href="index.php">متجرنا الإلكتروني</a></h1>
            </div>
            
            <div class="top-icons">
                <div class="cart-icon-fixed">
                    <a href="cart.php" class="cart-link">
                        <i class="fas fa-shopping-cart"></i>
                        <?php if($cart_count > 0): ?>
                            <span class="cart-badge"><?php echo $cart_count; ?></span>
                        <?php endif; ?>
                    </a>
                </div>
                <div class="user-menu">
                    <button class="user-icon-btn" onclick="toggleUserMenu()">
                        <i class="fas fa-user-circle"></i>
                    </button>
                    <div id="userDropdown" class="user-dropdown">
                        <?php if(isset($_SESSION['user'])): ?>
                            <a href="notifications.php" class="dropdown-item"><i class="fas fa-bell"></i> الإشعارات</a>
                            <a href="orders.php" class="dropdown-item"><i class="fas fa-clipboard-list"></i> الطلبات</a>
                            <a href="pending_orders.php" class="dropdown-item"><i class="fas fa-clock"></i> طلبات بانتظار الدفع</a>
                            <a href="wishlist.php" class="dropdown-item"><i class="fas fa-heart"></i> المفضلات</a>
                            <a href="account.php" class="dropdown-item"><i class="fas fa-user"></i> حسابي</a>
                            <a href="logout.php" class="dropdown-item logout"><i class="fas fa-sign-out-alt"></i> تسجيل الخروج</a>
                        <?php else: ?>
                            <a href="login.php" class="dropdown-item"><i class="fas fa-sign-in-alt"></i> تسجيل الدخول</a>
                            <a href="register.php" class="dropdown-item"><i class="fas fa-user-plus"></i> إنشاء حساب</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        
        <nav class="main-nav">
            <ul>
                <li><a href="index.php" class="active">الرئيسية</a></li>
                <li><a href="products.php">المنتجات</a></li>
                <li><a href="categories.php">الأقسام</a></li>
                <li><a href="offers.php">العروض</a></li>
                <li><a href="about.php">عن المتجر</a></li>
                <li><a href="contact.php">اتصل بنا</a></li>
            </ul>
        </nav>
    </header>

    <main class="main-content">
        <?php if(isset($_GET['login_success'])): ?>
            <div class="alert alert-success">
                تم تسجيل الدخول بنجاح! مرحباً بعودتك.
            </div>
        <?php endif; ?>
        
        <section class="hero-section">
            <div class="hero-content">
                <h2>أحدث المنتجات بأفضل الأسعار</h2>
                <p>اكتشف تشكيلتنا الواسعة من المنتجات عالية الجودة</p>
                <a href="products.php" class="btn btn-primary">تسوق الآن</a>
            </div>
        </section>
        
        <section class="products-section">
            <h2 class="section-title">منتجاتنا</h2>
            
            <?php if($products_exist): ?>
                <div class="products-grid">
                    <?php while($row = $result->fetch_assoc()): ?>
                        <div class="product-card">
                            <a href="details.php?id=<?php echo $row["id"]; ?>" class="product-link">
                                <div class="product-image">
                                    <img src="<?php echo $row["file_path"]; ?>" alt="<?php echo htmlspecialchars($row["title"]); ?>" loading="lazy">
                                    <?php if($row["price"] < 100): ?>
                                        <!-- مكان الخص بعدين نضيف متغير الخصم -->
                                        <span class="product-badge">خصم</span>
                                    <?php endif; ?>
                                </div>
                                <div class="product-info">
                                    <h3><?php echo htmlspecialchars($row["title"]); ?></h3>
                                    <p class="product-desc"><?php echo substr(htmlspecialchars($row["description"]), 0, 60); ?>...</p>
                                    <div class="product-price">
                                        <span><?php echo number_format($row["price"], 2); ?> ر.س</span>
                                        <?php if($row["price"] < 100): ?>
                                            <span class="old-price">150 ر.س</span>
                                        <?php endif; ?>
                                    </div>
                                    <button class="btn btn-add-to-cart" onclick="event.preventDefault(); addToCart(<?php echo $row['id']; ?>)">
                                        <i class="fas fa-cart-plus"></i> أضف للسلة
                                    </button>
                                </div>
                            </a>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <div class="empty-products">
                    <i class="fas fa-box-open"></i>
                    <p>لا توجد منتجات متاحة حالياً</p>
                    <a href="contact.php" class="btn btn-outline">اتصل بنا لمعرفة المزيد</a>
                </div>
            <?php endif; ?>
        </section>
    </main>

    <footer class="main-footer">
        <div class="footer-container">
            <div class="footer-section">
                <h3>روابط سريعة</h3>
                <ul>
                    <li><a href="mine.php">الرئيسية</a></li>
                    <li><a href="products.php">المنتجات</a></li>
                    <li><a href="about.php">عن المتجر</a></li>
                    <li><a href="contact.php">اتصل بنا</a></li>
                </ul>
            </div>
            
            <div class="footer-section">
                <h3>خدمة العملاء</h3>
                <ul>
                    <li><a href="#">الأسئلة الشائعة</a></li>
                    <li><a href="#">الشحن والتوصيل</a></li>
                    <li><a href="#">سياسة الإرجاع</a></li>
                    <li><a href="#">سياسة الخصوصية</a></li>
                </ul>
            </div>
            
            <div class="footer-section">
                <h3>تواصل معنا</h3>
                <ul class="contact-info"> 
                    <li><i class="fas fa-phone"></i> رقم جوال المتجر</li>
                    <li><i class="fas fa-envelope"></i> @</li>
                    <li><i class="fas fa-map-marker-alt"></i>  الموقع</li>
                </ul>
                <div class="social-links">
                    <a href="#"><i class="fab fa-facebook-f"></i></a>
                    <a href="#"><i class="fab fa-twitter"></i></a>
                    <a href="#"><i class="fab fa-instagram"></i></a>
                    <a href="#"><i class="fab fa-snapchat-ghost"></i></a>
                </div>
            </div>
        </div>
        
        <div class="copyright">
            <p>&copy; <?php echo date('Y'); ?> جميع الحقوق محفوظة - متجرنا الإلكتروني</p>
        </div>
    </footer>

    <script>
    function toggleUserMenu() {
        document.getElementById('userDropdown').classList.toggle('show');
    }

    window.onclick = function(event) {
        if (!event.target.matches('.user-icon-btn') && !event.target.closest('.user-icon-btn')) {
            var dropdown = document.getElementById('userDropdown');
            if (dropdown.classList.contains('show')) {
                dropdown.classList.remove('show');
            }
        }
    }

    function addToCart(productId) {
        fetch('add_to_cart.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'product_id=' + productId
        })
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                // تحديث عدد العناصر في السلة
                const cartBadge = document.querySelector('.cart-badge');
                if(cartBadge) {
                    cartBadge.textContent = data.cart_count;
                } else {
                    const cartLink = document.querySelector('.cart-link');
                    cartLink.innerHTML += `<span class="cart-badge">${data.cart_count}</span>`;
                }
                
                // إظهار إشعار
                alert('تمت إضافة المنتج إلى سلة التسوق بنجاح!');
            } else {
                alert('حدث خطأ أثناء إضافة المنتج إلى السلة: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('حدث خطأ في الاتصال بالخادم');
        });
    }
    </script>
</body>
</html>