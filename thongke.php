<?php
include '../config.php';

require '../Carbon/autoload.php';
use Carbon\Carbon;
use Carbon\CarbonInterval;

// if (isset($_POST['thoigian'])) {
//     $thoigian = $_POST['thoigian'];
// } else {
//     $thoigian = '';
//     $subdays = Carbon::now('Asia/Ho_Chi_Minh')->subdays(365)->toDateString();
// }

// if ($thoigian == '7 ngày') {
//     $subdays = Carbon::now('Asia/Ho_Chi_Minh')->subdays(7)->toDateString();
// } elseif ($thoigian == '28 ngày') {
//     $subdays = Carbon::now('Asia/Ho_Chi_Minh')->subdays(28)->toDateString();
// } elseif ($thoigian == '90 ngày') {
//     $subdays = Carbon::now('Asia/Ho_Chi_Minh')->subdays(90)->toDateString();
// } elseif ($thoigian == '365 ngày') {
//     $subdays = Carbon::now('Asia/Ho_Chi_Minh')->subdays(90)->toDateString();
// }

$subdays = Carbon::now('Asia/Ho_Chi_Minh')->subdays(365)->toDateString();
$now = Carbon::now('Asia/Ho_Chi_Minh')->toDateString();

$sql = "SELECT * FROM statistical WHERE created_time BETWEEN '$subdays' AND '$now' ORDER BY created_time ASC";
$sql_query = mysqli_query($mysqli, $sql);

while ($val = mysqli_fetch_array($sql_query)) {
    $chart_data[] = array(
        'date' => $val['created_time'],
        'order_sold' => $val['order_sold'],
        'revenue' => $val['revenue'],
        'quantity' => $val['quantity']
    );
}
echo "<pre>" . print_r($chart_data, true) . "</pre>";
echo $data = json_encode($chart_data);
?>