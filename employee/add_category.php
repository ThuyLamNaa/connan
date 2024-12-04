<?php
include '../config.php';
session_start();

$employee_id = $_SESSION['employee_id'];

if (!isset($employee_id)) {
    header('location:../login/login.php');
}

// Initialize an empty array to store error messages
$errors = [];

if (isset($_POST['add_category'])) {
    // Get data from the form and sanitize it
    $category_name = mysqli_real_escape_string($conn, $_POST['category_name']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);

    // Check if the category name is empty
    if (empty($category_name)) {
        $errors['category_name'] = "Vui lòng nhập tên danh mục sản phẩm!";
    }

    // Check if the description is empty
    if (empty($description)) {
        $errors['description'] = "Vui lòng nhập mô tả danh mục sản phẩm!";
    }

    // Check if the category already exists in the database
    $select_category = mysqli_query($conn, "SELECT * FROM categories WHERE category_name = '$category_name'") or die('Query failed');

    if (mysqli_num_rows($select_category) > 0) {
        $errors['category_exists'] = "Danh mục sản phẩm đã tồn tại!";
    }

    // If there are no errors, insert the new category into the database
    if (empty($errors)) {
        mysqli_query($conn, "INSERT INTO categories (category_name, description) VALUES ('$category_name', '$description')") or die('Query failed');
        echo "<script type='text/javascript'>
                window.alert('Thêm danh mục sản phẩm thành công');
              </script>";
    }
}

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý danh mục sản phẩm - Nhân viên</title>
    <link rel="stylesheet" href="./css/index.css">
    <link rel="stylesheet" href="./css/management.css">
    <link rel="stylesheet" href="./css/add_category.css">
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
        .error {
            color: red;
            font-size: 14px;
            margin-top: 5px;
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
                <span>THÊM DANH MỤC SẢN PHẨM</span>
            </div>

            <form method="post" class="form_information">
                <div class="form_group">
                    <label>Tên danh mục</label>
                    <input type="text" class="form-control" name="category_name" maxlength="30" placeholder="Nhập tên danh mục sản phẩm">
                    <?php if (isset($errors['category_name'])): ?>
                        <div class="error"><?php echo $errors['category_name']; ?></div>
                    <?php endif; ?>
                </div>
                <div class="form_group">
                    <label>Mô tả</label>
                    <input type="text" class="form-control" name="description" maxlength="60" placeholder="Nhập mô tả danh mục sản phẩm">
                    <?php if (isset($errors['description'])): ?>
                        <div class="error"><?php echo $errors['description']; ?></div>
                    <?php endif; ?>
                    <?php if (isset($errors['category_exists'])): ?>
                        <div class="error"><?php echo $errors['category_exists']; ?></div>
                    <?php endif; ?>
                </div>
                <div class="button_create">
                    <input name="add_category" type="submit" class="btn" value="Thêm">
                </div>
            </form>

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