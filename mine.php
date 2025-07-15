<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>متجر إلكتروني</title>
    <link rel="stylesheet" href="mine.css">

</head>
<body>
    <?php
    session_start();
    $cart_count = isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0;
    ?>
    <div style="position:fixed;top:25px;left:40px;z-index:1000;">
        <a href="cart.php" style="position:relative;display:inline-block;">
            <img src="img/cart.png" alt="السلة" style="width:40px;">
            <?php if($cart_count > 0): ?>
                <span style="
                    position:absolute;
                    top:-10px;left:-10px;
                    background:#e74c3c;
                    color:#fff;
                    border-radius:50%;
                    width:24px;height:24px;
                    display:flex;
                    align-items:center;
                    justify-content:center;
                    font-size:15px;
                    font-weight:bold;
                    border:2px solid #fff;
                "><?php echo $cart_count; ?></span>
            <?php endif; ?>
        </a>
    </div>

    <header>
        <h1>متجرنا الإلكتروني</h1>
    </header>

    <div class="cart" id="cart">img</div>

    <div class="container">
        <?php
       
        $servername = "localhost";
        $username = "root";
        $password = "";
        $dbname = "digital_store";
        
       
        $conn = new mysqli($servername, $username, $password, $dbname);
        
      
        if ($conn->connect_error) {
            die("فشل الاتصال: " . $conn->connect_error);
        }
        
        // استعلام SQL لسحب بيانات المنتجات
        $sql = "SELECT * FROM products WHERE is_active = 1";
        $result = $conn->query($sql);
                if ($result->num_rows > 0) {
            // عرض كل منتج
            while($row = $result->fetch_assoc()) {
                echo '<a href="details.php?id='.$row["id"].'" class="product-link">
                        <div class="product">
                            <img src="'.$row["file_path"].'" alt="'.$row["title"].'">
                            <h3>'.$row["title"].'</h3>
                            <p>'.$row["description"].'</p>
                            <div class="price">'.$row["price"].' ر.س</div>
                        </div>
                    </a>';
            }
        } else {
            echo "<div style='width:100%; text-align:center; padding:20px;'>لا توجد منتجات متاحة حالياً</div>";
        }
        

        
 
        $conn->close();
        ?>
    </div>

   
</body>
</html>