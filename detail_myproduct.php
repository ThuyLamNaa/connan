<?php
include './config.php';
session_start();
error_reporting(E_ALL & ~E_NOTICE);

if (isset($_GET['id'])) {
    $product_id = $_GET['id'];
    $sql = "SELECT * FROM products WHERE product_id = $product_id";
    $result = mysqli_query($conn, $sql);
    $fetch_product = mysqli_fetch_assoc($result);
}

if (isset($_POST['add_cart'])) {
    $product_id = $_GET['id'];
    if (isset($_POST['abc_quantity'])) {
        $quantity = $_POST['abc_quantity'];
    } else {
        $quantity = $_POST['quantity'] ?? 1;
    }
    $user_id = $_SESSION['user_id'];
    $sql = "SELECT * FROM cart WHERE user_id = '$user_id' AND product_id = $product_id";
    $result = mysqli_query($conn, $sql);
    if (mysqli_num_rows($result) > 0) {
        $fetch_cart = mysqli_fetch_assoc($result);
        $quantity = $fetch_cart['quantity'] + $quantity;
        $sql = "UPDATE cart SET quantity = $quantity WHERE user_id = '$user_id' AND product_id = $product_id";
        mysqli_query($conn, $sql);
        echo "<script type='text/javascript'>
                window.alert('Sản phẩm đã có trong giỏ hàng và đã được cập nhật số lượng');
                </script>";
    } else {
        $sql = "INSERT INTO cart (user_id, product_id, quantity) VALUES ('$user_id', $product_id, $quantity)";
        mysqli_query($conn, $sql);
        echo "<script type='text/javascript'>
				window.alert('Thêm sản phẩm vào giỏ hàng thành công');
				</script>";
        header('Location: product.php?id=' . $product_id);
    }
}
if (isset($_GET['delete'])) {
    $delete_id = $_GET['delete'];
    try {
        // Tắt kiểm tra khóa ngoại tạm thời
        mysqli_query($conn, "SET FOREIGN_KEY_CHECKS=0");

        // Xóa tất cả các mục trong giỏ hàng liên quan đến sản phẩm này
        mysqli_query($conn, "DELETE FROM cart WHERE product_id = '$delete_id'");

        // Xóa sản phẩm
        mysqli_query($conn, "DELETE FROM products WHERE product_id = '$delete_id'") or die('query failed');

        // Bật lại kiểm tra khóa ngoại
        mysqli_query($conn, "SET FOREIGN_KEY_CHECKS=1");

        echo "<script type='text/javascript'>
            window.alert('Xóa sản phẩm thành công.');
            window.location.href = 'myshop.php?id=$delete_id'; // Chuyển hướng về trang thông tin chi tiết sản phẩm
            </script>";
    } catch (Exception $e) {
        // Bật lại kiểm tra khóa ngoại nếu có lỗi xảy ra
        mysqli_query($conn, "SET FOREIGN_KEY_CHECKS=1");
        echo "<script type='text/javascript'>
            window.alert('Không thể xóa sản phẩm này. Lỗi: {$e->getMessage()}');
            window.location.href = 'myshop.php?id=$delete_id'; // Chuyển hướng về trang thông tin chi tiết sản phẩm
            </script>";
    }
}

?>
<html>

<head>
    <title>Sản phẩm</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width initial-scale=1.0">
    <link rel="stylesheet" href="./css/detail_product.css">
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

</head>

<body>

    <?php include 'header.php' ?>

    <div class="main_content">
        <div class="product_content">
            <?php
            $user_id = $_SESSION['user_id'];
            $select_user = mysqli_query($conn, "select * from users where user_id = '$user_id'");
            $fetch_user = mysqli_fetch_assoc($select_user);
            ?>
            <div class="product_photo">
                <img src="./upload_image/<?php echo $fetch_product['product_image']; ?>" alt="">
            </div>

            <div class="product_info">
                <div class="product_name">
                    <span><?php echo $fetch_product['product_name'] ?></span>
                </div>

                <div class="product_price">
                    <span>
                        <?php echo number_format($fetch_product['price'], 0, ',', '.'); ?> đ
                    </span>
                </div>

                <div class="create_time">
                    <i class="fa-solid fa-clock" style="color: gray;"></i>
                    <span style="font-size: 15px; color: gray">Cập nhật vào: <?php echo $fetch_product['created_time'] ?></span>
                </div>


                <div class="product-detail-quantity">
                    <div class="product-detail-label-lb" style="width: 110px;">Số lượng</div>
                    <?php
                    if ($fetch_product['quantity'] > 1) {
                        ?>

                        <div class="product-detail-quantity-action">
                            <input name="quantity" max="<?php echo $fetch_product['quantity'] ?>" min="1" type="number"
                                value="1" id="quantity-input" class="product-detail-quantity-input">
                        </div>

                        <?php
                    } else {
                        ?>
                        <div class="product-detail-quantity-action">
                            <input name="abc_quantity" type="number" disabled value="1" id="quantity-input"
                                class="product-detail-quantity-input">
                        </div>
                        <?php
                    }
                    ?>

                </div>

                <div class="product_addtocart">
                    <button class="btn_addtocart" type="submit" name="add_cart" data-toggle="modal"
                        ata-target="#dialog1">
                        <a href="detail_myproduct.php?delete=<?php echo $fetch_product['product_id'] ?>"
                            onclick="return confirm('Bạn muốn xóa sản phẩm này?')"> <i
                                class="fa-solid fa-trash"></i> Xóa sản phẩm
                        </a> </button>

                    <button class="btn_buy" type="submit" name="btn_buy">
                        <a href="edit_myproduct.php?product_id=<?php echo $fetch_product['product_id'] ?>"
                            style="color: white"><i class="fa-solid fa-pen"></i> Chỉnh sửa sản phẩm</a>
                    </button>
                </div>
            </div>
        </div>

        <div class="product_infomation">
            <div class="description">
                <span class="title">Mô tả chi tiết sản phẩm</span>
                <span><?php echo $fetch_product['description'] ?></span>
            </div>

            <div class="other_infomation">
                <span class="title">Thông tin cơ bản</span>

                <table>
                    <?php
                    $category_id = $fetch_product['category_id'];
                    $select_category = mysqli_query($conn, "select * from categories where category_id = '$category_id'");
                    $fetch_category = mysqli_fetch_assoc($select_category);
                    ?>
                    <tr>
                        <td class="table_title">Danh mục</td>
                        <td> <?php echo $fetch_category['category_name'] ?> </td>
                    </tr>
                    <tr>
                        <td class="table_title">Thời gian đã sử dụng</td>
                        <td> <?php echo $fetch_product['time_used'] ?> </td>
                    </tr>
                    <tr>
                        <td class="table_title">Nơi mua sản phẩm</td>
                        <td> <?php echo $fetch_product['place_of_purchase'] ?> </td>
                    </tr>
                    <tr>
                        <td class="table_title">Giá khi mua sản phẩm</td>
                        <td> <?php echo $fetch_product['purchase_price'] ?> </td>
                    </tr>
                    <tr>
                        <td class="table_title">Ngày đăng</td>
                        <td><?php echo $fetch_product['created_time'] ?></td>
                    </tr>
                    <tr>
                        <td class="table_title">Địa chỉ</td>
                        <td><?php echo $fetch_user['address'] ?></td>
                    </tr>
                </table>
            </div>
        </div>

    </div>

    <?php include 'footer.php' ?>
</body>

</html>