<?php
include './config.php';
session_start();
error_reporting(E_ALL & ~E_NOTICE);

$user_id = $_SESSION['user_id'];

if (!isset($user_id)) {
    header('location:../login/login.php');
    exit();
}

// Kiểm tra xem product_id có được truyền vào không
if (isset($_GET['product_id'])) {
    $product_id = $_GET['product_id'];

    // Lấy thông tin sản phẩm theo product_id
    $select_product = mysqli_query($conn, "SELECT * FROM products WHERE product_id = '$product_id'") or die('query failed');

    if (mysqli_num_rows($select_product) > 0) {
        $fetch_product = mysqli_fetch_assoc($select_product);
    } else {
        // Nếu không tìm thấy sản phẩm, chuyển hướng về trang cửa hàng của tôi
        header('location: myshop.php');
        exit();
    }
} else {
    header('location: myshop.php');
    exit();
}

// Xử lý cập nhật sản phẩm
if (isset($_POST['submit'])) {
    $errors = [];

    $product_name = mysqli_real_escape_string($conn, $_POST['product_name']);
    $price = mysqli_real_escape_string($conn, $_POST['price']);
    $quantity = $_POST['quantity'];
    $image = $_FILES['image']['name'];
    $image_tmp_name = $_FILES['image']['tmp_name'];
    $image_folder = 'upload_image/' . $image;
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $time_used = mysqli_real_escape_string($conn, $_POST['time_used']);
    $place_of_purchase = mysqli_real_escape_string($conn, $_POST['place_of_purchase']);
    $purchase_price = mysqli_real_escape_string($conn, $_POST['purchase_price']);
    $warranty_period = mysqli_real_escape_string($conn, $_POST['warranty_period']);

    // Kiểm tra điều kiện
    if (empty($product_name)) {
        $errors['product_name'] = "Vui lòng nhập tên sản phẩm!";
    }

    if (empty($price)) {
        $errors['price'] = "Vui lòng nhập giá sản phẩm!";
    } elseif ($price < 1000) {
        $errors['price'] = "Giá sản phẩm phải ít nhất 1.000 VNĐ!";
    }

    if (empty($quantity)) {
        $errors['quantity'] = "Vui lòng nhập số lượng sản phẩm!";
    } elseif ($quantity < 1) {
        $errors['quantity'] = "Số lượng sản phẩm ít nhất là 1!";
    }

    if (empty($description)) {
        $errors['description'] = "Vui lòng nhập mô tả chi tiết của sản phẩm!";
    }

    // Kiểm tra hạn sử dụng
    if (!empty($warranty_period)) {
        $current_date = date('Y-m-d'); // Lấy ngày hiện tại
        if ($warranty_period < $current_date) {
            $errors['warranty_period'] = "Hạn sử dụng không hợp lệ!";
        }
    }

    if (empty($place_of_purchase)) {
        $errors['place_of_purchase'] = "Vui lòng nhập địa chỉ mua sản phẩm!";
    }

    if (empty($purchase_price)) {
        $errors['purchase_price'] = "Vui lòng nhập giá khi mua sản phẩm!";
    } elseif ($purchase_price < 1000) {
        $errors['purchase_price'] = "Giá sản phẩm phải ít nhất 1.000 VNĐ!";
    }

    if (!empty($warranty_period)) {
        $current_date = date('Y-m-d'); // Lấy ngày hiện tại
        if ($warranty_period < $current_date) {
            $errors['warranty_period'] = "Hạn sử dụng không hợp lệ!";
        }
    }

    // If no errors, proceed with updating
    if (empty($errors)) {
        // Handle image upload
        if (!empty($image)) {
            move_uploaded_file($image_tmp_name, $image_folder);
        } else {
            $image = $fetch_product['product_image']; // Keep the current image if not updated
        }

        // Use prepared statements to update the product
        $stmt = mysqli_prepare($conn, "UPDATE products SET product_name = ?, description = ?, price = ?, quantity = ?, product_image = ?, time_used = ?, place_of_purchase = ?, purchase_price = ?, warranty_period = ? WHERE product_id = ?");
        mysqli_stmt_bind_param($stmt, 'ssdisssssi', $product_name, $description, $price, $quantity, $image, $time_used, $place_of_purchase, $purchase_price, $warranty_period, $product_id);

        if (mysqli_stmt_execute($stmt)) {
            $message = 'Cập nhật thông tin sản phẩm thành công!';
            echo "<script type='text/javascript'>
                window.alert('$message');
                window.location.href = 'detail_myproduct.php?id=$product_id'; // Chuyển hướng về trang thông tin chi tiết sản phẩm
            </script>";
        } else {
            $message = 'Cập nhật không thành công, vui lòng thử lại!';
            echo "<script type='text/javascript'>
                window.alert('$message');
            </script>";
        }
        mysqli_stmt_close($stmt);
    }
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chỉnh sửa sản phẩm</title>
    <link rel="stylesheet" href="./css/header.css">
    <link rel="stylesheet" href="./css/footer.css">
    <!-- Fontawesome css -->
    <link rel="stylesheet" href="./icon/fontawesome-free-6.6.0-web/css/all.min.css">
    <link rel="stylesheet" href="./icon/fontawesome-free-6.6.0-web/css/brands.min.css">
    <link rel="stylesheet" href="./icon/fontawesome-free-6.6.0-web/css/fontawesome.min.css">
    <!-- Fontawesome js -->
    <link rel="stylesheet" href="./icon/fontawesome-free-6.6.0-web/js/all.min.js">
    <link rel="stylesheet" href="./icon/fontawesome-free-6.6.0-web/js/brands.min.js">
    <link rel="stylesheet" href="./icon/fontawesome-free-6.6.0-web/js/fontawesome.min.js">
    <style>
        .main_content {
            width: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            padding: 10px;
        }

        .form_info_product {
            width: 60%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .group {
            width: 100%;
            display: flex;
            padding: 5px;
            flex-direction: column;
        }

        .group .group_title {
            width: 100%;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: flex-start;
            padding-left: 5px;
            background-color: lightyellow;
            border: none;
            border-radius: 5px;
        }

        .group_content {
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            border: none;
            border-radius: 5px;
            flex-direction: column;
        }

        .group_content select {
            width: 100%;
            height: 100%;
            border-radius: 5px;
            border: 1px solid orange;
            margin: 10px 0;
            padding: 10px;
        }

        .group_content .input_group {
            width: 95%;
            border-radius: 5px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            margin-top: 10px;
        }

        .input_group label {
            width: 100%;
            height: 30px;
            display: flex;
            align-items: center;
            color: gray;
        }

        .input_group input {
            width: 100%;
            height: 35px;
            border: 1px solid orange;
            border-radius: 5px;
            padding: 10px;
        }

        .input_group textarea {
            width: 100%;
            height: 100px;
            padding: 10px;
            font-family: Arial, Helvetica, sans-serif;
            border-radius: 5px;
        }

        .input_group p {
            color: black;
            font-size: 15px;
            margin-top: 10px;
            text-align: left;
        }

        .upload_box {
            width: 100%;
            height: 80px;
            border: 2px dashed #4caf50;
            border-radius: 5px;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            cursor: pointer;
            transition: background-color 0.3s;
            position: relative;
        }

        .upload_box:hover {
            background-color: rgba(76, 175, 80, 0.1);
        }

        .upload_icon {
            font-size: 30px;
            color: #4caf50;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }

        .upload_box input[type="file"] {
            width: 100%;
            height: 100%;
            position: absolute;
            top: 0;
            left: 0;
            opacity: 0;
            cursor: pointer;
        }

        .btn_submit {
            width: 120px;
            height: 35px;
            border: 1px solid orange;
            border-radius: 5px;
            background-color: orange;
            color: white;
            font-size: 15px;
            margin-top: 10px;
            cursor: pointer;
            transition: background-color 0.3s, transform 0.2s;
        }

        .btn_submit:hover {
            background-color: orangered;
            color: white;
            transform: scale(1.05);
        }

        .errors {
            width: 100%;
            display: flex;
            align-items: center;
            margin-top: 10px;
        }

        .error {
            color: red;

        }
    </style>
</head>

<body>
    <?php include 'header.php' ?>

    <main>
        <form method="POST" enctype="multipart/form-data" class="main_content">

            <div class="form_info_product">
                <h4 style="text-align: center; color: orangered">Cập nhật thông tin sản phẩm</h4>
                <div class="group">
                    <div class="group_content">
                        <div class="input_group">
                            <label for="product_name" style="color: gray">Tên sản phẩm</label>
                            <input name="product_name" type="text" id="product_name"
                                value="<?php echo $fetch_product['product_name']; ?>">
                            <div class="errors">
                                <?php if (isset($errors['product_name'])): ?>
                                    <div class="error"><?php echo $errors['product_name']; ?></div>
                                <?php endif; ?>
                            </div>
                        </div>


                        <div class="input_group">
                            <label for="price" style="color: gray">Giá sản phẩm</label>
                            <input name="price" type="number" id="price" value="<?php echo $fetch_product['price']; ?>"
                                min="1">
                            <div class="errors">
                                <?php if (isset($errors['price'])): ?>
                                    <div class="error"><?php echo $errors['price']; ?></div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="input_group">
                            <label for="quantity" style="color: gray">Số lượng</label>
                            <input name="quantity" type="number" id="quantity"
                                value="<?php echo $fetch_product['quantity']; ?>" min="1">
                            <div class="errors">
                                <?php if (isset($errors['quantity'])): ?>
                                    <div class="error"><?php echo $errors['quantity']; ?></div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="input_group">
                            <label for="description" style="color: gray">Mô tả chi tiết sản phẩm</label>
                            <textarea name="description" id="description" maxlength="2000"
                                required><?php echo $fetch_product['description']; ?></textarea>
                            <div class="errors">
                                <?php if (isset($errors['description'])): ?>
                                    <div class="error"><?php echo $errors['description']; ?></div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="input_group">
                            <label for="image" style="color: gray">Hình ảnh sản phẩm</label>
                            <div class="upload_box">
                                <div class="upload_icon">+</div>
                                <input id="imageUpload" name="image" accept="image/jpg, image/jpeg, image/png"
                                    type="file" onchange="previewImage(event)">
                            </div>
                            <p class="note">Nhấp vào ô phía dưới để tải lên hình ảnh mới (nếu có).</p>

                            <div id="fileInfo" style="margin-top: 10px;">
                                <?php
                                // Hiển thị hình ảnh hiện tại nếu có
                                $product_image = $fetch_product['product_image'];
                                if ($product_image && file_exists('./upload_image/' . $product_image)) {
                                    echo '<img src="./upload_image/' . htmlspecialchars($product_image) . '" alt="Product Image" style="max-width: 200px; max-height: 200px; margin-top: 10px;">';
                                } else {
                                    echo '<span>Chưa có hình ảnh sản phẩm</span>';
                                }
                                ?>
                            </div>
                        </div>


                        <div class="input_group">
                            <label for="time_used">Bạn đã sử dụng sản phẩm này bao lâu?</label>
                            <select name="time_used" id="">
                                <option value="">Dưới 1 tháng</option>
                                <option value="">Từ 1 -3 tháng</option>
                                <option value="">Từ 4-7 tháng</option>
                                <option value="">Từ 8 - 12 tháng</option>
                                <option value="">Khoảng 1 năm</option>
                                <option value="">Trên 3 năm</option>
                            </select>
                        </div>

                        <div class="input_group">
                            <label for="place_of_purchase" style="color: gray">Địa chỉ mua sản phẩm</label>
                            <input name="place_of_purchase" type="text" id="place_of_purchase"
                                value="<?php echo $fetch_product['place_of_purchase']; ?>">
                            <div class="errors">
                                <?php if (isset($errors['place_of_purchase'])): ?>
                                    <div class="error"><?php echo $errors['place_of_purchase']; ?></div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="input_group">
                            <label for="purchase_price">Giá khi mua sản phẩm</label>
                            <input name="purchase_price" type="text" id="purchase_price"
                                value="<?php echo $fetch_product['purchase_price']; ?>">
                            <div class="errors">
                                <?php if (isset($errors['purchase_price'])): ?>
                                    <div class="error"><?php echo $errors['purchase_price']; ?></div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="input_group">
                            <label for="warranty_period" style="color: gray">Hạn sử dụng sản phẩm (nếu có)</label>
                            <input name="warranty_period" type="date" id="warranty_period"
                                value="<?php echo $fetch_product['warranty_period']; ?>">
                            <div class="errors">
                                <?php if (isset($errors['warranty_period'])): ?>
                                    <div class="error"><?php echo $errors['warranty_period']; ?></div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="input_group">
                            <label for="category" style="color: gray">Danh mục sản phẩm</label>
                            <select name="category" id="category">
                                <?php
                                $select_category = mysqli_query($conn, "SELECT * FROM categories");
                                while ($fetch_category = mysqli_fetch_assoc($select_category)) {
                                    $selected = ($fetch_category['category_id'] == $fetch_product['category_id']) ? 'selected' : '';
                                    echo "<option value='" . $fetch_category['category_id'] . "' $selected>" . $fetch_category['category_name'] . "</option>";
                                }
                                ?>
                            </select>
                        </div>

                        <input type="submit" class="btn_submit" name="submit" value="Cập nhật">
                    </div>
                </div>
            </div>
        </form>
    </main>
    <?php include 'footer.php' ?>
</body>
<script>
    // Hàm để xem trước hình ảnh khi người dùng chọn file
    function previewImage(event) {
        const file = event.target.files[0];
        const fileInfo = document.getElementById("fileInfo");
        const reader = new FileReader();

        reader.onload = function () {
            // Hiển thị hình ảnh được chọn
            fileInfo.innerHTML = '<img src="' + reader.result + '" alt="Preview" style="max-width: 200px; max-height: 200px; margin-top: 10px;">';
        }

        if (file) {
            reader.readAsDataURL(file);
        }
    }
</script>

</html>