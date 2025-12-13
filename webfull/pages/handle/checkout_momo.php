<?php
// Tuyệt đối không có BOM/space trước thẻ mở PHP
declare(strict_types=1);

header('Content-Type: text/html; charset=utf-8');
session_start();
include('../../admin/config/config.php');

/**
 * Gửi POST JSON và trả về mảng:
 * [
 *   'ok' => bool,
 *   'http_code' => int|null,
 *   'raw' => string|null,
 *   'curl_error' => string|null,
 * ]
 */
function execPostRequest(string $url, string $jsonBody): array
{
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_CUSTOMREQUEST   => 'POST',
        CURLOPT_POSTFIELDS      => $jsonBody,
        CURLOPT_RETURNTRANSFER  => true,
        CURLOPT_HTTPHEADER      => [
            'Content-Type: application/json',
            'Content-Length: ' . strlen($jsonBody),
        ],
        CURLOPT_TIMEOUT         => 15, // tăng timeout cho chắc
        CURLOPT_CONNECTTIMEOUT  => 10,
    ]);

    $raw = curl_exec($ch);
    $httpCode = null;
    if ($raw !== false) {
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    }
    $curlErr = ($raw === false) ? curl_error($ch) : null;
    curl_close($ch);

    return [
        'ok'        => ($raw !== false),
        'http_code' => $httpCode,
        'raw'       => $raw !== false ? $raw : null,
        'curl_error'=> $curlErr,
    ];
}

// ============== MoMo test config ==============
$endpoint   = "https://test-payment.momo.vn/v2/gateway/api/create";
$partnerCode= 'MOMOBKUN20180529';
$accessKey  = 'klm05TvNBzhg7h7j';
$secretKey  = 'at67qH6mk8w5Y1nAyMoYKMWACiEi2bsa';

// ============== Dữ liệu đơn hàng ==============
$orderInfo  = "Thanh toán qua MoMo";

// Lấy từ session, có kiểm tra
$amount     = isset($_SESSION['total_amount']) ? (string)$_SESSION['total_amount'] : '';
$orderId    = isset($_SESSION['order_code'])   ? (string)$_SESSION['order_code']   : '';

// Bạn nên dùng HTTPS public URL cho redirect & ipn (test có thể chấp nhận http, nhưng khuyến nghị https)
$redirectUrl= "http://thinhdh.com/guhastorephp/index.php?page=checkout";
$ipnUrl     = "http://thinhdh.com/guhastorephp/index.php?page=checkout";
$extraData  = "";

// Validate sớm để tránh lỗi null
if ($amount === '' || $orderId === '') {
    // Không echo/var_dump để khỏi phá header; ghi log và chuyển hướng an toàn
    error_log("[MoMo] Missing session data: amount='{$amount}', orderId='{$orderId}'");
    // Chuyển về trang checkout kèm thông báo (tùy bạn xử lý ở UI bằng query string)
    header('Location: ' . $redirectUrl . '&momo_error=missing_session');
    exit;
}

// ============== Tạo chữ ký & payload ==============
$requestId  = (string) time();
$requestType= "captureWallet";

// raw string để ký
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

$signature = hash_hmac("sha256", $rawHash, $secretKey);

$payload = [
    'partnerCode' => $partnerCode,
    'partnerName' => "Test",
    'storeId'     => "MomoTestStore",
    'requestId'   => $requestId,
    'amount'      => $amount,
    'orderId'     => $orderId,
    'orderInfo'   => $orderInfo,
    'redirectUrl' => $redirectUrl,
    'ipnUrl'      => $ipnUrl,
    'lang'        => 'vi',
    'extraData'   => $extraData,
    'requestType' => $requestType,
    'signature'   => $signature,
];

$jsonBody = json_encode($payload, JSON_UNESCAPED_UNICODE);

// ============== Gọi MoMo ==============
$res = execPostRequest($endpoint, $jsonBody);

// Nếu cURL lỗi hoặc HTTP code khác 200 → log & quay về
if (!$res['ok'] || (int)$res['http_code'] !== 200) {
    error_log("[MoMo] cURL/HTTP error. http_code=" . var_export($res['http_code'], true)
        . " curl_error=" . var_export($res['curl_error'], true)
        . " response=" . substr((string)$res['raw'], 0, 1000));
    header('Location: ' . $redirectUrl . '&momo_error=http_error');
    exit;
}

// Giải mã JSON
$jsonResult = json_decode((string)$res['raw'], true);

// Kiểm tra JSON decode hợp lệ và có payUrl
if (!is_array($jsonResult)) {
    error_log("[MoMo] JSON decode failed. Raw=" . substr((string)$res['raw'], 0, 1000));
    header('Location: ' . $redirectUrl . '&momo_error=json_decode');
    exit;
}

// MoMo trả resultCode === 0 là OK (theo docs). Kiểm tra thêm payUrl.
if (
    isset($jsonResult['resultCode']) &&
    (int)$jsonResult['resultCode'] === 0 &&
    !empty($jsonResult['payUrl'])
) {
    // Thành công → redirect qua payUrl
    header('Location: ' . $jsonResult['payUrl']);
    exit;
}

// Nếu thất bại → log chi tiết để tra
error_log("[MoMo] API error. result=" . json_encode($jsonResult));
header('Location: ' . $redirectUrl . '&momo_error=api_error');
exit;
