<?php
require '../../carbon/autoload.php';
include('../config/config.php');

use Carbon\Carbon;

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$chart_data = [];

// Lấy tham số thời gian từ AJAX
if (isset($_POST['thoigian'])) {
    $thoigian = $_POST['thoigian'];
} else {
    $thoigian = '365ngay';
}

// Xác định mốc ngày bắt đầu
if ($thoigian == '7ngay') {
    $subdays = Carbon::now('Asia/Ho_Chi_Minh')->subDays(7)->toDateString();
} elseif ($thoigian == '28ngay') {
    $subdays = Carbon::now('Asia/Ho_Chi_Minh')->subDays(28)->toDateString();
} elseif ($thoigian == '90ngay') {
    $subdays = Carbon::now('Asia/Ho_Chi_Minh')->subDays(90)->toDateString();
} else { // mặc định 365 ngày
    $subdays = Carbon::now('Asia/Ho_Chi_Minh')->subDays(365)->toDateString();
}

$now = Carbon::now('Asia/Ho_Chi_Minh')->toDateString();

/*
 * THỐNG KÊ ĐƠN HÀNG THEO NGÀY ĐẶT (order_date)
 * - Chỉ lấy đơn KHÔNG BỊ HỦY (order_status != -1)
 * - Tính doanh thu & số lượng từ order_detail
 */
$sql = "
    SELECT 
        DATE(o.order_date) AS metric_date,
        COUNT(DISTINCT o.order_code) AS metric_order,
        SUM(
            (d.product_price - (d.product_price / 100 * d.product_sale))
            * d.product_quantity
        ) AS metric_sales,
        SUM(d.product_quantity) AS metric_quantity
    FROM orders o
    INNER JOIN order_detail d ON o.order_code = d.order_code
    WHERE o.order_status != -1
      AND o.order_date BETWEEN '{$subdays}' AND '{$now}'
    GROUP BY DATE(o.order_date)
    ORDER BY metric_date ASC
";

$result = mysqli_query($mysqli, $sql);

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $chart_data[] = [
            'date'     => $row['metric_date'],
            'order'    => (int)$row['metric_order'],
            'sales'    => (int)$row['metric_sales'],
            'quantity' => (int)$row['metric_quantity'],
        ];
    }
}

// Trả JSON về cho JS
echo json_encode($chart_data);
