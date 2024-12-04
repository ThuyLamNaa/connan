<?php 
include './config.php';
session_start();
error_reporting(E_ALL & ~E_NOTICE);


if(isset($_GET['product_id'])) {
    $product_id = $_GET['product_id'];
    $user_id = $_SESSION['user_id'];

    mysqli_query($conn, "delete from cart where product_id = '$product_id'and user_id = '$user_id'");
    $message = 'Xóa sản phẩm khỏi giỏ hàng thành công!';
    echo "<script type='text/javascript'>
    window.alert('$message');
        window.location.href = 'cart.php'; // Chuyển hướng về trang giỏ hàng
    </script>";

}

if(isset($_GET['delete_all'])) {
    $user_id = $_SESSION['user_id'];

    mysqli_query($conn, "delete from cart where user_id = '$user_id'");
    $message = 'Xóa giỏ hàng thành công!';
    echo "<script type='text/javascript'>
    window.alert('$message');
        window.location.href = 'cart.php'; // Chuyển hướng về trang giỏ hàng
    </script>";
}

?>