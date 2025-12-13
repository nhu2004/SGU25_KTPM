<?php
// Phase 3 – Thank You (GIẢ LẬP THANH TOÁN)

// Đồng bộ session với toàn site (guha)
if (session_status() === PHP_SESSION_NONE) {
    session_name('guha');
    session_start();
}

error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);

// Helper format tiền
if (!function_exists('vnd')) {
    function vnd($n) {
        return number_format((float)$n, 0, ',', '.') . ' ₫';
    }
}

// Mặc định: thất bại
$status = 0;

// Lấy thông tin đơn từ session
$orderSummary = $_SESSION['order_summary'] ?? null;

if ($orderSummary && is_array($orderSummary)) {
    $status          = 1;
    $order_code      = (int)($orderSummary['order_code']       ?? 0); // INT
    $delivery_name   = (string)($orderSummary['delivery_name']    ?? '');
    $delivery_phone  = (string)($orderSummary['delivery_phone']   ?? '');
    $delivery_addr   = (string)($orderSummary['delivery_address'] ?? '');
    $delivery_note   = (string)($orderSummary['delivery_note']    ?? '');
    $payment_method  = (string)($orderSummary['payment_method']   ?? '');
    $is_paid         = (int)   ($orderSummary['is_paid']          ?? 0);
    $total_amount    = (float) ($orderSummary['total_amount']     ?? 0);
    $items           = (array) ($orderSummary['items']            ?? []);
} else {
    $order_code = 0;
    $delivery_name = $delivery_phone = $delivery_addr = $delivery_note = $payment_method = '';
    $is_paid = 0;
    $total_amount = 0;
    $items = [];
}

// Chuẩn bị mã hiển thị (có tiền tố ORD)
$display_code = $order_code > 0 ? 'ORD' . $order_code : '';
?>
<?php if ($status == 1): ?>
    <!-- ĐƠN HÀNG GIẢ LẬP THÀNH CÔNG -->
    <section class="thankiu">
        <div class="container">
            <div class="thankiu__box text-center">
                <div class="thankiu_image">
                    <img src="assets/images/icon/icon-success.gif" alt="success">
                </div>
                <h1 class="thankiu__heading h2">Đặt hàng thành công</h1>
                <span class="thankiu__heading2 h3">Cảm ơn quý khách đã mua hàng tại Guha Perfume</span>

                <p class="thankiu__description">
                    Đơn hàng của quý khách đã được tiếp nhận và đang trong thời gian xử lý.
                    Chúng tôi sẽ thông báo đến quý khách ngay khi hàng chuẩn bị được giao.
                    <?php if ($display_code !== ''): ?>
                        <br>Mã đơn hàng: <strong><?php echo htmlspecialchars($display_code, ENT_QUOTES, 'UTF-8'); ?></strong>
                    <?php endif; ?>
                    <?php if (!empty($payment_method)): ?>
                        <br>Phương thức thanh toán: <strong><?php echo htmlspecialchars($payment_method, ENT_QUOTES, 'UTF-8'); ?></strong>
                    <?php endif; ?>
                    <?php if ($total_amount > 0): ?>
                        <br>Tổng tiền: <strong><?php echo vnd($total_amount); ?></strong>
                    <?php endif; ?>
                </p>

                <div class="thankiu_link">
                    <a href="index.php" class="btn btn__outline">Trang chủ</a>

                    <?php if ($order_code > 0): ?>
                        <!-- Đi thẳng tới trang chi tiết đơn hàng (dùng mã số thật trong DB) -->
                        <a href="index.php?page=order_detail&order_code=<?php echo urlencode((string)$order_code); ?>" class="btn btn__outline">
                            Xem chi tiết
                        </a>
                    <?php else: ?>
                        <a href="index.php?page=my_account&tab=account_order" class="btn btn__outline">
                            Xem chi tiết
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>

    <?php
    // Sau khi hiển thị, clear giỏ + tóm tắt đơn để lần F5 không hiện lại
    unset($_SESSION['order_summary']);
    unset($_SESSION['cart']);
    ?>

<?php else: ?>
    <!-- TRƯỜNG HỢP KHÔNG CÓ DỮ LIỆU ĐƠN / LỖI -->
    <section class="thankiu">
        <div class="container">
            <div class="thankiu__box text-center">
                <div class="thankiu_image">
                    <img src="assets/images/icon/icon-error.gif" alt="error">
                </div>
                <h1 class="thankiu__heading heading--wanning h2">Giao dịch thất bại</h1>
                <span class="thankiu__heading2 h3">Thanh toán không thành công hoặc không tìm thấy đơn hàng</span>
                <p class="thankiu__description">
                    Quý khách vui lòng thực hiện đặt hàng lại hoặc chọn phương thức khác.
                    Các sản phẩm hiện vẫn còn trong giỏ hàng (nếu có).
                </p>
                <div class="thankiu_link">
                    <a href="index.php" class="btn btn__outline">Trang chủ</a>
                    <a href="index.php?page=cart" class="btn btn__outline">Xem giỏ hàng</a>
                </div>
            </div>
        </div>
    </section>
<?php endif; ?>
