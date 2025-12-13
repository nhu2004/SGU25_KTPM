<?php
session_start();
include('../../admin/config/config.php');

/**
 * USER HỦY ĐƠN HÀNG
 * - Chỉ cho hủy đơn đang xử lý (order_status = 0)
 * - Hoàn lại tồn kho
 * - Cập nhật trạng thái đơn = -1 (Đã hủy)
 * - Redirect về danh sách đơn với message=cancel_success
 */
if (isset($_GET['order_code']) && isset($_GET['order_cancel']) && $_GET['order_cancel'] == 1) {
    $order_code = (int) $_GET['order_code'];

    if ($order_code > 0) {
        // Lấy thông tin đơn
        $sql_get_order = "SELECT * FROM orders WHERE order_code = $order_code LIMIT 1";
        $query_get_order = mysqli_query($mysqli, $sql_get_order);
        $order = mysqli_fetch_array($query_get_order);

        // Chỉ xử lý nếu đơn tồn tại và đang ở trạng thái 0 (đang xử lý)
        if ($order && (int)$order['order_status'] === 0) {

            // Lấy chi tiết đơn
            $sql_get_order_detail = "SELECT * FROM order_detail WHERE order_code = $order_code";
            $query_order_detail = mysqli_query($mysqli, $sql_get_order_detail);

            // Hoàn lại tồn kho
            while ($item = mysqli_fetch_array($query_order_detail)) {
                $product_id = (int)$item['product_id'];

                $query_get_product = mysqli_query($mysqli, "SELECT * FROM product WHERE product_id = $product_id LIMIT 1");
                $product = mysqli_fetch_array($query_get_product);
                if (!$product) continue;

                $quantity       = $product['product_quantity'] + $item['product_quantity'];
                $quantity_sales = $product['quantity_sales'] - $item['product_quantity'];

                mysqli_query(
                    $mysqli,
                    "UPDATE product 
                        SET product_quantity = $quantity, 
                            quantity_sales   = $quantity_sales 
                      WHERE product_id = $product_id"
                );
            }

            // Cập nhật trạng thái đơn sang ĐÃ HỦY
            $sql_cancel_order = "UPDATE orders SET order_status = -1 WHERE order_code = $order_code LIMIT 1";
            mysqli_query($mysqli, $sql_cancel_order);
        }
    }

    // Quay về danh sách đơn hàng của user + báo huỷ thành công
    header('Location: ../../index.php?page=my_account&tab=account_order&message=cancel_success');
    exit;
}

/**
 * USER XÁC NHẬN ĐÃ NHẬN HÀNG
 * - Đặt ví dụ status = 5 (hoàn tất bởi khách)
 */
elseif (isset($_GET['order_code']) && isset($_GET['order_confirm']) && $_GET['order_confirm'] == 1) {
    $order_code = (int) $_GET['order_code'];

    if ($order_code > 0) {
        $sql_update_order = "UPDATE orders SET order_status = 5 WHERE order_code = $order_code LIMIT 1";
        mysqli_query($mysqli, $sql_update_order);
    }

    header('Location: ../../index.php?page=my_account&tab=account_order');
    exit;
}
