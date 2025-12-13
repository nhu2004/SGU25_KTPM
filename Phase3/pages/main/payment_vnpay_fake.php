<?php
// Màn giả lập thanh toán VNPAY
if (session_status() === PHP_SESSION_NONE) {
    session_name('guha');
    session_start();
}

error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);

// ✅ Nhúng thư viện PHP QR Code (giống MoMo)
// File thư viện: pages/phpqrcode/qrlib.php
require_once __DIR__ . '/../phpqrcode/qrlib.php';

// Lấy thông tin đơn từ session
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

if (!function_exists('vnd_vnpay_fake')) {
    function vnd_vnpay_fake($n) {
        return number_format((float)$n, 0, ',', '.') . ' ₫';
    }
}

// ✅ Payload JSON encode vào QR (demo)
$qrPayloadArray = [
    'gateway'    => 'vnpay-demo',
    'order_code' => $display_code,
    'amount'     => $total_amount,
    'customer'   => $customer_name,
    'timestamp'  => time(),
];
$qrPayload = json_encode($qrPayloadArray, JSON_UNESCAPED_UNICODE);

// ✅ Sinh QR vào bộ nhớ rồi encode base64
ob_start();
QRcode::png($qrPayload, null, 0, 6, 1); 
$imageString = ob_get_clean();
$qrBase64    = base64_encode($imageString);
?>
<style>
    .vnp-page {
        padding: 60px 0;
        background: #f3f4f6; 
        font-family: 'Inter', system-ui, -apple-system, BlinkMacSystemFont, sans-serif;
    }

    /* POPUP WRAPPER  */
    .qr-vnp-popup {
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
    }
    .qr-vnp-popup:hover {
        transform: scale(1.02);
    }

    /* LEFT PANEL – nền xanh VNPAY  */
    .vnp-left {
        flex: 1;
        background: linear-gradient(135deg, #0d5cb6, #2563eb);
        color: #fff;
        padding: 30px 24px;
        display: flex;
        flex-direction: column;
        gap: 12px;
    }
    .vnp-left h2 {
        font-size: 26px;
        margin: 0 0 4px;
        font-weight: 700;
    }
    .vnp-left .vnp-brand {
        color: #ffdd57;
    }
    .vnp-left .vnp-greeting {
        font-size: 15px;
        opacity: 0.95;
    }
    .vnp-left p {
        margin: 6px 0;
        font-size: 15px;
        line-height: 1.5;
    }
    .vnp-left .vnp-order-info {
        margin-top: 12px;
        padding-top: 8px;
        border-top: 1px solid rgba(255,255,255,0.25);
        font-size: 14px;
    }
    .vnp-row {
        display: flex;
        justify-content: space-between;
        margin-bottom: 6px;
        font-size: 14px;
    }
    .vnp-label {
        color: rgba(255,255,255,0.85);
    }
    .vnp-value {
        font-weight: 600;
        color: #fff;
    }
    .vnp-amount {
        margin: 14px 0 4px;
        padding: 10px 12px;
        border-radius: 12px;
        background: rgba(255,255,255,0.12);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .vnp-amount-label {
        font-size: 13px;
        color: rgba(255,255,255,0.9);
    }
    .vnp-amount-value {
        font-size: 18px;
        font-weight: 700;
        color: #fff;
    }
    .vnp-countdown {
        font-size: 13px;
        color: #fee2e2;
        margin-top: 4px;
    }

    /* Nút ở đáy panel trái  */
    .vnp-left-bottom {
        margin-top: auto;
        padding-top: 20px;
        margin-bottom: 20px;
    }
    .vnp-left-bottom .vnp-btn {
        width: 100%;
    }

    /* RIGHT PANEL */
    .vnp-right {
        flex: 1.2;
        background: #fff;
        padding: 30px 16px;
        text-align: center;
        display: flex;
        flex-direction: column;
        align-items: center;
    }
    .vnp-right .vnp-logo {
        width: 80px;      
        margin-bottom: 10px;
        object-fit: contain;
        transition: transform 0.2s ease;
    }
    .vnp-right .vnp-logo:hover {
        transform: scale(1.05);
    }
    .vnp-right .qr-image {
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
    .vnp-right .qr-image:hover {
        transform: scale(1.03);
    }

    /* Khối đáy bên phải chứa nút */
    .vnp-right-bottom {
        margin-top: auto;
        margin-bottom: 20px;
        width: 100%;
        max-width: 360px;
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    .vnp-actions {
        width: 100%;
        display: flex;
        justify-content: center;
    }
    .vnp-actions-row {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .vnp-btn {
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

    /* Màu nút theo VNPAY */
    .vnp-btn-primary {
        background: #0d5cb6;
        color: #ffffff;
        border-color: #0d5cb6;
    }
    .vnp-btn-primary:hover {
        background: #0b4da0;
        border-color: #0b4da0;
    }
    .vnp-btn-secondary {
        background: #ffffff;
        color: #111827;
        border-color: #d1d5db;
    }
    .vnp-btn-secondary:hover {
        background: #f9fafb;
    }

    .vnp-footer {
        margin-top: 15px;
        font-size: 13px;
        color: #777;
        line-height: 1.4;
        text-align: center;
        width: 100%;
    }

    @media (max-width: 900px) {
        .qr-vnp-popup {
            width: 100%;
            height: auto;
            flex-direction: column;
        }
        .vnp-left, .vnp-right {
            padding: 20px 16px;
        }
        .vnp-right .qr-image {
            width: 220px;
            height: 220px;
        }
        .vnp-left-bottom {
            padding-top: 16px;
            margin-bottom: 16px;
        }
        .vnp-right-bottom {
            margin-bottom: 16px;
        }
    }

    @media (max-width: 480px) {
        .vnp-page {
            padding: 30px 0;
        }
    }
</style>

<section class="vnp-page">
    <div class="container">
        <div class="qr-vnp-popup">
            <!-- LEFT: thông tin đơn + nút hủy -->
            <div class="vnp-left">
                <h2>Thanh toán <span class="vnp-brand">VNPAY</span></h2>
                <p class="vnp-greeting">
                    Xin chào <strong><?php echo htmlspecialchars($customer_name, ENT_QUOTES, 'UTF-8'); ?></strong>
                </p>
                <p>
                    Vui lòng mở ứng dụng ngân hàng / VNPAY, chọn chức năng <strong>Quét mã</strong> và quét QR bên phải
                    để tiến hành thanh toán.
                </p>

                <div class="vnp-order-info">
                    <div class="vnp-row">
                        <span class="vnp-label">Mã đơn hàng</span>
                        <span class="vnp-value">
                            <?php echo $display_code !== '' ? htmlspecialchars($display_code, ENT_QUOTES, 'UTF-8') : '—'; ?>
                        </span>
                    </div>
                    <div class="vnp-row">
                        <span class="vnp-label">Số tiền cần thanh toán</span>
                        <span class="vnp-value"><?php echo vnd_vnpay_fake($total_amount); ?></span>
                    </div>
                    <div class="vnp-amount">
                        <div class="vnp-amount-label">Tổng thanh toán</div>
                        <div class="vnp-amount-value"><?php echo vnd_vnpay_fake($total_amount); ?></div>
                    </div>
                    <div class="vnp-countdown">
                        Thời gian hiệu lực: <span id="vnp-timer">15:00</span>
                    </div>
                </div>

                <!-- Nút hủy ở đáy -->
                <div class="vnp-left-bottom">
                    <button type="button"
                            class="vnp-btn vnp-btn-secondary"
                            onclick="window.location.href='index.php?page=cart';">
                        Hủy thanh toán &amp; quay lại giỏ hàng
                    </button>
                </div>
            </div>

            <!-- RIGHT: QR + NÚT THANH TOÁN THÀNH CÔNG -->
            <div class="vnp-right">
                <img src="assets/images/payment/vnpay.png"
                     alt="VNPAY"
                     class="vnp-logo"
                     onerror="this.style.display='none'">

                <!-- QR sinh động -->
                <img
                    src="data:image/png;base64,<?php echo $qrBase64; ?>"
                    alt="VNPAY QR"
                    class="qr-image"
                >

                <!-- Khối đáy giống momo-right-bottom -->
                <div class="vnp-right-bottom">
                    <div class="vnp-actions">
                        <a href="pages/handle/payment_result.php?gateway=vnpay&status=success&order_code=<?php echo urlencode((string)$order_code); ?>"
                           class="vnp-btn vnp-btn-primary">
                            Thanh toán thành công
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
    // Đếm ngược 15 phút (hiển thị là chính, giống MoMo)
    (function () {
        var remain = 15 * 60;
        var el = document.getElementById('vnp-timer');
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
