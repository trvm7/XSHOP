<?php
// اتصال بقاعدة البيانات
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "digital_store";

// إنشاء الاتصال
$conn = new mysqli($servername, $username, $password, $dbname);

// التحقق من الاتصال
if ($conn->connect_error) {
    die("فشل الاتصال: " . $conn->connect_error);
}

// إنشاء المجلدات إذا لم تكن موجودة مع صلاحيات الكتابة
$upload_dir = __DIR__ . '/uploads/';
$thumbnail_dir = __DIR__ . '/thumbnails/';

if (!file_exists($upload_dir)) {
    mkdir($upload_dir, 0755, true);
}
if (!file_exists($thumbnail_dir)) {
    mkdir($thumbnail_dir, 0755, true);
}

// معالجة إرسال النموذج
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // استقبال البيانات الأساسية
    $title = $conn->real_escape_string($_POST['title']);
    $description = $conn->real_escape_string($_POST['description']);
    $price = floatval($_POST['price']);
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    
    // معالجة تحميل الصورة الرئيسية
    $file_path = '';
    if (isset($_FILES['img']) && $_FILES['img']['error'] == UPLOAD_ERR_OK) {
        $file_name = preg_replace('/[^a-zA-Z0-9\._-]/', '', $_FILES["img"]["name"]);
        $file_name = uniqid() . '_' . $file_name;
        $target_file = $upload_dir . $file_name;
        
        // التحقق من أن الملف صورة
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
        $max_file_size = 5 * 1024 * 1024; // 5MB
        
        if (in_array($imageFileType, $allowed_types)) {
            if ($_FILES["img"]["size"] <= $max_file_size) {
                if (move_uploaded_file($_FILES["img"]["tmp_name"], $target_file)) {
                    $file_path = 'uploads/' . $file_name;
                } else {
                    echo "<div class='error'>خطأ في رفع الصورة الرئيسية. تأكد من صلاحيات المجلد.</div>";
                }
            } else {
                echo "<div class='error'>حجم الصورة كبير جداً (الحد الأقصى 5MB)</div>";
            }
        } else {
            echo "<div class='error'>نوع الملف غير مسموح به للصورة الرئيسية (jpg, jpeg, png, gif فقط)</div>";
        }
    }
    
    // معالجة تحميل الصورة المصغرة
    $thumbnail = '';
    if (isset($_FILES['thumbnail']) && $_FILES['thumbnail']['error'] == UPLOAD_ERR_OK) {
        $file_name = preg_replace('/[^a-zA-Z0-9\._-]/', '', $_FILES["thumbnail"]["name"]);
        $file_name = uniqid() . '_' . $file_name;
        $target_file = $thumbnail_dir . $file_name;
        
        // التحقق من أن الملف صورة
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
        
        if (in_array($imageFileType, $allowed_types)) {
            if ($_FILES["thumbnail"]["size"] <= $max_file_size) {
                if (move_uploaded_file($_FILES["thumbnail"]["tmp_name"], $target_file)) {
                    $thumbnail = 'thumbnails/' . $file_name;
                } else {
                    echo "<div class='error'>خطأ في رفع الصورة المصغرة. تأكد من صلاحيات المجلد.</div>";
                }
            } else {
                echo "<div class='error'>حجم الصورة المصغرة كبير جداً (الحد الأقصى 5MB)</div>";
            }
        } else {
            echo "<div class='error'>نوع الملف غير مسموح به للصورة المصغرة (jpg, jpeg, png, gif فقط)</div>";
        }
    }
    
    // إدراج البيانات في قاعدة البيانات
    if (!empty($file_path)) {
        $stmt = $conn->prepare("INSERT INTO products (title, description, price, file_path, thumbnail, created_at, is_active) VALUES (?, ?, ?, ?, ?, NOW(), ?)");
        $stmt->bind_param("ssdssi", $title, $description, $price, $file_path, $thumbnail, $is_active);
        
        if ($stmt->execute()) {
            echo "<div class='success'>تمت إضافة المنتج بنجاح!</div>";
        } else {
            echo "<div class='error'>خطأ في قاعدة البيانات: " . $stmt->error . "</div>";
        }
        $stmt->close();
    } else {
        echo "<div class='error'>يجب رفع صورة رئيسية للمنتج</div>";
    }
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إضافة منتج جديد</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #333;
            text-align: center;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        input[type="text"],
        input[type="number"],
        textarea {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }
        textarea {
            height: 100px;
        }
        .checkbox-group {
            display: flex;
            align-items: center;
        }
        .checkbox-group input {
            margin-left: 10px;
        }
        button {
            background-color: #4CAF50;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }
        button:hover {
            background-color: #45a049;
        }
        .success {
            color: green;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid green;
            border-radius: 4px;
            background-color: #e6ffe6;
        }
        .error {
            color: red;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid red;
            border-radius: 4px;
            background-color: #ffebeb;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>إضافة منتج جديد</h1>
        
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label for="title">اسم المنتج:</label>
                <input type="text" name="title" id="title" required>
            </div>
            
            <div class="form-group">
                <label for="description">وصف المنتج:</label>
                <textarea name="description" id="description" required></textarea>
            </div>
            
            <div class="form-group">
                <label for="price">السعر:</label>
                <input type="number" name="price" id="price" step="0.01" min="0" required>
            </div>
            
            <div class="form-group">
                <label for="img">صورة المنتج الرئيسية:</label>
                <input type="file" name="img" id="img" accept="image/*" required>
            </div>
            
            <div class="form-group">
                <label for="thumbnail">الصورة المصغرة:</label>
                <input type="file" name="thumbnail" id="thumbnail" accept="image/*">
            </div>
            
            <div class="form-group checkbox-group">
                <label for="is_active">مفعل:</label>
                <input type="checkbox" name="is_active" id="is_active" checked>
            </div>
            
            <button type="submit">حفظ المنتج</button>
        </form>
    </div>
</body>
</html>
<?php
$conn->close();
?>