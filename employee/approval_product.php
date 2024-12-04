<?php
include '../config.php';
session_start();
$user_id = $_SESSION['employee_id'];


// Duyệt sản phẩm
if (isset($_GET['browse_id'])) {
    $product_id = $_GET['browse_id'];
    $created_time = date('Y-m-d H:i:s');

    mysqli_query($conn, "insert into productapproval (status, created_time, product_id, employee_id) values ('Accept', '$created_time', '$product_id', '$user_id')") or die('query fail');

    mysqli_query($conn, "update products set status = 1 where product_id = '$product_id'");
    echo "<script type='text/javascript'>
        window.alert('Duyệt sản phẩm thành công.');
        </script>";
}
// Từ chối sản phẩm
if (isset($_GET['refuse_id'])) {
    $product_id = $_GET['refuse_id'];
    $created_time = date('Y-m-d H:i:s');
    mysqli_query($conn, "insert into productapproval (status, created_time, product_id, employee_id) values ('Dismiss', '$created_time', '$product_id', '$user_id')") or die('query fail');

    mysqli_query($conn, "update products set status = 1 where product_id = '$product_id'");
    echo "<script type='text/javascript'>
        window.alert('Đã từ chối sản phẩm!');
        </script>";
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Duyệt sản phẩm - Nhân viên</title>
    <link rel="stylesheet" href="./css/index.css">
    <link rel="stylesheet" href="./css/management.css">
    <link rel="stylesheet" href="../icon/fontawesome-free-6.6.0-web/css/all.min.css">
    <style>
        img {
            width: 80px;
            height: 80px;
            object-fit: cover;
        }

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
                <span>DANH SÁCH SẢN PHẨM</span>
            </div>

            <div class="table_account">
                <table>
                    <thead>
                        <tr>
                            <th>STT</th>
                            <th>Hình ảnh</th>
                            <th>Tên sản phẩm</th>
                            <th>Giá</th>
                            <th>Số lượng</th>
                            <th>Ngày đăng</th>
                            <th>Chi tiết</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $select_product = mysqli_query($conn, "select * from products where status = 0") or die('query fail');
                        $stt = 1;
                        if (mysqli_num_rows($select_product) > 0) {
                            while ($fetch_product = mysqli_fetch_assoc($select_product)) {
                                ?>
                                <tr>
                                    <td style="text-align: center"><?php echo $stt ?></td>
                                    <td style="text-align: center"><img
                                            src="../upload_image/<?php echo $fetch_product['product_image']; ?>" alt=""></td>
                                    <td><?php echo $fetch_product['product_name'] ?></td>
                                    <td><?php echo number_format($fetch_product['price'], 0, ',', '.'); ?> VNĐ</td>
                                    <td style="text-align: center"><?php echo $fetch_product['quantity'] ?></td>
                                    <td style="text-align: center"><?php echo $fetch_product['created_time'] ?></td>
                                    <td style="text-align: center">
                                        <a href="detail_product.php?product_id=<?php echo $fetch_product['product_id']; ?>"
                                            style="text-decoration: none; color: orange; font-size: 20px">
                                            <i class="fa-solid fa-circle-info"></i>
                                        </a>
                                    </td>
                                    <td style="text-align: center">
                                        <a href="approval_product.php?browse_id=<?php echo $fetch_product['product_id'] ?> "
                                            onclick="return confirm('Bạn muốn duyệt sản phẩm này?')"
                                            style="margin-right: 10px; color: limegreen; font-size: 20px; text-decoration: none">
                                            <i class="fa-solid fa-check"></i>
                                        </a>

                                        <a href="approval_product.php?refuse_id=<?php echo $fetch_product['product_id'] ?> "
                                            onclick="return confirm('Bạn muốn từ chối sản phẩm này?')"
                                            style="margin-left: 10px; color: red; font-size: 20px">
                                            <i class="fa-solid fa-x"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php
                                $stt++;
                            }
                        } else {
                            echo "<tr><td colspan='7'>Không có sản phẩm nào để xét duyệt!.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
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