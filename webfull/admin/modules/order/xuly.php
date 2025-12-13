<?php
require '../../../carbon/autoload.php';
include('../../config/config.php');

use Carbon\Carbon;

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$now = Carbon::now('Asia/Ho_Chi_Minh')->toDateString();

/**
 * Redirect về đúng trang trước khi gọi (giữ nguyên filter),
 * kèm theo message=...
 */
function redirect_with_message($message, $fallback = '../../index.php?action=order&query=order_list')
{
    $redirect = $_SERVER['HTTP_REFERER'] ?? $fallback;
    $separator = (strpos($redirect, '?') !== false) ? '&' : '?';
    header('Location: ' . $redirect . $separator . 'message=' . urlencode($message));
    exit;
}

// ----------------- KHỞI TẠO MẶC ĐỊNH -----------------
$order_codes = [];   // luôn có biến này

// Lấy danh sách mã đơn (confirm/cancel/reverse nhiều đơn)
if (isset($_GET['data']) && $_GET['data'] !== '') {
    $data = $_GET['data'];
    $decoded = json_decode($data, true);
    if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
        $order_codes = $decoded;
    }
}

/* ======================================================
 * 1) XÁC NHẬN / CHUYỂN TRẠNG THÁI ĐƠN HÀNG (ONLINE)
 *    - Có thể xác nhận nhiều đơn (data=[]) hoặc 1 đơn (order_code)
 * ====================================================== */
if (isset($_GET['confirm']) && (int)$_GET['confirm'] === 1) {

    // ======= TRƯỜNG HỢP NHIỀU ĐƠN (data=...) =======
    if (!empty($order_codes)) {

        foreach ($order_codes as $code) {
            $code = (int)$code;
            if ($code <= 0) continue;

            // Lấy thông tin đơn
            $sql_order_get  = "SELECT * FROM orders WHERE order_code = {$code} LIMIT 1";
            $query_order_get = mysqli_query($mysqli, $sql_order_get);
            $order = mysqli_fetch_array($query_order_get);
            if (!$order) continue;

            $order_status = (int)$order['order_status'];
            if ($order_status < 3) {
                $order_status++;
            }

            // Chuyển trạng thái đơn
            $sql_order_confirm = "UPDATE orders SET order_status = {$order_status} WHERE order_code = {$code}";
            mysqli_query($mysqli, $sql_order_confirm);

            // Nếu từ trạng thái 0 -> 1 (xác nhận lần đầu) thì cập nhật metrics
            if ((int)$order['order_status'] === 0) {
                $sql_order_detail = "SELECT * FROM order_detail WHERE order_code = {$code}";
                $query_order_detail = mysqli_query($mysqli, $sql_order_detail);

                $total    = 0;
                $quantity = 0;

                while ($row = mysqli_fetch_array($query_order_detail)) {
                    $line = ($row['product_price'] - ($row['product_price'] / 100 * $row['product_sale']))
                        * $row['product_quantity'];
                    $total    += $line;
                    $quantity += $row['product_quantity'];
                }

                $sql_thongke   = "SELECT * FROM metrics WHERE metric_date = '{$now}'";
                $query_thongke = mysqli_query($mysqli, $sql_thongke);

                if (mysqli_num_rows($query_thongke) == 0) {
                    $metric_sales    = $total;
                    $metric_quantity = $quantity;
                    $metric_order    = 1;

                    $sql_update_metrics = "
                        INSERT INTO metrics (metric_date, metric_order, metric_sales, metric_quantity)
                        VALUE ('{$now}', '{$metric_order}', '{$metric_sales}', '{$metric_quantity}')
                    ";
                    mysqli_query($mysqli, $sql_update_metrics);
                } else {
                    while ($row_tk = mysqli_fetch_array($query_thongke)) {
                        $metric_sales    = $row_tk['metric_sales']    + $total;
                        $metric_quantity = $row_tk['metric_quantity'] + $quantity;
                        $metric_order    = $row_tk['metric_order']    + 1;

                        $sql_update_metrics = "
                            UPDATE metrics
                               SET metric_order    = '{$metric_order}',
                                   metric_sales    = '{$metric_sales}',
                                   metric_quantity = '{$metric_quantity}'
                             WHERE metric_date     = '{$now}'
                        ";
                        mysqli_query($mysqli, $sql_update_metrics);
                    }
                }
            }
        }

        redirect_with_message('success');

    // ======= TRƯỜNG HỢP 1 ĐƠN (order_code=...) =======
    } elseif (!empty($_GET['order_code'])) {

        $code = (int)$_GET['order_code'];
        if ($code <= 0) {
            redirect_with_message('error');
        }

        // Lấy thông tin đơn
        $sql_order_get  = "SELECT * FROM orders WHERE order_code = {$code} LIMIT 1";
        $query_order_get = mysqli_query($mysqli, $sql_order_get);
        $order = mysqli_fetch_array($query_order_get);
        if ($order) {
            $order_status = (int)$order['order_status'];
            if ($order_status < 3) {
                $order_status++;
            }

            // Chuyển trạng thái đơn
            $sql_order_confirm = "UPDATE orders SET order_status = {$order_status} WHERE order_code = {$code}";
            mysqli_query($mysqli, $sql_order_confirm);

            // Nếu trạng thái ban đầu là 0 thì cập nhật metrics
            if ((int)$order['order_status'] === 0) {
                $sql_order_detail = "SELECT * FROM order_detail WHERE order_code = {$code}";
                $query_order_detail = mysqli_query($mysqli, $sql_order_detail);

                $total    = 0;
                $quantity = 0;

                while ($row = mysqli_fetch_array($query_order_detail)) {
                    $line = ($row['product_price'] - ($row['product_price'] / 100 * $row['product_sale']))
                        * $row['product_quantity'];
                    $total    += $line;
                    $quantity += $row['product_quantity'];
                }

                $sql_thongke   = "SELECT * FROM metrics WHERE metric_date = '{$now}'";
                $query_thongke = mysqli_query($mysqli, $sql_thongke);

                if (mysqli_num_rows($query_thongke) == 0) {
                    $metric_sales    = $total;
                    $metric_quantity = $quantity;
                    $metric_order    = 1;

                    $sql_update_metrics = "
                        INSERT INTO metrics (metric_date, metric_order, metric_sales, metric_quantity)
                        VALUE ('{$now}', '{$metric_order}', '{$metric_sales}', '{$metric_quantity}')
                    ";
                    mysqli_query($mysqli, $sql_update_metrics);
                } else {
                    while ($row_tk = mysqli_fetch_array($query_thongke)) {
                        $metric_sales    = $row_tk['metric_sales']    + $total;
                        $metric_quantity = $row_tk['metric_quantity'] + $quantity;
                        $metric_order    = $row_tk['metric_order']    + 1;

                        $sql_update_metrics = "
                            UPDATE metrics
                               SET metric_order    = '{$metric_order}',
                                   metric_sales    = '{$metric_sales}',
                                   metric_quantity = '{$metric_quantity}'
                             WHERE metric_date     = '{$now}'
                        ";
                        mysqli_query($mysqli, $sql_update_metrics);
                    }
                }
            }
        }

        // với 1 đơn, vẫn quay về chi tiết đơn online
        header('Location: ../../index.php?action=order&query=order_detail_online&order_code=' . $code . '&message=success');
        exit;
    }
}

/* ======================================================
 * 2) ROLLBACK (KHÔNG LƯU THAY ĐỔI)
 * ====================================================== */
if (isset($_GET['rollback']) && (int)$_GET['rollback'] === 1) {
    $code = isset($_GET['order_code']) ? (int)$_GET['order_code'] : 0;
    header('Location: ../../index.php?action=order&query=order_detail_online&order_code=' . $code . '&message=info');
    exit;
}

/* ======================================================
 * 3) HỦY ĐƠN (CANCEL) – kèm hoàn lại tồn kho
 * ====================================================== */
if (isset($_GET['cancel']) && (int)$_GET['cancel'] === 1 && !empty($order_codes)) {
    foreach ($order_codes as $code) {
        $code = (int)$code;
        if ($code <= 0) continue;

        $sql_get_order_detail = "SELECT * FROM order_detail WHERE order_code = {$code}";
        $query_order_detail   = mysqli_query($mysqli, $sql_get_order_detail);

        // Hoàn lại tồn kho
        while ($item = mysqli_fetch_array($query_order_detail)) {
            $product_id = (int)$item['product_id'];

            $query_get_product = mysqli_query($mysqli, "SELECT * FROM product WHERE product_id = {$product_id} LIMIT 1");
            $product = mysqli_fetch_array($query_get_product);
            if (!$product) continue;

            $quantity       = $product['product_quantity'] + $item['product_quantity'];
            $quantity_sales = $product['quantity_sales']   - $item['product_quantity'];

            mysqli_query(
                $mysqli,
                "UPDATE product
                    SET product_quantity = {$quantity},
                        quantity_sales   = {$quantity_sales}
                  WHERE product_id = {$product_id}"
            );
        }

        // Đánh dấu đơn bị hủy
        $sql_order_cancel = "UPDATE orders SET order_status = -1 WHERE order_code = {$code}";
        mysqli_query($mysqli, $sql_order_cancel);
    }

    redirect_with_message('success');
}

/* ======================================================
 * 3.1) XÓA ĐƠN HÀNG (ADMIN) – TC8 & TC10
 *      - Chỉ cho xoá đơn ĐÃ HỦY
 *      - Không cho xoá đơn ĐANG GIAO (2) hoặc ĐÃ HOÀN THÀNH (3)
 * ====================================================== */
if (isset($_GET['delete_order']) && (int)$_GET['delete_order'] === 1) {
    $code = isset($_GET['order_code']) ? (int)$_GET['order_code'] : 0;

    if ($code <= 0) {
        redirect_with_message('cannot_delete');
    }

    // Lấy trạng thái hiện tại của đơn
    $sql_get_order  = "SELECT * FROM orders WHERE order_code = {$code} LIMIT 1";
    $query_get_order = mysqli_query($mysqli, $sql_get_order);
    $order = mysqli_fetch_array($query_get_order);

    if (!$order) {
        redirect_with_message('cannot_delete');
    }

    $currentStatus = (int)$order['order_status'];

    // Nếu đơn đang giao (2) hoặc đã hoàn thành (3) -> KHÔNG CHO XOÁ (TC10)
    if ($currentStatus == 2 || $currentStatus == 3) {
        redirect_with_message('cannot_delete');
    }

    // Chỉ cho xoá đơn đã hủy theo TC8
    if ($currentStatus != -1) {
        redirect_with_message('cannot_delete');
    }

    // Xoá chi tiết đơn
    mysqli_query($mysqli, "DELETE FROM order_detail WHERE order_code = {$code}");
    // Xoá đơn chính
    mysqli_query($mysqli, "DELETE FROM orders WHERE order_code = {$code}");

    redirect_with_message('delete_success');
}

/* ======================================================
 * 4) XÓA SẢN PHẨM KHỎI SESSION ĐƠN HÀNG (ADMIN TẠO ĐƠN)
 * ====================================================== */
if (isset($_SESSION['order']) && isset($_GET['delete'])) {
    $product_id = (int)$_GET['delete'];
    $product    = [];

    foreach ($_SESSION['order'] as $order_item) {
        if ($order_item['product_id'] != $product_id) {
            $product[] = [
                'product_id'       => $order_item['product_id'],
                'product_name'     => $order_item['product_name'],
                'product_quantity' => $order_item['product_quantity'],
                'product_price'    => $order_item['product_price'],
                'product_sale'     => $order_item['product_sale'],
                'product_image'    => $order_item['product_image'],
            ];
        }
    }

    $_SESSION['order'] = $product;
    header('Location:../../index.php?action=order&query=order_add&message=success');
    exit;
}

// Xóa tất cả sản phẩm khỏi session đơn
if (isset($_GET['deleteall']) && (int)$_GET['deleteall'] === 1) {
    unset($_SESSION['order']);
    header('Location:../../index.php?action=order&query=order_add&message=success');
    exit;
}

/* ======================================================
 * 5) THÊM SẢN PHẨM VÀO ĐƠN (ADMIN TẠO ĐƠN)
 * ====================================================== */
if (isset($_POST['addtoorder'])) {
    $product_id       = (int)$_POST['product_id'];
    $product_quantity = (int)$_POST['product_quantity'];

    $sql   = "SELECT * FROM product WHERE product_id = {$product_id} LIMIT 1";
    $query = mysqli_query($mysqli, $sql);
    $row   = mysqli_fetch_array($query);

    // Lấy URL gọi về (nếu không có referer thì fallback)
    $redirect = $_SERVER['HTTP_REFERER'] ?? '../../index.php?action=order&query=order_add';

    if ($row && $row['product_quantity'] >= $product_quantity) {
        $new_product = [[
            'product_id'       => $product_id,
            'product_name'     => $row['product_name'],
            'product_quantity' => $product_quantity,
            'product_price'    => $row['product_price'],
            'product_sale'     => $row['product_sale'],
            'product_image'    => $row['product_image'],
        ]];

        if (isset($_SESSION['order'])) {
            $found   = false;
            $product = [];

            foreach ($_SESSION['order'] as $order_item) {
                if ($order_item['product_id'] == $product_id) {
                    $product[] = [
                        'product_id'       => $order_item['product_id'],
                        'product_name'     => $order_item['product_name'],
                        'product_quantity' => $order_item['product_quantity'] + $product_quantity,
                        'product_price'    => $order_item['product_price'],
                        'product_sale'     => $order_item['product_sale'],
                        'product_image'    => $order_item['product_image'],
                    ];
                    $found = true;
                } else {
                    $product[] = [
                        'product_id'       => $order_item['product_id'],
                        'product_name'     => $order_item['product_name'],
                        'product_quantity' => $order_item['product_quantity'],
                        'product_price'    => $order_item['product_price'],
                        'product_sale'     => $order_item['product_sale'],
                        'product_image'    => $order_item['product_image'],
                    ];
                }
            }

            $_SESSION['order'] = $found ? $product : array_merge($product, $new_product);
        } else {
            $_SESSION['order'] = $new_product;
        }

        $separator = (strpos($redirect, '?') !== false) ? '&' : '?';
        header('Location: ' . $redirect . $separator . 'message=success');
        exit;
    } else {
        $separator = (strpos($redirect, '?') !== false) ? '&' : '?';
        header('Location: ' . $redirect . $separator . 'message=error');
        exit;
    }
}

/* ======================================================
 * 6) ADMIN TẠO ĐƠN HÀNG TRỰC TIẾP (tại cửa hàng)
 * ====================================================== */
if (isset($_POST['order_add'])) {

    // Nếu không có session order thì khỏi xử lý, tránh warning
    if (!isset($_SESSION['order']) || empty($_SESSION['order'])) {
        header('Location:../../index.php?action=order&query=order_add&message=error');
        exit;
    }

    $account_id_admin = (int)($_SESSION['account_id_admin'] ?? 0);
    $order_code       = rand(0, 9999);
    $delivery_id      = rand(0, 9999);
    $order_date       = Carbon::now('Asia/Ho_Chi_Minh');
    $delivery_name    = $_POST['customer_name'];
    $delivery_address = $_POST['customer_address'];
    $delivery_phone   = $_POST['customer_phone'];
    $delivery_note    = 'Đơn hàng mua trực tiếp';
    $order_type       = 5;  // mua trực tiếp
    $total_amount     = 0;
    $validate         = 1;

    foreach ($_SESSION['order'] as $cart_item) {
        $product_id = (int)$cart_item['product_id'];
        $query_get_product = mysqli_query($mysqli, "SELECT * FROM product WHERE product_id = {$product_id} LIMIT 1");
        $product = mysqli_fetch_array($query_get_product);
        if (!$product || $product['product_quantity'] < $cart_item['product_quantity']) {
            $validate = 0;
        }
        $total_amount +=
            ($cart_item['product_price'] - ($cart_item['product_price'] / 100 * $cart_item['product_sale']))
            * $cart_item['product_quantity'];
    }

    if ($validate == 1) {
        $insert_delivery = "
            INSERT INTO delivery(delivery_id, account_id, delivery_name, delivery_phone, delivery_note, delivery_address)
            VALUE ({$delivery_id}, {$account_id_admin}, '{$delivery_name}', '{$delivery_phone}', '{$delivery_note}', '{$delivery_address}')
        ";
        mysqli_query($mysqli, $insert_delivery);

        $insert_order = "
            INSERT INTO orders(order_code, order_date, account_id, delivery_id, total_amount, order_type, order_status)
            VALUE ({$order_code}, '" . $order_date . "', {$account_id_admin}, '{$delivery_id}', {$total_amount}, {$order_type}, 3)
        ";
        $query_insert_order = mysqli_query($mysqli, $insert_order);

        $quantity_tk = 0;
        if ($query_insert_order) {
            foreach ($_SESSION['order'] as $cart_item) {
                $product_id = (int)$cart_item['product_id'];
                $query_get_product = mysqli_query($mysqli, "SELECT * FROM product WHERE product_id = {$product_id} LIMIT 1");
                $product = mysqli_fetch_array($query_get_product);
                if ($product && $product['product_quantity'] >= $cart_item['product_quantity']) {
                    $product_quantity = $cart_item['product_quantity'];
                    $product_price    = $cart_item['product_price'];
                    $product_sale     = $cart_item['product_sale'];

                    $quantity       = $product['product_quantity'] - $product_quantity;
                    $quantity_tk   += $product_quantity;
                    $quantity_sales = $product['quantity_sales'] + $product_quantity;

                    $insert_order_detail = "
                        INSERT INTO order_detail(order_code, product_id, product_quantity, product_price, product_sale)
                        VALUE ('{$order_code}', '{$product_id}', '{$product_quantity}', '{$product_price}', '{$product_sale}')
                    ";
                    mysqli_query($mysqli, $insert_order_detail);
                    mysqli_query(
                        $mysqli,
                        "UPDATE product
                            SET product_quantity = {$quantity},
                                quantity_sales   = {$quantity_sales}
                          WHERE product_id = {$product_id}"
                    );
                }
            }
        }

        $update_total_amount = "UPDATE orders SET total_amount = {$total_amount} WHERE order_code = {$order_code}";
        mysqli_query($mysqli, $update_total_amount);

        $nowDate = $order_date->format('Y-m-d');

        $sql_thongke   = "SELECT * FROM metrics WHERE metric_date = '{$nowDate}'";
        $query_thongke = mysqli_query($mysqli, $sql_thongke);

        if (mysqli_num_rows($query_thongke) == 0) {
            $metric_sales    = $total_amount;
            $metric_quantity = $quantity_tk;
            $metric_order    = 1;

            $sql_update_metrics = "
                INSERT INTO metrics (metric_date, metric_order, metric_sales, metric_quantity)
                VALUE ('{$nowDate}', '{$metric_order}', '{$metric_sales}', '{$metric_quantity}')
            ";
            mysqli_query($mysqli, $sql_update_metrics);
        } else {
            while ($row_tk = mysqli_fetch_array($query_thongke)) {
                $metric_sales    = $row_tk['metric_sales']    + $total_amount;
                $metric_quantity = $row_tk['metric_quantity'] + $quantity_tk;
                $metric_order    = $row_tk['metric_order']    + 1;

                $sql_update_metrics = "
                    UPDATE metrics
                       SET metric_order    = '{$metric_order}',
                           metric_sales    = '{$metric_sales}',
                           metric_quantity = '{$metric_quantity}'
                     WHERE metric_date     = '{$nowDate}'
                ";
                mysqli_query($mysqli, $sql_update_metrics);
            }
        }

        unset($_SESSION['order']);
        header('Location:../../index.php?action=order&query=order_detail&order_code=' . $order_code . '&message=success');
        exit;
    } else {
        header('Location:../../index.php?page=404');
        exit;
    }
}
