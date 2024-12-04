<?php
include '../config.php';
session_start();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home - Nhân viên</title>
    <link rel="stylesheet" href="./css/index.css">
    <link rel="stylesheet" href="../icon/fontawesome-free-6.6.0-web/css/all.min.css">
    <style>
        .account-name {
            position: relative;
            cursor: pointer;
        }

        .dropdown-content {
            display: none;
            position: absolute;
            top: 100%;
            right: 0;
            background-color: white;
            box-shadow: 0px 8px 16px rgba(0, 0, 0, 0.2);
            z-index: 1;
            border-radius: 4px;
            width: 150px;
        }

        .dropdown-content a {
            padding: 10px;
            text-decoration: none;
            display: block;
            color: black;
        }

        .dropdown-content a:hover {
            background-color: #f1f1f1;
        }

        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            justify-content: center;
            align-items: center;
        }

        .modal-content {
            background-color: white;
            padding: 20px;
            border-radius: 4px;
            text-align: center;
            width: 350px;
            height: 100px;
        }

        .modal-content button {
            margin: 10px;
            padding: 8px 16px;
            cursor: pointer;
        }

        .modal-content .btn-ok {
            background-color: white;
            width: 80px;
            border-radius: 5px;
            color: green;
            border: 1px solid green;
        }

        .btn-ok:hover {
            background-color: green;
            color: white;
        }

        .modal-content .btn-cancel {
            width: 80px;
            background-color: white;
            color: red;
            border: 1px solid red;
            border-radius: 5px;
        }

        .btn-cancel:hover {
            background-color: red;
            color: white;
        }
    </style>
</head>

<body>
    <aside class="sidebar">
        <div class="name_website">
            <span>CONNAN</span>
        </div>
        <ul>
            <li class="menu_item">
                <div class="menu_link">
                    <a href="index.php" style="text-decoration: none; color: white">
                        <i class="fa-solid fa-list"></i>
                        <span>Bảng điều khiển</span>
                    </a>
                </div>
            </li>
            <li class="menu_item">
                <div class="menu_link">
                    <a href="approval_product.php" style="text-decoration: none; color: white">
                        <i class="fa-solid fa-check-to-slot"></i>
                        <span>Duyệt sản phẩm</span>
                    </a>
                </div>
            </li>
            <li class="menu_item">
                <div class="menu_link">
                    <a href="management_product.php" style="text-decoration: none; color: white">
                        <i class="fa-brands fa-shopify"></i>
                        <span>Quản lý sản phẩm</span>
                    </a>
                </div>
            </li>
            <li class="menu_item">
                <div class="menu_link">
                    <a href="management_category.php" style="text-decoration: none; color: white">
                        <i class="fa-solid fa-tag"></i>
                        <span>Quản lý danh mục</span>
                    </a>
                </div>
            </li>
            <li class="menu_item">
                <div class="menu_link">
                    <a href="notification.php" style="text-decoration: none; color: white">
                        <i class="fa-solid fa-paper-plane"></i>
                        <span>Gừi thông báo</span>
                    </a>
                </div>
            </li>
            <li class="menu_item">
                <div class="menu_link">
                    <a href="transaction_statistics.php" style="text-decoration: none; color: white">
                        <i class="fa-solid fa-hand-holding-dollar"></i>
                        <span>Thống kê giao dịch</span>
                    </a>
                </div>
            </li>
        </ul>
    </aside>

    <!-- Nội dung chính bên phải -->
    <div class="main-content">
        <header class="header">
            <div class="account-name" onclick="toggleDropdown()">
                <span><?php echo $_SESSION['employee_name'] ?></span>
                <i class="fa-regular fa-circle-user"></i>
                <div id="accountDropdown" class="dropdown-content">
                    <a href="#">Hồ sơ cá nhân</a>
                    <a href="#" onclick="showLogoutModal()">Đăng xuất</a>
                </div>
            </div>
        </header>

        <div class="content">
            <div class="content_title">
                <span>BẢNG ĐIỀU KHIỂN</span>
            </div>

            <div class="content_overview">

                <div class="overview_item">
                    <?php

                    $select_product = mysqli_query($conn, "select * from products where status = '1'") or die('query fail');
                    $total_product = mysqli_num_rows($select_product);

                    ?>
                    <div class="overview_item_title">
                        <span style="font-size: 10px; color: limegreen; font-weight: bold">SẢN PHẨM</span>
                        <span style="font-weight: bold; color: green; font-size: 20px">
                            <?php echo $total_product  ?>
                        </span>
                    </div>
                    <div class="overview_item_icon">
                        <span style="font-size: 30px; color: gray"><i class="fa-solid fa-bag-shopping"></i></span>
                    </div>
                </div>

                <div class="overview_item">
                    <?php

                    $select_category = mysqli_query($conn, "select * from categories") or die('query fail');
                    $total_category = mysqli_num_rows($select_category);

                    ?>
                    <div class="overview_item_title">
                        <span style="font-size: 10px; color: limegreen; font-weight: bold">DANH MỤC SẢN PHẨM</span>
                        <span style="font-weight: bold; color: yellow; font-size: 20px">
                            <?php echo $total_category ?>
                        </span>
                    </div>
                    <div class="overview_item_icon">
                        <span style="font-size: 30px; color: gray"><i class="fa-solid fa-tag"></i></span>
                    </div>
                </div>

                <div class="overview_item">
                    <?php

                    $select_productapp = mysqli_query($conn, "select * from products where status = 0") or die('query fail');
                    $total_productapp = mysqli_num_rows($select_productapp);

                    ?>
                    <div class="overview_item_title">
                        <span style="font-size: 10px; color: limegreen; font-weight: bold">SẢN PHẨM CẦN DUYỆT</span>
                        <span style="font-weight: bold; color: red; font-size: 20px">
                            <?php echo $total_productapp ?>
                        </span>
                    </div>
                    <div class="overview_item_icon">
                        <span style="font-size: 30px; color: gray"><i class="fa-solid fa-store"></i></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal xác nhận đăng xuất -->
    <div id="logoutModal" class="modal">
        <div class="modal-content">
            <p>Bạn có chắc chắn muốn đăng xuất không?</p>
            <button class="btn-cancel" onclick="hideLogoutModal()">Hủy</button>
            <button class="btn-ok" onclick="confirmLogout()">OK</button>
        </div>
    </div>


</body>

<script>
    function toggleDropdown() {
        const dropdown = document.getElementById('accountDropdown');
        dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block';
    }

    // Đóng dropdown khi nhấp ra ngoài
    window.addEventListener('click', function (event) {
        const dropdown = document.getElementById('accountDropdown');
        const accountName = document.querySelector('.account-name');
        if (!accountName.contains(event.target)) {
            dropdown.style.display = 'none';
        }
    });

    // Hiển thị modal xác nhận đăng xuất
    function showLogoutModal() {
        document.getElementById('logoutModal').style.display = 'flex';
    }

    // Ẩn modal xác nhận đăng xuất
    function hideLogoutModal() {
        document.getElementById('logoutModal').style.display = 'none';
    }

    // Xác nhận đăng xuất
    function confirmLogout() {
        window.location.href = '../login/logout.php'; // Đường dẫn đến trang xử lý đăng xuất
    }

</script>

</html>