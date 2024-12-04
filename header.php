<?php
session_start();
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
$user_name = isset($_SESSION['user_name']) ? $_SESSION['user_name'] : null;

$sql = "SELECT * FROM notifications ORDER BY created_time DESC";
$result = $conn->query($sql);

$notifications = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $notifications[] = $row;
    }
}
?>

<form action="index.php" method="POST">
    <!-- HEADER -->
    <div class="header">
        <div class="header_logo">
            <a href="index.php">
                <img src="./img/C.png" alt="">
            </a>
        </div>
        <div class="header_search">
        </div>
        <div class="header_item">
            
            <div class="notification" id="notificationIcon">
                <i class="fa-solid fa-bell"></i>
                <div id="notificationDropdown" class="dropdown__content" style="display: none;">
                    <div class="dropdown_header">
                        <span>Thông báo của bạn</span>
                    </div>

                    <div class="dropdown_body">
                        <?php foreach ($notifications as $notify): ?>
                            <div class="notification_item">

                                <div class="notification_photo">
                                    <img src="./img/notification.png" alt="">
                                </div>

                                <div class="notification_content">
                                    <p class="notification-title"><?php echo $notify['title']; ?></p>
                                    <p class="notification-content"><?php echo $notify['content']; ?></p>
                                </div>

                            </div>
                        <?php endforeach; ?>

                    </div>
                </div>
            </div>
            <div class="cart">
                <?php if ($user_id): ?>
                    <?php
                    $count_cart = mysqli_query($conn, "SELECT COUNT(*) as TotalCart FROM cart where user_id = $user_id");
                    $fetch_total_cart = mysqli_fetch_assoc($count_cart);
                ?>
                    <a href="./cart.php">
                        <i class="fa-solid fa-cart-shopping"></i>
                        <?php if ($count_cart > 0): ?>
                            <span class="cart-badge"><?= $fetch_total_cart['TotalCart'] ?></span>
                        <?php endif; ?>
                    </a>
                <?php else: ?>
                    <a href="./login/login.php">
                        <i class="fa-solid fa-cart-shopping"></i>
                    </a>
                <?php endif; ?>
            </div>

        </div>
        <div class="header_login">
            <?php if ($user_id): ?>
                <div class="user_dropdown" id="username">
                    <span
                        style="cursor: pointer; margin-right: 5px"><?php echo isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'Tên tài khoản'; ?></span>
                    <i class="fa-solid fa-circle-user" style="cursor: pointer;"></i>
                    <div class="dropdown_content" id="dropdownMenu">
                        <a href="./myprofile.php">Hồ sơ cá nhân</a>
                        <a href="./change_password.php">Đổi mật khẩu</a>
                        <a href="#" onclick="showLogoutModal()">Đăng xuất</a>
                    </div>
                </div>
            <?php else: ?>
                <a href="./login/login.php" style="text-decoration: none; color: orange">Đăng nhập</a>
                <p>/</p>
                <a href="./login/signin.php" style="text-decoration: none; color: orange">Đăng ký</a>
            <?php endif; ?>
        </div>
    </div>
    <!-- NAVBAR -->
    <div class="menu">
        <div class="menu_item">
            <a href="index.php">TRANG CHỦ</a>
        </div>
        <div class="menu_item">
            <a href="aboutus.php">GIỚI THIỆU</a>
        </div>
        <div class="menu_item">
            <a href="#">TIN TỨC</a>
        </div>
        <div class="menu_item">
            <?php if ($user_id): ?>
                <a href="./myshop.php" class="navbar-link">CỬA HÀNG</a>
            <?php else: ?>
                <a href="./login/login.php" class="navbar-link">CỬA HÀNG</a>
            <?php endif; ?>
        </div>
        <div class="menu_item" onclick="toggleOrderDropdown()">
            <a href="#">ĐƠN HÀNG</a>
            <div id="orderDropdown" class="dropdown-content" style="display: none; margin-top: 120px">
                <?php if ($user_id): ?>
                    <a href="./order_purchase.php" class="navbar-link">Đơn mua</a>
                <?php else: ?>
                    <a href="./login/login.php" class="navbar-link">Đơn mua</a>
                <?php endif; ?>
                <?php if ($user_id): ?>
                    <a href="./sales_order.php" class="navbar-link">Đơn bán</a>
                <?php else: ?>
                    <a href="./login/login.php" class="navbar-link">Đơn bán</a>
                <?php endif; ?>
            </div>
        </div>
        <div class="menu_item">
            <?php if ($user_id): ?>
                <a href="./post_product.php" class="navbar-link">ĐĂNG SẢN PHẨM</a>
            <?php else: ?>
                <a href="./login/login.php" class="navbar-link">ĐĂNG SẢN PHẨM</a>
            <?php endif; ?>
        </div>
    </div>
</form>
<div id="logoutModal" class="modal" style="display: none;">
    <div class="modal-content">
        <p>Bạn có chắc chắn muốn đăng xuất không?</p>
        <div class="button">
            <button onclick="closeModal()" class="btn_no">Hủy</button>
            <button onclick="confirmLogout()" class="btn_ok">OK</button>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const username = document.getElementById('username');
        const dropdownMenu = document.getElementById('dropdownMenu');
        const orderDropdown = document.getElementById('orderDropdown');
        const notificationIcon = document.getElementById('notificationIcon');
        const notificationDropdown = document.getElementById('notificationDropdown');

        // Toggle user dropdown
        if (username) {
            username.addEventListener('click', function (event) {
                dropdownMenu.style.display = dropdownMenu.style.display === 'block' ? 'none' : 'block';
                event.stopPropagation(); // Prevent event bubbling
            });
        }

        // Toggle order dropdown
        document.querySelector('.menu_item a[href="#"]').addEventListener('click', function (event) {
            toggleOrderDropdown();
            event.stopPropagation(); // Prevent event bubbling
        });

        // Toggle notification dropdown
        notificationIcon.addEventListener('click', function (event) {
            notificationDropdown.style.display = notificationDropdown.style.display === 'block' ? 'none' :
                'block';
            event.stopPropagation(); // Prevent event bubbling
        });

        // Close dropdowns if clicking outside
        window.addEventListener('click', function (event) {
            if (!event.target.closest('.user_dropdown')) {
                dropdownMenu.style.display = 'none'; // Hide user dropdown
            }
            if (!event.target.closest('.menu_item')) {
                orderDropdown.style.display = 'none'; // Hide order dropdown
            }
            if (!event.target.closest('#notificationIcon')) {
                notificationDropdown.style.display = 'none'; // Hide notification dropdown
            }
        });
    });

    function toggleOrderDropdown() {
        const orderDropdown = document.getElementById('orderDropdown');
        orderDropdown.style.display = orderDropdown.style.display === 'block' ? 'none' : 'block';
    }

    function showLogoutModal() {
        document.getElementById('logoutModal').style.display = 'flex';
    }

    function closeModal() {
        document.getElementById('logoutModal').style.display = 'none';
    }

    function confirmLogout() {
        window.location.href = './login/logout.php';
    }
</script>