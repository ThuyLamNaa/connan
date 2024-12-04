<?php
include './config.php';
session_start();
error_reporting(E_ALL & ~E_NOTICE);

$errors = [];

if (isset($_POST['submit'])) {
    // Lấy giá trị từ form và thực hiện bảo vệ dữ liệu
    $product_name = mysqli_real_escape_string($conn, $_POST['product_name']);
    $price = $_POST['price'];
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $quantity = $_POST['quantity'];
    $time_used = $_POST['time_used'];
    $create_time = date('Y-m-d H:i:s');
   
    $warranty_period = $_POST['warranty_period'];
    $place_of_purchase = $_POST['place_of_purchase'];
    $purchase_price = $_POST['purchase_price'];
    $user_id = $_SESSION['user_id'];
    $category_id = $_POST['category'];

    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $image = $_FILES['image']['name'];
        $image_tmp_name = $_FILES['image']['tmp_name'];
        $image_folder = 'upload_image/' . $image;
    
        // Di chuyển tệp đã tải lên vào thư mục đích
        move_uploaded_file($image_tmp_name, $image_folder);
    } else {
        $image = null; // Đặt giá trị mặc định nếu không có hình ảnh được tải lên
        $errors['image'] = "Vui lòng chọn hình ảnh cho sản phẩm!";
    }
    

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
        $errors['image'] = "Vui lòng chọn hình ảnh!";
    }
    

    // Nếu không có lỗi nào, thực hiện lệnh SQL
    if (empty($errors)) {
        $image_value = $image ? "'$image'" : "NULL"; // Sử dụng NULL nếu không có hình ảnh
        $add_product = mysqli_query($conn, "INSERT INTO products (
            product_name, price, description, quantity, time_used, create_time, product_image, warranty_period,
            place_of_purchase, purchase_price, user_id, category_id
        ) VALUES ('$product_name', '$price', '$description', '$quantity', '$time_used', '$create_time', $image_value, '$warranty_period', '$place_of_purchase', '$purchase_price', '$user_id', '$category_id')");

        if ($add_product) {
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
    </style>
</head>

<body>

    <?php include 'header.php' ?>

    <form method="POST" enctype="multipart/form-data" class="main_content">
        <div class="title_post_product">
            <span>ĐĂNG THÔNG TIN SẢN PHẨM</span>
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
                        <input name="price" type="text" id="tieude" placeholder="Nhập giá sản phẩm bạn muốn bán">
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
                        <label for="images">Hình ảnh sản phẩm</label>
                        <div class="image-container">
                            <div class="image-box">
                                <input type="file" accept="image/jpg, image/jpeg, image/png" class="image-input" hidden
                                    name="image">
                                <div class="placeholder">
                                    <span>+</span>
                                </div>
                            </div>
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
                        <input name="time_used" type="text" placeholder="Bao nhiêu ngày (tuần, tháng, năm,...)">
                    </div>
                    <div class="input_group">
                        <label for="time_used">Bạn mua sản phẩm này ở đâu?</label>
                        <input name="place_of_purchase" type="text" placeholder="Nhập địa chỉ.">
                    </div>
                    <div class="input_group">
                        <label for="time_used">Giá khi mua của sản phẩm này?</label>
                        <input name="purchase_price" type="text" placeholder="Nhập giá khi mua.">
                    </div>

                    <div class="input_group">
                        <label for="time_used">Hạn sử dụng của sản phẩm? (Nếu có)</label>
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
    document.querySelectorAll('.image-box').forEach((box) => {
        const input = box.querySelector('.image-input');
        const placeholder = box.querySelector('.placeholder');

        box.addEventListener('click', () => input.click());

        input.addEventListener('change', (e) => {
            if (input.files && input.files[0]) {
                const img = document.createElement('img');
                img.src = URL.createObjectURL(input.files[0]);
                box.innerHTML = ''; // Clear placeholder
                box.appendChild(img);

                // Add remove button
                const removeBtn = document.createElement('button');
                removeBtn.classList.add('remove-btn');
                removeBtn.innerText = 'X';
                box.appendChild(removeBtn);

                // Remove image on click
                removeBtn.addEventListener('click', (event) => {
                    event.stopPropagation();
                    input.value = ''; // Clear input
                    box.innerHTML = ''; // Clear box
                    box.appendChild(placeholder.cloneNode(true));
                });
            }
        });
    });



</script>


</html>