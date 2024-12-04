<?php
include './config.php';
session_start();
error_reporting(E_ALL & ~E_NOTICE);

if (isset($_POST['submit'])) {
    $product_name = mysqli_real_escape_string($conn, $_POST['product_name']);
    $price = $_POST['price'];
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $quantity = $_POST['quantity'];
    $time_used = $_POST['time_used'];
    $created_time = date('Y-m-d H:i:s');
    $image = $_FILES['image']['name'];
    $image_tmp_name = $_FILES['image']['tmp_name'];
    $image_folder = 'upload_image/' . $image;
    $warranty_period = $_POST['warranty_period'];
    $place_of_purchase = $_POST['place_of_purchase'];
    $purchase_price = $_POST['purchase_price'];
    $seller_id = $_SESSION['user_id'];
    $category_id = $_POST['category'];

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
    if (empty($image)) {
        $errors['image'] = "Vui lòng thêm hình ảnh sản phẩm!";
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

    if (empty($errors)) {
        $add_product = mysqli_query($conn, "INSERT INTO products (
            product_name, price, description, quantity, time_used, created_time, product_image, warranty_period,
            place_of_purchase, purchase_price, seller_id, category_id
        ) VALUES ('$product_name', '$price', '$description', '$quantity', '$time_used', '$created_time', '$image', '$warranty_period', '$place_of_purchase', '$purchase_price', '$seller_id', '$category_id')");

        if ($add_product) {
            move_uploaded_file($image_tmp_name, $image_folder);
            echo "<script type='text/javascript'>
                window.alert('Đăng sản phẩm thành công, vui lòng chờ nhân viên duyệt sản phẩm của bạn.');
            </script>";
        } else {
            echo "<script type='text/javascript'>
                window.alert('Đăng sản phẩm không thành công. Lỗi: " . mysqli_error($conn) . "');
            </script>";
        }
    }

}

?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng sản phẩm</title>
    <link rel="stylesheet" href="./css/main_new.css">
    <link rel="stylesheet" href="./css/header.css">
    <link rel="stylesheet" href="./css/add_product.css">

    <link rel="stylesheet" href="./icon/fontawesome-free-6.6.0-web/css/all.min.css">

    <style>
        .errors {
            width: 100%;
            display: flex;
            align-items: center;
            margin-top: 10px;
        }

        .error {
            color: red;

        }

        #previewContainer {
            position: relative;
            width: 200px;
            height: 250px;
            border: 2px dashed #ccc;
            margin-top: 10px;
            display: flex;
            justify-content: center;
            align-items: center;
            overflow: hidden;
            background-color: #f9f9f9;
        }

        #previewContainer img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .deleteImageBtn {
            position: absolute;
            top: 5px;
            right: 5px;
            background-color: red;
            color: white;
            border: none;
            border-radius: 50%;
            width: 25px;
            height: 25px;
            display: flex;
            justify-content: center;
            align-items: center;
            cursor: pointer;
            font-weight: bold;
        }
    </style>
</head>

<body>

    <?php include 'header.php' ?>

    <form method="POST" enctype="multipart/form-data" class="main_content">
        <div class="title_post_product">
            <span>Đăng thông tin sản phẩm</span>
        </div>


        <div class="form_post_product">
            <!-- CHỌN DANH MỤC SẢN PHẨM -->

            <div class="group">
                <div class="group_title">
                    <span>Chọn danh mục sản phẩm</span>
                </div>
                <div class="group_content">
                    <select name="category">
                        <?php
                        $select_category = mysqli_query($conn, "select * from categories");

                        if (mysqli_num_rows($select_category) > 0) {
                            while ($fetch_category = mysqli_fetch_assoc($select_category)) {
                                echo "<option value='" . $fetch_category['category_id'] . "'>" . $fetch_category['category_name'] . "</option>";
                            }
                        } else {
                            echo "<option>Không có danh mục nào.</option>";
                        }
                        ?>
                    </select>
                </div>
            </div>

            <!-- NHẬP THÔNG TIN SẢN PHẨM -->
            <div class="group">
                <div class="group_title">
                    <span>Nhập thông tin sản phẩm cần bán</span>
                </div>
                <div class="group_content">
                    <div class="input_group">
                        <label for="tieude">Tên sản phẩm</label>
                        <input name="product_name" type="text" id="tieude" placeholder="Nhập tên sản phẩm của bạn"
                            maxlength="70">
                        <div class="errors">
                            <?php if (isset($errors['product_name'])): ?>
                                <div class="error"><?php echo $errors['product_name']; ?></div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="input_group">
                        <label for="tieude">Giá sản phẩm</label>
                        <input name="price" type="number" id="tieude" placeholder="Nhập giá sản phẩm bạn muốn bán">
                        <div class="errors">
                            <?php if (isset($errors['price'])): ?>
                                <div class="error"><?php echo $errors['price']; ?></div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="input_group">
                        <label for="tieude">Số lượng</label>
                        <input name="quantity" type="number" id="tieude" placeholder="Nhập số lượng sản phẩm cần bán"
                            min="1">
                        <div class="errors">
                            <?php if (isset($errors['quantity'])): ?>
                                <div class="error"><?php echo $errors['quantity']; ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="input_group">
                        <label for="mota">Mô tả chi tiết sản phẩm</label>
                        <textarea name="description" id="mota" placeholder="Nhập thông tin mô tả sản phẩm của bạn"
                            maxlength="2000" rows="5"></textarea>
                        <div class="errors">
                            <?php if (isset($errors['description'])): ?>
                                <div class="error"><?php echo $errors['description']; ?></div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="input_group">
                        <label for="hinhanh">Hình ảnh</label>
                        <input id="imageUpload" name="image" accept="image/*" type="file">
                        <p class="note">Nhấp vào ô phía trên để tải lên hình ảnh sản phẩm của bạn.</p>
                        <div id="previewContainer">
                            <!-- Hình ảnh xem trước sẽ được thêm vào đây -->
                            <button class="deleteImageBtn" id="deleteImageBtn" style="display: none;">&times;</button>
                        </div>
                        <div class="errors">
                            <?php if (isset($errors['image'])): ?>
                                <div class="error"><?php echo $errors['image']; ?></div>
                            <?php endif; ?>
                        </div>
                    </div>


                </div>
            </div>

            <div class="group">
                <div class="group_title">
                    <span>Các thông tin liên quan khác</span>
                </div>
                <div class="group_content">
                    <div class="input_group">
                        <label for="time_used">Bạn đã sử dụng sản phẩm này bao lâu?</label>
                        <select name="time_used" id="">
                            <option value="Dưới 1 tháng">Dưới 1 tháng</option>
                            <option value="Từ 1 - 3 tháng">Từ 1 - 3 tháng</option>
                            <option value="Từ 4-7 tháng">Từ 4-7 tháng</option>
                            <option value="Từ 8 - 12 tháng">Từ 8 - 12 tháng</option>
                            <option value="Khoảng 1 năm">Khoảng 1 năm</option>
                            <option value="Trên 3 năm">Trên 3 năm</option>
                        </select>
                    </div>
                    <div class="input_group">
                        <label for="place_of_purchase">Bạn mua sản phẩm này ở đâu?</label>
                        <input name="place_of_purchase" type="text" placeholder="Nhập địa chỉ.">
                        <div class="errors">
                            <?php if (isset($errors['place_of_purchase'])): ?>
                                <div class="error"><?php echo $errors['place_of_purchase']; ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="input_group">
                        <label for="purchase_price">Giá khi mua của sản phẩm này?</label>
                        <input name="purchase_price" type="number" placeholder="Nhập giá khi mua.">
                        <div class="errors">
                            <?php if (isset($errors['purchase_price'])): ?>
                                <div class="error"><?php echo $errors['purchase_price']; ?></div>
                            <?php endif; ?>
                        </div>
                        
                    </div>

                    <div class="input_group">
                        <label for="warranty_period">Hạn sử dụng của sản phẩm? (Nếu có)</label>
                        <input name="warranty_period" type="date">
                        <div class="errors">
                            <?php if (isset($errors['warranty_period'])): ?>
                                <div class="error"><?php echo $errors['warranty_period']; ?></div>
                            <?php endif; ?>
                        </div>
                    </div>


                </div>
            </div>

            <!-- Nhập thông tin liên hệ -->
            <div class="group">
                <div class="group_title">
                    <span>Thông tin liên hệ</span>
                </div>

                <?php
                $select_user = mysqli_query($conn, "select * from users where user_id = '$user_id'");
                $fetch_user = mysqli_fetch_assoc($select_user);

                ?>

                <div class="group_content">

                    <div class="input_group">
                        <label for="dienthoai">Điện thoại</label>
                        <input type="text" placeholder="Điện thoại" value="<?php echo $fetch_user['phone_number'] ?>"
                            disabled>
                    </div>
                    <div class="input_group">
                        <label for="nguoilienhe">Người liên hệ</label>
                        <input type="text" placeholder="Người liên hệ" value="<?php echo $fetch_user['user_name'] ?>"
                            disabled>
                    </div>
                    <div class="input_group">
                        <label for="tinhthanh">Email</label>
                        <input type="text" placeholder="Tỉnh/Thành" value="<?php echo $fetch_user['email'] ?>" disabled>
                    </div>
                    <div class="input_group">
                        <label for="tinhthanh">Email</label>
                        <input type="text" placeholder="Tỉnh/Thành" value="<?php echo $fetch_user['address'] ?>"
                            disabled>
                    </div>
                </div>
            </div>
            <input type="submit" class="btn_submit" name="submit" value="Đăng sản phẩm">
    </form>
    </div>
    <?php include 'footer.php' ?>
</body>
<script>
    document.getElementById('imageUpload').addEventListener('change', function (event) {
        const file = event.target.files[0];
        const previewContainer = document.getElementById('previewContainer');
        const deleteImageBtn = document.getElementById('deleteImageBtn');

        // Xóa nội dung cũ trong khung
        previewContainer.innerHTML = '';
        deleteImageBtn.style.display = 'none';

        if (file) {
            // Tạo thẻ img để hiển thị ảnh
            const img = document.createElement('img');
            img.src = URL.createObjectURL(file);
            img.alt = 'Hình ảnh đã chọn';
            previewContainer.appendChild(img);

            // Hiển thị nút xóa
            deleteImageBtn.style.display = 'block';
            previewContainer.appendChild(deleteImageBtn);
        }

        // Xóa hình ảnh khi nhấn nút
        deleteImageBtn.addEventListener('click', function () {
            event.target.value = ''; // Reset input file
            previewContainer.innerHTML = '';
            deleteImageBtn.style.display = 'none';
        });
    });

</script>


</html>