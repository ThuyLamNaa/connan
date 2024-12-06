<?php
include './config.php';
session_start();
error_reporting(E_ALL & ~E_NOTICE);

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
} else {
    $user_id = null;
}

if (isset($_GET['id'])) {
    $product_id = $_GET['id'];
    $sql = "SELECT * FROM products WHERE product_id = $product_id";
    $result = mysqli_query($conn, $sql);
    $fetch_product = mysqli_fetch_assoc($result);
}



$notification = ""; // Khởi tạo biến thông báo

if (isset($_POST['add_cart'])) {
    if ($user_id == null) {
        header('location: ./login/login.php');
        exit; // Thêm exit để dừng mã sau khi chuyển hướng
    }

    $product_id = $_GET['id'];

    // Kiểm tra số lượng
    $quantity = isset($_POST['abc_quantity']) ? $_POST['abc_quantity'] : ($_POST['quantity'] ?? 1);

    if ($quantity <= 0) {
        // Nếu số lượng bằng 0, xóa sản phẩm khỏi bảng products
        $sql = "DELETE FROM products WHERE product_id = $product_id";
        mysqli_query($conn, $sql);
        echo "<script type='text/javascript'>
                window.alert('Sản phẩm đã được xóa khỏi trang web vì số lượng bằng 0.');
                window.location.href = 'product.php'; // Thay đổi đến trang danh sách sản phẩm
              </script>";
        exit; // Thêm exit để dừng mã sau khi xóa sản phẩm
    }

    // Còn lại mã xử lý giỏ hàng như bình thường
    $sql = "SELECT * FROM cart WHERE user_id = '$user_id' AND product_id = $product_id";
    $result = mysqli_query($conn, $sql);
    if (mysqli_num_rows($result) > 0) {
        $fetch_cart = mysqli_fetch_assoc($result);
        $quantity += $fetch_cart['quantity'];
        $sql = "UPDATE cart SET quantity = $quantity WHERE user_id = '$user_id' AND product_id = $product_id";
        mysqli_query($conn, $sql);
        $notification = "Sản phẩm đã có trong giỏ hàng và đã được cập nhật số lượng";
    } else {
        $sql = "INSERT INTO cart (user_id, product_id, quantity) VALUES ('$user_id', $product_id, $quantity)";
        mysqli_query($conn, $sql);
        $notification = "Thêm sản phẩm vào giỏ hàng thành công";
    }

    // Chuyển hướng sau khi thực hiện thêm vào giỏ hàng
    header('Location: product.php?id=' . $product_id . '&notification=' . urlencode($notification));
    exit; // Thêm exit để dừng mã sau khi chuyển hướng
}

if (isset($_GET['notification'])) {
    $notification = urldecode($_GET['notification']);
}

if (isset($_POST['buy_product'])) {
    if ($user_id == null) {
        header('location: ./login/login.php');
        exit; // Dừng mã sau khi chuyển hướng
    }

    $product_id = $_GET['id'];

    // Kiểm tra số lượng
    $quantity = isset($_POST['abc_quantity']) ? $_POST['abc_quantity'] : ($_POST['quantity'] ?? 1);

    if ($quantity <= 0) {
        // Nếu số lượng bằng 0, xóa sản phẩm khỏi bảng products
        $sql = "DELETE FROM products WHERE product_id = $product_id";
        mysqli_query($conn, $sql);
        echo "<script type='text/javascript'>
                window.alert('Sản phẩm đã được xóa khỏi trang web vì số lượng bằng 0.');
                window.location.href = 'product.php'; // Thay đổi đến trang danh sách sản phẩm
              </script>";
        exit; // Dừng mã sau khi xóa sản phẩm
    }

    // Chuyển hướng trực tiếp đến trang checkout mà không cần lưu vào giỏ hàng
    header('Location: checkout_product.php?id=' . $product_id . '&quantity=' . $quantity);
    exit; // Dừng mã sau khi chuyển hướng
}


// Xử lý lưu đánh giá
if (isset($_POST['submit_rating'])) {
    if ($user_id == null) {
        header('Location: ./login/login.php');
        exit;
    }

    $rating = $_POST['rating'];
    $review = mysqli_real_escape_string($conn, $_POST['review']);
    $created_time = date('Y-m-d H:i:s');
 

    // Kiểm tra xem người dùng đã đánh giá sản phẩm này chưa
    $check_query = "SELECT * FROM rating WHERE user_id = '$user_id' AND product_id = '$product_id'";
    $check_result = mysqli_query($conn, $check_query);

    if (mysqli_num_rows($check_result) > 0) {
        // Cập nhật đánh giá nếu đã tồn tại
        $update_query = "UPDATE rating SET number_rating = '$rating', comment = '$review', created_time = '$created_time' WHERE user_id = '$user_id' AND product_id = '$product_id'";
        mysqli_query($conn, $update_query);
    } else {
        // Thêm mới đánh giá
        $insert_query = "INSERT INTO rating (user_id, created_time, product_id, number_rating, comment) VALUES ('$user_id', '$created_time', '$product_id', '$rating', '$review')";
        mysqli_query($conn, $insert_query);
    }

    header("Location: product.php?id=$product_id&notification=Gửi đánh giá thành công");
    exit;
}

// Lấy các đánh giá hiện có của sản phẩm
$reviews_query = "SELECT r.*, u.user_name FROM rating r JOIN users u ON r.user_id = u.user_id WHERE product_id = '$product_id'";
$reviews_result = mysqli_query($conn, $reviews_query);

?>
<html>

<head>
    <title>Sản phẩm</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width initial-scale=1.0">
    <link rel="stylesheet" href="./css/main_new.css">
    <link rel="stylesheet" href="./css/product.css">
    <link rel="stylesheet" href="./css/header.css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.0/css/all.css"
        integrity="sha384-lZN37f5QGtY3VHgisS14W3ExzMWZxybE1SJSEsQp9S+oqd12jhcu+A56Ebc1zFSJ" crossorigin="anonymous">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

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

    <form method="POST" class="main_content">
        <div class="product_content">
            <div class="photo_product">
                <img src="./upload_image/<?php echo $fetch_product['product_image']; ?>" alt="">
            </div>

            <div class="info_product">
                <div class="name_product">
                    <span><?php echo $fetch_product['product_name'] ?></span>
                </div>

                <div class="price_product">
                    <span>
                        <?php echo number_format($fetch_product['price'], 0, ',', '.'); ?> đ
                    </span>
                </div>

                <div class="create_time">
                    <i class="fa-solid fa-clock" style="color: gray;"></i>
                    <span>Ngày đăng: <?php echo $fetch_product['created_time'] ?></span>
                </div>

                <div class="quantity_product">
                    <div class="product-detail-label-lb" style="width: 110px;">Số lượng</div>
                    <?php
                    if ($fetch_product['quantity'] > 1) {
                        ?>
                        <div class="quantity_product_action">
                            <input name="quantity" max="<?php echo $fetch_product['quantity'] ?>" min="1" type="number"
                                value="1" id="quantity-input" class="product-detail-quantity-input">
                        </div>
                        <?php
                    } else {
                        ?>
                        <div class="product-detail-quantity-action">
                            <input name="abc_quantity" type="number" disabled value="1" id="quantity-input">
                        </div>
                        <?php
                    }
                    ?>
                </div>

                <div class="product_addtocart">
                    <input class="btn_addtocart" type="submit" name="add_cart" value="Thêm vào giỏ hàng">

                    <input class="btn_buyproduct" type="submit" name="buy_product" value="Mua ngay">
                </div>

                <div class="shop_info">
                    <?php
                    $seller_id = $fetch_product['seller_id'];
                    $select_user = mysqli_query($conn, "select * from users where  user_id = '$seller_id'");
                    $fetch_user = mysqli_fetch_assoc($select_user);
                    ?>
                    <div class="shop_infodetail">
                        <a href="shop_seller.php?u_id=<?php echo $fetch_user['user_id']; ?>">

                            <div class="shop_avt">
                                <img src="./img/avt.jfif" alt="">
                            </div>

                            <div class="shop_content">
                                <span class="shop_name"><?php echo $fetch_user['user_name'] ?></span>
                                <span><span style="color: gray; font-weight: bold">Đã bán: </span>: 2 sản phẩm</span>
                                <span><span style="color: gray; font-weight: bold">Số điện thoại:
                                    </span><?php echo $fetch_user['phone_number'] ?></span>

                            </div>
                        </a>
                    </div>
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



        <?php if (!empty($notification)): ?>
            <script>
                window.onload = function () {
                    alert("<?php echo $notification; ?>");
                };
            </script>
        <?php endif; ?>
    </form>

    <div class="product-reviews">
        <h2>Đánh giá sản phẩm</h2>

        <!-- Form đánh giá -->
        <?php if ($user_id): ?>
            <form method="POST">
                <label for="rating">Đánh giá sao:</label>
                <select name="rating" id="rating" required>
                    <option value="1">1 sao</option>
                    <option value="2">2 sao</option>
                    <option value="3">3 sao</option>
                    <option value="4">4 sao</option>
                    <option value="5">5 sao</option>
                </select>

                <label for="review">Nhận xét:</label>
                <textarea name="review" id="review" rows="4" placeholder="Nhập nhận xét của bạn" required></textarea>

                <button type="submit" name="submit_rating">Gửi đánh giá</button>
            </form>
        <?php else: ?>
            <p><a href="./login/login.php">Đăng nhập</a> để gửi đánh giá.</p>
        <?php endif; ?>

        <!-- Hiển thị danh sách đánh giá -->
        <div class="reviews-list">
            <?php while ($review = mysqli_fetch_assoc($reviews_result)): ?>
                <div class="review-item">
                    <h4>
                      <span style="color: black">  <?php echo $review['user_name']; ?> </span>
                        <span class="stars">
                            <?php for ($i = 0; $i < $review['number_rating']; $i++): ?>
                                <i class="fa-solid fa-star" style="color: #FFD43B;"></i>
                            <?php endfor; ?>
                        </span>
                    </h4>
                    <p><?php echo $review['comment']; ?></p>
                </div>
            <?php endwhile; ?>
        </div>

    </div>

    <?php include 'footer.php' ?>

</body>


</html>