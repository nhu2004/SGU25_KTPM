<?php
// Màn giả lập thanh toán MOMO
if (session_status() === PHP_SESSION_NONE) {
    session_name('guha');
    session_start();
}

error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);

// ✅ Nhúng thư viện PHP QR Code
// File thư viện: Phase3/pages/phpqrcode/qrlib.php
// File hiện tại: Phase3/pages/base/payment_momo_fake.php
require_once __DIR__ . '/../phpqrcode/qrlib.php';

$orderSummary = $_SESSION['order_summary'] ?? null;

if (!$orderSummary || !is_array($orderSummary)) {
    // Không có thông tin đơn -> quay về giỏ
    ?>
    <section class="checkout pd-section">
        <div class="container">
            <div class="thankiu__box text-center">
                <h2 class="h3">Không tìm thấy thông tin đơn hàng</h2>
                <p>Vui lòng thực hiện đặt hàng lại.</p>
                <a href="index.php?page=cart" class="btn btn__outline">Về giỏ hàng</a>
            </div>
        </div>
    </section>
    <?php
    return;
}

$order_code    = (int)($orderSummary['order_code'] ?? 0);
$display_code  = $order_code > 0 ? 'ORD' . $order_code : '';
$total_amount  = (float)($orderSummary['total_amount'] ?? 0);
$customer_name = isset($_SESSION['account_name']) ? (string)$_SESSION['account_name'] : '';
$customer_name = $customer_name !== '' ? $customer_name : 'Quý khách';

if (!function_exists('vnd_momo_fake')) {
    function vnd_momo_fake($n) {
        return number_format((float)$n, 0, ',', '.') . ' ₫';
    }
}

// ✅ Tạo payload sẽ encode vào QR (dùng JSON cho dễ trình bày đồ án)
$qrPayloadArray = [
    'gateway'    => 'momo-demo',
    'order_code' => $display_code,
    'amount'     => $total_amount,
    'customer'   => $customer_name,
    'timestamp'  => time(),
];

$qrPayload = json_encode($qrPayloadArray, JSON_UNESCAPED_UNICODE);

// ✅ Sinh QR vào bộ nhớ rồi encode base64 để nhúng vào <img>
ob_start();
// Tham số 3 = 0: mức sửa lỗi L (Low), 6: kích thước, 1: margin
QRcode::png($qrPayload, null, 0, 6, 1);
$imageString = ob_get_clean();
$qrBase64    = base64_encode($imageString);
?>
<style>
    .momo-page {
        padding: 60px 0;
        background: #f5f5f5;
        font-family: 'Inter', system-ui, -apple-system, BlinkMacSystemFont, sans-serif;
    }

    /* POPUP WRAPPER */
    .qr-momo-popup {
        display: flex;
        width: 900px;
        height: 600px;
        max-width: 100%;
        margin: 0 auto;
        background: #fff;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 16px 40px rgba(0, 0, 0, 0.25);
        transition: transform 0.3s ease;
        /* align-items: stretch mặc định -> panel tím phủ full chiều cao */
    }
    .qr-momo-popup:hover {
        transform: scale(1.02);
    }

    /* LEFT PANEL */
    .momo-left {
        flex: 1;
        background: linear-gradient(135deg, #a22d86, #c43bb8); /* màu đặc trưng MoMo */
        color: #fff;
        padding: 30px 24px;
        display: flex;
        flex-direction: column;
        gap: 12px;
    }
    .momo-left h2 {
        font-size: 26px;
        margin: 0 0 4px;
        font-weight: 700;
    }
    .momo-left .momo-brand {
        color: #ffe066;
    }
    .momo-left .momo-greeting {
        font-size: 15px;
        opacity: 0.95;
    }
    .momo-left p {
        margin: 6px 0;
        font-size: 15px;
        line-height: 1.5;
    }
    .momo-left .momo-order-info {
        margin-top: 12px;
        padding-top: 8px;
        border-top: 1px solid rgba(255,255,255,0.25);
        font-size: 14px;
    }
    .momo-row {
        display: flex;
        justify-content: space-between;
        margin-bottom: 6px;
        font-size: 14px;
    }
    .momo-label {
        color: rgba(255,255,255,0.8);
    }
    .momo-value {
        font-weight: 600;
        color: #fff;
    }
    .momo-amount {
        margin: 14px 0 4px;
        padding: 10px 12px;
        border-radius: 12px;
        background: rgba(255,255,255,0.12);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .momo-amount-label {
        font-size: 13px;
        color: rgba(255,255,255,0.85);
    }
    .momo-amount-value {
        font-size: 18px;
        font-weight: 700;
        color: #fff;
    }
    .momo-countdown {
        font-size: 13px;
        color: #ffe4e6;
        margin-top: 4px;
    }

    /* Nút ở đáy panel trái (giống VNPay) */
    .momo-left-bottom {
        margin-top: auto;
        padding-top: 20px;
        margin-bottom: 20px; /* căn đáy */
    }
    .momo-left-bottom .momo-btn {
        width: 100%;
    }

    /* RIGHT PANEL */
    .momo-right {
        flex: 1.2;
        background: #fff;
        padding: 30px 16px;
        text-align: center;
        display: flex;
        flex-direction: column;
        align-items: center;
    }
    .momo-right .momo-logo {
        width: 80px;
        margin-bottom: 10px;
        transition: transform 0.2s ease;
    }
    .momo-right .momo-logo:hover {
        transform: scale(1.05);
    }
    .momo-right .qr-image {
        width: 280px;
        height: 280px;
        margin: 15px 0 8px;
        border-radius: 16px;
        border: 2px solid #e0e0e0;
        box-shadow: 0 8px 20px rgba(0,0,0,0.1);
        transition: transform 0.2s ease;
        object-fit: contain;
        background: #fff;
    }
    .momo-right .qr-image:hover {
        transform: scale(1.03);
    }

    /* Khối đáy bên phải chứa nút + footer, để căn ngang với bên trái */
    .momo-right-bottom {
        margin-top: auto;
        margin-bottom: 20px;
        width: 100%;
        max-width: 360px;
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    .momo-actions {
        width: 100%;
        display: flex;
        justify-content: center;
    }
    .momo-actions-row {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .momo-btn {
        width: 100%;
        padding: 11px 16px;
        border-radius: 999px;
        font-size: 14px;
        font-weight: 600;
        border: 1px solid transparent;
        cursor: pointer;
        text-align: center;
        text-decoration: none;
        transition: background 0.15s ease-in-out, color 0.15s ease-in-out,
                    border-color 0.15s ease-in-out, opacity 0.15s ease-in-out;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }
    /* Nút chính: hồng tím MoMo */
    .momo-btn-primary {
        background: #a22d86;
        color: #ffffff;
        border-color: #a22d86;
    }
    .momo-btn-primary:hover {
        background: #8d2774;
        border-color: #8d2774;
    }
    /* Nút hủy: nền trắng, viền nhạt */
    .momo-btn-secondary {
        background: #ffffff;
        color: #111827;
        border-color: #d1d5db;
    }
    .momo-btn-secondary:hover {
        background: #f9fafb;
    }

    .momo-footer {
        margin-top: 15px;
        font-size: 13px;
        color: #777;
        line-height: 1.4;
        text-align: center;
        width: 100%;
    }

    @media (max-width: 900px) {
        .qr-momo-popup {
            width: 100%;
            height: auto;
            flex-direction: column;
        }
        .momo-left, .momo-right {
            padding: 20px 16px;
        }
        .momo-right .qr-image {
            width: 220px;
            height: 220px;
        }
        .momo-left-bottom {
            padding-top: 16px;
            margin-bottom: 16px;
        }
        .momo-right-bottom {
            margin-bottom: 16px;
        }
    }

    @media (max-width: 480px) {
        .momo-page {
            padding: 30px 0;
        }
    }
</style>

<section class="momo-page">
    <div class="container">
        <div class="qr-momo-popup">
            <!-- LEFT -->
            <div class="momo-left">
                <h2>Thanh toán <span class="momo-brand">MoMo</span></h2>
                <p class="momo-greeting">
                    Xin chào <strong><?php echo htmlspecialchars($customer_name, ENT_QUOTES, 'UTF-8'); ?></strong>
                </p>
                <p>
                    Vui lòng mở ứng dụng MoMo, chọn chức năng <strong>Quét mã</strong> và quét QR bên phải
                    để tiến hành thanh toán.
                </p>

                <div class="momo-order-info">
                    <div class="momo-row">
                        <span class="momo-label">Mã đơn hàng</span>
                        <span class="momo-value">
                            <?php echo $display_code !== '' ? htmlspecialchars($display_code, ENT_QUOTES, 'UTF-8') : '—'; ?>
                        </span>
                    </div>
                    <div class="momo-row">
                        <span class="momo-label">Số tiền cần thanh toán</span>
                        <span class="momo-value"><?php echo vnd_momo_fake($total_amount); ?></span>
                    </div>

                    <div class="momo-amount">
                        <div class="momo-amount-label">Tổng thanh toán</div>
                        <div class="momo-amount-value"><?php echo vnd_momo_fake($total_amount); ?></div>
                    </div>
                    <div class="momo-countdown">
                        Thời gian hiệu lực: <span id="momo-timer">15:00</span>
                    </div>
                </div>

                <!-- Nút hủy nằm ở đáy panel trái -->
                <div class="momo-left-bottom">
                    <button type="button"
                            class="momo-btn momo-btn-secondary"
                            onclick="window.location.href='index.php?page=cart';">
                        Hủy thanh toán &amp; quay lại giỏ hàng
                    </button>
                </div>
            </div>

            <!-- RIGHT -->
            <div class="momo-right">
                <img src="assets/images/payment/momo.png"
                     alt="MoMo"
                     class="momo-logo"
                     onerror="this.style.display='none'">

                <!-- ✅ QR sinh bằng PHP, không dùng ảnh tĩnh -->
                <img
                    src="data:image/png;base64,<?php echo $qrBase64; ?>"
                    alt="MoMo QR"
                    class="qr-image"
                >

                <!-- Đáy bên phải: nút + footer (ngang hàng nút bên trái) -->
                <div class="momo-right-bottom">
                    <div class="momo-actions">
                        <a href="pages/handle/payment_result.php?gateway=momo&status=success&order_code=<?php echo urlencode((string)$order_code); ?>"
                           class="momo-btn momo-btn-primary">
                            Thanh toán thành công
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
    // Đếm ngược 15 phút (hiển thị là chính)
    (function () {
        var remain = 15 * 60; // 15 phút
        var el = document.getElementById('momo-timer');
        if (!el) return;

        function tick() {
            var m = Math.floor(remain / 60);
            var s = remain % 60;
            el.textContent = String(m).padStart(2, '0') + ':' + String(s).padStart(2, '0');
            if (remain > 0) {
                remain--;
                setTimeout(tick, 1000);
            } else {
                el.textContent = 'Hết hạn';
            }
        }
        tick();
    })();
</script>
