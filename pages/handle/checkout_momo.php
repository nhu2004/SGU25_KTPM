<?php
declare(strict_types=1);
header('Content-type: text/html; charset=utf-8');
session_start();

// ⚠️ Nếu file config có echo/print => dễ gây "Headers already sent"
// include('../../admin/config/config.php'); // Chỉ include khi cần và đảm bảo không output gì

/**
 * Phase3 - MoMo Create Payment (standalone từ checkout)
 * - Không phụ thuộc router
 * - Tự build redirectUrl/ipnUrl theo URL đang chạy
 * - Redirect sang payUrl nếu tạo giao dịch thành công
 */

// ====== MoMo SANDBOX CONFIG ======
$endpoint    = 'https://test-payment.momo.vn/v2/gateway/api/create';
$partnerCode = 'MOMOBKUN20180529';
$accessKey   = 'klm05TvNBzhg7h7j';
$secretKey   = 'at67qH6mk8w5Y1nAyMoYKMWACiEi2bsa';

// ====== Build base URL tự động (dù bạn truy cập có /Phase3/ hay không) ======
$scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host   = $_SERVER['HTTP_HOST'];

// File hiện tại: /pages/handle/checkout_momo.php  → lên 1 cấp là /pages
$pagesBasePath = rtrim(dirname(dirname($_SERVER['PHP_SELF'])), '/\\');
if ($pagesBasePath === '') $pagesBasePath = '/';
$appPagesBase  = $scheme . '://' . $host . $pagesBasePath;

// Trang user quay về sau thanh toán (UI) – bạn đã có: /pages/main/thankiu.php
$redirectUrl   = $appPagesBase . '/main/thankiu.php';
// Endpoint IPN server-side (nếu chưa có, tạo /pages/handle/momo_ipn.php theo mẫu mình gửi trước)
$ipnUrl        = $appPagesBase . '/handle/momo_ipn.php';

// ====== Dữ liệu đơn hàng ======
$orderInfo   = 'Thanh toán đơn hàng nước hoa';
$requestType = 'captureWallet';
$lang        = 'vi';
$extraData   = '';

// Lấy từ session (cho phép test 10000 nếu chưa có)
$amount  = $_SESSION['total_amount'] ?? null;
$orderId = $_SESSION['order_code']   ?? null;

if ($amount === null || !preg_match('/^\d+$/', (string)$amount) || (int)$amount <= 0) {
    $amount = '10000'; // test
}
if ($orderId === null || trim((string)$orderId) === '') {
    $orderId = 'ORD' . time();
}

// requestId duy nhất
$requestId = (string)time() . '-' . bin2hex(random_bytes(4));

// ====== Ký chữ ký ======
$rawHash = "accessKey={$accessKey}"
    . "&amount={$amount}"
    . "&extraData={$extraData}"
    . "&ipnUrl={$ipnUrl}"
    . "&orderId={$orderId}"
    . "&orderInfo={$orderInfo}"
    . "&partnerCode={$partnerCode}"
    . "&redirectUrl={$redirectUrl}"
    . "&requestId={$requestId}"
    . "&requestType={$requestType}";
$signature = hash_hmac('sha256', $rawHash, $secretKey);

// ====== Payload ======
$payload = [
    'partnerCode' => $partnerCode,
    'partnerName' => 'PerfumePhase3',
    'storeId'     => 'PerfumeP3',
    'requestId'   => $requestId,
    'amount'      => (string)$amount,
    'orderId'     => (string)$orderId,
    'orderInfo'   => $orderInfo,
    'redirectUrl' => $redirectUrl,
    'ipnUrl'      => $ipnUrl,
    'lang'        => $lang,
    'extraData'   => $extraData,
    'requestType' => $requestType,
    'signature'   => $signature,
];

// ====== Gọi MoMo ======
[$ok, $httpCode, $body] = momoPostJson($endpoint, $payload);

// ====== Xử lý phản hồi ======
if (!$ok) {
    http_response_code(502);
    echo "<h2 style='color:red;text-align:center;margin-top:40px;'>Không kết nối được MoMo (HTTP {$httpCode})</h2>";
    echo "<pre style='width:80%;margin:20px auto;background:#f9f9f9;border:1px solid #ccc;padding:10px;'>"
       . htmlspecialchars($body, ENT_QUOTES|ENT_SUBSTITUTE|ENT_HTML5)
       . "</pre>";
    exit;
}

$res = json_decode($body, true);
if (!is_array($res)) {
    http_response_code(502);
    echo "<h2 style='color:red;text-align:center;margin-top:40px;'>Phản hồi không hợp lệ từ MoMo</h2>";
    echo "<pre style='width:80%;margin:20px auto;background:#f9f9f9;border:1px solid #ccc;padding:10px;'>"
       . htmlspecialchars($body, ENT_QUOTES|ENT_SUBSTITUTE|ENT_HTML5)
       . "</pre>";
    exit;
}

if (!empty($res['payUrl']) && ($res['resultCode'] ?? 1) === 0) {
    if (headers_sent($f, $l)) {
        echo "<b>Headers already sent</b> in $f:$l";
        exit;
    }
    // (Tuỳ chọn) Lưu trạng thái pending đơn hàng ở DB tại đây
    header('Location: ' . $res['payUrl']);
    exit;
}

// Không có payUrl → in response để debug
http_response_code(400);
echo "<h2 style='color:red;text-align:center;margin-top:40px;'>Tạo giao dịch thất bại</h2>";
echo "<pre style='width:80%;margin:20px auto;background:#f9f9f9;border:1px solid #ccc;padding:10px;'>"
   . htmlspecialchars(json_encode($res, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE), ENT_QUOTES|ENT_SUBSTITUTE|ENT_HTML5)
   . "</pre>";
exit;

// ====== Helper ======
function momoPostJson(string $url, array $data): array {
    $ch   = curl_init($url);
    $json = json_encode($data, JSON_UNESCAPED_UNICODE);

    curl_setopt_array($ch, [
        CURLOPT_CUSTOMREQUEST  => 'POST',
        CURLOPT_POSTFIELDS     => $json,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER     => [
            'Content-Type: application/json',
            'Content-Length: ' . strlen($json),
        ],
        CURLOPT_TIMEOUT        => 30,   // quan trọng
        CURLOPT_CONNECTTIMEOUT => 10,
        CURLOPT_SSL_VERIFYPEER => true,
        CURLOPT_SSL_VERIFYHOST => 2,
        CURLOPT_FAILONERROR    => false,
    ]);

    $body = curl_exec($ch);
    if ($body === false) {
        $err = curl_error($ch);
        curl_close($ch);
        return [false, 0, json_encode(['curl_error' => $err], JSON_UNESCAPED_UNICODE)];
    }
    $httpCode = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    $ok = ($httpCode >= 200 && $httpCode < 300);
    return [$ok, $httpCode, $body];
}
