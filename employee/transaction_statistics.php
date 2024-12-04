<?php
include '../config.php';
session_start();

// Define default values for start_date and end_date
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : '';
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : '';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý giao dịch - Nhân viên</title>
    <link rel="stylesheet" href="./css/index.css">
    <link rel="stylesheet" href="./css/management.css">
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

        .filter_date {
            margin-top: 10px;
        }

        .filter_date input {
            width: 150px;
            height: 30px;
            border-radius: 5px;
            border: 1px solid orange;
            padding: 5px;
            font-family: Arial, Helvetica, sans-serif;
        }

        .filter_date button {
            width: 70px;
            height: 30px;
            border-radius: 5px;
            background-color: orange;
            border: none;
            cursor: pointer;
        }

        .filter_date button:hover {
            background-color: orangered;
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
                        <span>Gửi thông báo</span>
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
                <span>THỐNG KÊ GIAO DỊCH</span>
            </div>
            <form method="GET" action="" class="filter_date">
                <label for="start_date">Từ ngày:</label>
                <input type="date" id="start_date" name="start_date" value="<?php echo $start_date; ?>">
                <label for="end_date">Đến ngày:</label>
                <input type="date" id="end_date" name="end_date" value="<?php echo $end_date; ?>">
                <button type="submit">Lọc</button>
            </form>
            <div class="table_account">
                <table>
                    <thead>
                        <tr>
                            <th>STT</th>
                            <th>ID</th>
                            <th>Người bán</th>
                            <th>Số giao dịch</th>
                            <th>Doanh thu</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Truy vấn SQL đã được cải tiến
                        $query = "
                            SELECT 
                                u.user_id, 
                                u.user_name, 
                                COUNT(o.order_id) AS total_transactions, 
                                SUM(o.total_amount) AS total_revenue,
                                t.created_time
                            FROM 
                                users u
                            JOIN 
                                orders o ON u.user_id = o.seller_id
                            LEFT JOIN transactions t ON o.order_id = t.order_id
                            WHERE 
                                o.status = 'completed' 
                                AND t.status = 'Đã thanh toán'";

                        if ($start_date && $end_date) {
                            $query .= " AND t.created_time BETWEEN '$start_date' AND '$end_date' ";
                        }

                        $query .= "
                            GROUP BY 
                                u.user_id
                            ORDER BY 
                                total_transactions DESC
                        ";

                        $result = mysqli_query($conn, $query) or die('Query failed');
                        $stt = 1;

                        if (mysqli_num_rows($result) > 0) {
                            while ($row = mysqli_fetch_assoc($result)) {
                                ?>
                                <tr>
                                    <td style="text-align: center"><?php echo $stt ?></td>
                                    <td><?php echo $row['user_id'] ?></td>
                                    <td><?php echo $row['user_name'] ?></td>
                                    <td style="text-align: center"><?php echo $row['total_transactions'] ?></td>
                                    <td style="text-align: right">
                                        <?php echo number_format($row['total_revenue'], 0, ',', '.') ?> đ
                                    </td>
                                </tr>
                                <?php
                                $stt++;
                            }
                        } else {
                            echo "<tr><td colspan='5'>Không có giao dịch nào.</td></tr>";
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

</body>

</html>