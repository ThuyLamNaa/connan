<?php
include './config.php';
session_start();
error_reporting(E_ALL & ~E_NOTICE);
$user_id = @$_SESSION['user_id']; // Lấy ID người dùng hiện tại

// Xác định số sản phẩm mỗi trang
$products_per_page = 12;

// Lấy số trang từ query string (nếu có)
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$start_from = ($page - 1) * $products_per_page;

// Truy vấn lấy tất cả thông báo (không thay đổi)
$sql = "SELECT * FROM notifications ORDER BY created_time DESC";
$result = $conn->query($sql);

$notifications = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $notifications[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CANNON</title>
    <link rel="stylesheet" href="./css/main_new.css">
    <link rel="stylesheet" href="./css/header.css">
    <style>
        /* Phần phân trang */
        .pagination {
            text-align: center;
            margin-top: 20px;
        }

        /* Các liên kết phân trang */
        .pagination a {
            display: inline-block;
            padding: 8px 15px;
            margin: 0 5px;
            background-color: #f0f0f0;
            color: #333;
            text-decoration: none;
            border: 1px solid #ddd;
            border-radius: 5px;
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        /* Hiệu ứng hover */
        .pagination a:hover {
            background-color: #ff6600;
            color: white;
        }

        /* Liên kết phân trang hiện tại */
        .pagination a.active {
            background-color: #ff6600;
            color: white;
            font-weight: bold;
            pointer-events: none;
            /* Không cho phép nhấn vào trang hiện tại */
        }

        /* Liên kết phân trang "Previous" và "Next" */
        .pagination a.previous,
        .pagination a.next {
            font-weight: bold;
        }

        /* Phân trang ở trên cùng */
        .pagination a.previous:hover,
        .pagination a.next:hover {
            background-color: #ff6600;
            color: white;
        }
    </style>

    <!-- Fontawesome css -->
    <link rel="stylesheet" href="./icon/fontawesome-free-6.6.0-web/css/all.min.css">
    <link rel="stylesheet" href="./icon/fontawesome-free-6.6.0-web/css/brands.min.css">
    <link rel="stylesheet" href="./icon/fontawesome-free-6.6.0-web/css/fontawesome.min.css">
</head>

<body>
    <?php include 'header.php' ?>

    <div class="container">
        <div class="category">
            <div class="category_title">
                <i class="fa-solid fa-bars"></i>
                <span class="title">DANH MỤC SẢN PHẨM</span>
            </div>

            <div class="category_content">
                <ul class="category_list">
                    <?php
                    $select_category = mysqli_query($conn, "SELECT * FROM categories") or die('Query failed');
                    if (mysqli_num_rows($select_category) > 0) {
                        while ($fetch_category = mysqli_fetch_assoc($select_category)) {
                            ?>
                            <li class="category_item" style="color: green">
                                <a href="?category_id=<?php echo $fetch_category['category_id']; ?>"
                                    style="color: orange; text-decoration: none;">
                                    <?php echo $fetch_category['category_name'] ?>
                                </a>
                            </li>
                            <?php
                        }
                    }
                    ?>
                </ul>
            </div>
        </div>

        <div class="product">
            <div class="filter">
                <span class="filter_title">Sắp xếp theo: </span>
                <form method="GET" action="">
                    <select name="sort_by" onchange="this.form.submit()" class="select_sort_by">
                        <option value="new">Mới nhất</option>
                        <option value="price_asc" <?php echo (isset($_GET['sort_by']) && $_GET['sort_by'] == 'price_asc') ? 'selected' : ''; ?>>Giá từ thấp đến cao</option>
                        <option value="price_desc" <?php echo (isset($_GET['sort_by']) && $_GET['sort_by'] == 'price_desc') ? 'selected' : ''; ?>>Giá từ cao đến thấp</option>
                    </select>

                    <input type="text" name="keyword" placeholder="Nhập từ khóa để tìm kiếm sản phẩm"
                        class="input_search">
                    <input type="submit" value="Tìm kiếm" class="btn_search">
                </form>
            </div>

            <div class="list_product">
                <?php
                // Lấy category_id từ URL nếu có
                $category_id = isset($_GET['category_id']) ? $_GET['category_id'] : '';
                $keyword = isset($_GET['keyword']) ? mysqli_real_escape_string($conn, $_GET['keyword']) : '';

                // Xác định thứ tự sắp xếp
                $sort_by = isset($_GET['sort_by']) ? $_GET['sort_by'] : 'new';  // Mặc định là 'new' nếu không có giá trị
                $order_by = "p.created_time DESC"; // Mặc định sắp xếp theo mới nhất
                switch ($sort_by) {
                    case 'new':
                        $order_by = "p.created_time DESC"; // Mặc định sắp xếp theo mới nhất
                        break;
                    case 'price_asc':
                        $order_by = "p.price ASC"; // Sắp xếp giá từ thấp đến cao
                        break;
                    case 'price_desc':
                        $order_by = "p.price DESC"; // Sắp xếp giá từ cao đến thấp
                        break;
                }

                // Truy vấn tất cả các sản phẩm từ productapproval có status là 'Accept' và quantity > 0
                $query = "SELECT p.* FROM productapproval pa
                JOIN products p ON pa.product_id = p.product_id
                WHERE pa.status = 'Accept' AND p.quantity > 0 AND p.seller_id != '$user_id'"; // Điều kiện ẩn các sản phẩm của người dùng hiện tại

                // Nếu có lọc theo danh mục, thêm điều kiện vào truy vấn
                if (!empty($category_id)) {
                    $query .= " AND p.category_id = '$category_id'";
                }

                // Nếu có từ khóa tìm kiếm, thêm điều kiện tìm kiếm vào truy vấn
                if (!empty($keyword)) {
                    $query .= " AND (p.product_name LIKE '%$keyword%' OR p.description LIKE '%$keyword%')";
                }

                // Thêm điều kiện sắp xếp
                $query .= " ORDER BY $order_by LIMIT $start_from, $products_per_page";

                $select_all_product = mysqli_query($conn, $query) or die('Query Failed');

                if (mysqli_num_rows($select_all_product) > 0) {
                    while ($product = mysqli_fetch_assoc($select_all_product)) {
                        ?>
                        <div class="product_item">
                            <a href="product.php?id=<?php echo $product['product_id']; ?>">
                                <div class="product_photo">
                                    <img src="./upload_image/<?php echo $product['product_image']; ?>" alt="">
                                </div>
                                <div class="product_info">
                                    <span><?php echo $product['product_name']; ?></span>
                                </div>
                                <div class="product_price">
                                    <span><?php echo number_format($product['price'], 0, ',', '.'); ?> VNĐ</span>
                                </div>
                            </a>
                        </div>
                        <?php
                    }
                } else {
                    echo "<p>Không có sản phẩm!</p>";
                }

                // Lấy tổng số sản phẩm để tính số trang
                $total_query = "SELECT COUNT(*) AS total FROM productapproval pa
                JOIN products p ON pa.product_id = p.product_id
                WHERE pa.status = 'Accept' AND p.quantity > 0 AND p.seller_id != '$user_id'"; // Điều kiện ẩn sản phẩm của người dùng hiện tại

                if (!empty($category_id)) {
                    $total_query .= " AND p.category_id = '$category_id'";
                }

                if (!empty($keyword)) {
                    $total_query .= " AND (p.product_name LIKE '%$keyword%' OR p.description LIKE '%$keyword%')";
                }

                $total_result = mysqli_query($conn, $total_query);
                $total_row = mysqli_fetch_assoc($total_result);
                $total_products = $total_row['total'];
                $total_pages = ceil($total_products / $products_per_page);
                ?>

            </div>

            <!-- Phân trang -->
            <div class="pagination">
            <?php
            // Lấy giá trị của 'sort_by' nếu có, nếu không thì mặc định là 'new'
            $sort_by = isset($_GET['sort_by']) ? $_GET['sort_by'] : 'new';
            
            // Liên kết "Previous" (Trang trước)
            if ($page > 1) {
                echo '<a href="?page=' . ($page - 1) . '&category_id=' . $category_id . '&keyword=' . $keyword . '&sort_by=' . $sort_by . '" class="previous">Previous</a>';
            }

            // Liên kết các trang số
            for ($i = 1; $i <= $total_pages; $i++) {
                echo '<a href="?page=' . $i . '&category_id=' . $category_id . '&keyword=' . $keyword . '&sort_by=' . $sort_by . '" class="' . ($page == $i ? 'active' : '') . '">' . $i . '</a>';
            }

            // Liên kết "Next" (Trang sau)
            if ($page < $total_pages) {
                echo '<a href="?page=' . ($page + 1) . '&category_id=' . $category_id . '&keyword=' . $keyword . '&sort_by=' . $sort_by . '" class="next">Next</a>';
            }
            ?>
        </div>


        </div>
    </div>

    <?php include 'footer.php' ?>
</body>

</html>
