<?php
include '../config.php';
session_start();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home - Admin</title>
    <link rel="stylesheet" href="./css/index.css">
    <link rel="stylesheet" href="../icon/fontawesome-free-6.6.0-web/css/all.min.css">
    <style>
        .content .content_logout {
            width: 100%;
            height: 35px;
            display: flex;
            justify-content: flex-end;
            align-items: center;
        }

        .content_logout button {
            width: 80px;
            height: 35px;
            background-color: white;
            border: none;
            margin-right: 5px;
            cursor: pointer;
        }

        .content_logout button:hover {
            background-color: white;
            border-bottom: 1px solid #ddd;
        }

        /* modal logout */
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
                <div class="menu_link" onclick="toggleSubmenu()">
                    <i class="fa-solid fa-user"></i>
                    <span>Quản lý tài khoản</span>
                    <i class="fa-solid fa-angle-down" style="font-size: 10px"></i>
                </div>
                <ul id="submenu" class="submenu">
                    <li>
                        <div class="submenu_link">
                            <a href="./management_account.php" style="text-decoration: none; color: white">
                                <i class="fa-solid fa-users"></i><span>Tất cả tài khoản</span>
                            </a>
                        </div>
                    </li>
                    <li>
                        <div class="submenu_link">
                            <a href="account_employee.php" style="text-decoration: none; color: white">
                                <i class="fa-solid fa-users-line"></i><span>Tài khoản nhân viên</span>
                            </a>
                        </div>
                    </li>
                </ul>
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
        </ul>
    </aside>

    <!-- Nội dung chính bên phải -->
    <div class="main-content">
        <header class="header">
            <div class="account-name" onclick="toggleLogoutButton()">
                <span><?php echo $_SESSION['admin_name'] ?></span>
                <i class="fa-regular fa-circle-user"></i>
            </div>
        </header>


        <div class="content">
            <div class="content_logout">
                <button type="button" onclick="showLogoutModal()">
                    <i class="fa-solid fa-right-from-bracket"></i> <span>Đăng xuất</span>
                </button>
            </div>
            <div class="content_title">
                <span>BẢNG ĐIỀU KHIỂN</span>
            </div>

            <div class="content_overview">

                <div class="overview_item">
                    <?php

                    $select_all_account = mysqli_query($conn, "select * from users where role_id !=1") or die('query fail');
                    $total_all_account = mysqli_num_rows($select_all_account);

                    ?>
                    <div class="overview_item_title">
                        <span style="font-size: 10px; color: orange; font-weight: bold">TẤT CẢ TÀI KHOẢN</span>
                        <span style="font-weight: bold; color: orangered; font-size: 20px"><?php echo $total_all_account ?>
                        </span>
                    </div>
                    <div class="overview_item_icon">
                        <span style="font-size: 30px; color: gray"><i class="fa-solid fa-user-group"></i></span>
                    </div>
                </div>

                <div class="overview_item">
                    <?php

                    $select_accounts = mysqli_query($conn, "select * from users where role_id = 3") or die('query fail');
                    $total_account = mysqli_num_rows($select_accounts);

                    ?>
                    <div class="overview_item_title">
                        <span style="font-size: 10px; color: orange; font-weight: bold">TÀI KHOẢN NGƯỜI DÙNG</span>
                        <span style="font-weight: bold; color: orangered; font-size: 20px">
                            <?php echo $total_account ?>
                        </span>
                    </div>
                    <div class="overview_item_icon">
                        <span style="font-size: 30px; color: gray"><i class="fa-solid fa-user-large"></i></span>
                    </div>
                </div>

                <div class="overview_item">
                    <?php

                    $select_accemployee = mysqli_query($conn, "select * from users where role_id = 2") or die('query fail');
                    $total_account_employee = mysqli_num_rows($select_accemployee);

                    ?>
                    <div class="overview_item_title">
                        <span style="font-size: 10px; color: orange; font-weight: bold">TÀI KHOẢN NHÂN VIÊN</span>
                        <span style="font-weight: bold; color: orangered; font-size: 20px">
                            <?php echo $total_account_employee ?>
                        </span>
                    </div>
                    <div class="overview_item_icon">
                        <span style="font-size: 30px; color: gray"><i class="fa-solid fa-user-check"></i></span>
                    </div>
                </div>
            </div>

            <div class="content_overview">
                <div class="overview_item">
                    <?php

                    $select_product = mysqli_query($conn, "select * from products where status = 1") or die('query fail');
                    $total_product = mysqli_num_rows($select_product);

                    ?>
                    <div class="overview_item_title">
                        <span style="font-size: 10px; color: orange; font-weight: bold">SẢN PHẨM TẠI CỬA HÀNG</span>
                        <span style="font-weight: bold; color: orangered; font-size: 20px">
                            <?php echo $total_product ?>
                        </span>
                    </div>
                    <div class="overview_item_icon">
                        <span style="font-size: 30px; color: gray"><i class="fa-brands fa-shopify"></i></span>
                    </div>
                </div>

                <div class="overview_item">
                    <?php

                    $select_category = mysqli_query($conn, "select * from categories ") or die('query fail');
                    $total_category = mysqli_num_rows($select_category);

                    ?>
                    <div class="overview_item_title">
                        <span style="font-size: 10px; color: orange; font-weight: bold">DANH MỤC SẢN PHẨM</span>
                        <span style="font-weight: bold; color: orangered; font-size: 20px">
                            <?php echo $total_category ?>
                        </span>
                    </div>
                    <div class="overview_item_icon">
                        <span style="font-size: 30px; color: gray"><i class="fa-solid fa-table-list"></i></span>
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
    function toggleSubmenu() {
        const submenu = document.getElementById('submenu');
        submenu.style.display = submenu.style.display === 'none' || submenu.style.display === '' ? 'block' : 'none';
    }

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