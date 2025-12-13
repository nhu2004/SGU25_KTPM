<?php
/**
 * PHASE 2: Checkout handler (preview only)
 * - Không ghi DB, không trừ tồn, không gọi cổng thanh toán.
 * - Lưu thông tin giao hàng & tổng tiền vào SESSION để hiển thị.
 * - Chuẩn bị sẵn để nâng cấp lên Phase sau (COD/MoMo/VNPAY).
 */
if (session_status() === PHP_SESSION_NONE) session_start();

// Nếu form không submit đúng
if (!isset($_POST['redirect'])) {
    header('Location: ../../index.php?page=cart');
    exit;
}

// Yêu cầu đăng nhập ở Phase 2 (nếu spec của bạn cần)
if (empty($_SESSION['account_id'])) {
    header('Location: ../../index.php?page=login');
    exit;
}

/* CSRF (nếu form của bạn có đưa token; không có cũng không sao) */
if (!empty($_SESSION['csrf_token'])) {
    $csrf_ok = hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'] ?? '');
    if (!$csrf_ok) {
        http_response_code(403);
        echo 'Invalid CSRF token';
        exit;
    }
}

/* Lấy dữ liệu từ form */
$delivery_name    = trim($_POST['delivery_name'] ?? '');
$delivery_address = trim($_POST['delivery_address'] ?? '');
$delivery_phone   = trim($_POST['delivery_phone'] ?? '');
$delivery_note    = trim($_POST['delivery_note'] ?? '');
$order_type       = (int)($_POST['order_type'] ?? 1); // 1: COD, 2/3/4: cổng thanh toán (Phase 3+ mới dùng)

/* Validate cơ bản cho Phase 2 */
$errors = [];
if ($delivery_name === '')    $errors[] = 'Vui lòng nhập tên người nhận.';
if ($delivery_address === '') $errors[] = 'Vui lòng nhập địa chỉ nhận hàng.';
if ($delivery_phone === '')   $errors[] = 'Vui lòng nhập số điện thoại.';

if (!empty($errors)) {
    // Lưu lỗi và dữ liệu để fill lại form
    $_SESSION['checkout_errors'] = $errors;
    $_SESSION['checkout_old'] = [
        'delivery_name'    => $delivery_name,
        'delivery_address' => $delivery_address,
        'delivery_phone'   => $delivery_phone,
        'delivery_note'    => $delivery_note,
        'order_type'       => $order_type,
    ];
    header('Location: ../../index.php?page=checkout');
    exit;
}

/* XÁC ĐỊNH NGUỒN SẢN PHẨM: BUYNOW HAY CART */
$mode  = $_SESSION['checkout_mode'] ?? 'cart';
$items = [];

if ($mode === 'buynow' && !empty($_SESSION['buynow'])) {
    // Đơn MUA NGAY: chỉ lấy 1 sản phẩm vừa bấm Mua ngay
    $items = [ $_SESSION['buynow'] ];
} else {
    // Đơn từ GIỎ HÀNG như cũ
    if (!empty($_SESSION['cart']) && is_array($_SESSION['cart'])) {
        $items = $_SESSION['cart'];
    }
}

if (empty($items)) {
    // Không có sản phẩm -> quay lại giỏ
    header('Location: ../../index.php?page=cart');
    exit;
}

/* Tính tổng tiền từ danh sách items (cart hoặc buynow) */
$total_amount = 0.0;
foreach ($items as $item) {
    $price = (float)($item['product_price'] ?? 0);
    $sale  = (float)($item['product_sale']  ?? 0);
    $qty   = (int)  ($item['product_quantity'] ?? 0);
    $unit  = $price - ($price * $sale / 100);
    if ($unit < 0) $unit = 0;
    $total_amount += $unit * $qty;
}

/* Lưu SESSION để trang cảm ơn/preview hiển thị */
$_SESSION['phase2_checkout_preview'] = true;
$_SESSION['delivery_name']    = $delivery_name;
$_SESSION['delivery_address'] = $delivery_address;
$_SESSION['delivery_phone']   = $delivery_phone;
$_SESSION['delivery_note']    = $delivery_note;
$_SESSION['order_type']       = $order_type;     // chỉ để hiển thị
$_SESSION['total_amount']     = $total_amount;

/**
 * Phase 2 KHÔNG sinh mã đơn & KHÔNG insert DB.
 * Nếu bạn muốn hiển thị “mã đơn tạm”, có thể generate tạm trong session:
 */
$_SESSION['order_code_preview'] = 'P2-' . str_pad((string)random_int(0, 999999), 6, '0', STR_PAD_LEFT);

// Clear mode buynow sau khi dùng xong (không đụng tới giỏ hàng)
unset($_SESSION['checkout_mode'], $_SESSION['buynow']);

/* Điều hướng sang trang cảm ơn/preview
 * Gợi ý: dùng order_type=0 để phân biệt Phase 2 (preview)
 * Bạn có thể sửa trang thankiu để nếu phase2_checkout_preview=true thì hiển thị ở chế độ “demo/preview”.
 */
header('Location: ../../index.php?page=thankiu&order_type=0');
exit;
