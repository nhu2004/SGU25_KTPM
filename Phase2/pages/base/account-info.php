<?php
// Bắt đầu session (đồng bộ với index.php)
if (session_status() === PHP_SESSION_NONE) {
    session_name('guha'); // nếu index.php cũng đặt 'guha' thì sẽ chung session
    session_start();
}

/* ====== Kết nối DB nếu chưa có ====== */
if (!isset($mysqli) || !($mysqli instanceof mysqli)) {
    $candidates = [
        __DIR__ . '/../../admin/config/config.php',
        __DIR__ . '/../../../admin/config/config.php',
        __DIR__ . '/admin/config/config.php',
    ];
    foreach ($candidates as $p) {
        if (is_file($p)) {
            require_once $p;
            break;
        }
    }
}
if (isset($mysqli) && $mysqli instanceof mysqli) {
    @$mysqli->set_charset('utf8mb4');
}

/* ====== Helper ====== */
function e($v) {
    return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');
}
function fetch_one(mysqli $db, string $sql, string $types = '', array $params = []): ?array {
    $stmt = $db->prepare($sql);
    if (!$stmt) return null;
    if ($types && $params) $stmt->bind_param($types, ...$params);
    if (!$stmt->execute()) {
        $stmt->close();
        return null;
    }
    $res = $stmt->get_result();
    $row = $res ? $res->fetch_assoc() : null;
    $stmt->close();
    return $row ?: null;
}

/* ====== Lấy thông tin account hiện tại ====== */
$accId    = (int)($_SESSION['account_id']    ?? 0);
$accEmail = (string)($_SESSION['account_email'] ?? '');

// Nếu chưa login thì đá về trang đăng nhập
if ($accId <= 0 && $accEmail === '') {
    header('Location: index.php?page=login');
    exit;
}

$row = null;
if ($accId > 0) {
    $row = fetch_one(
        $mysqli,
        "SELECT a.account_id,
                a.account_name,
                a.account_email,
                a.account_phone,
                c.customer_address,
                c.customer_gender
         FROM account a
         LEFT JOIN customer c ON c.account_id = a.account_id
         WHERE a.account_id = ?
         LIMIT 1",
        "i",
        [$accId]
    );
}

if (!$row && $accEmail !== '') {
    // fallback theo email nếu vì lý do gì đó không có account_id
    $row = fetch_one(
        $mysqli,
        "SELECT a.account_id,
                a.account_name,
                a.account_email,
                a.account_phone,
                c.customer_address,
                c.customer_gender
         FROM account a
         LEFT JOIN customer c ON c.account_id = a.account_id
         WHERE a.account_email = ?
         LIMIT 1",
        "s",
        [$accEmail]
    );
}

// Nếu vẫn không có bản ghi => coi như chưa đăng nhập hợp lệ
if (!$row) {
    header('Location: index.php?page=login');
    exit;
}

/* ====== Map dữ liệu ra biến view ====== */
$name    = $row['account_name']      ?? '';
$email   = $row['account_email']     ?? '';
$phone   = $row['account_phone']     ?? '';
$address = $row['customer_address']  ?? '';
$gCode   = isset($row['customer_gender']) ? (int)$row['customer_gender'] : 0;

if ($gCode === 1)      $genderText = 'Nam';
elseif ($gCode === 2)  $genderText = 'Nữ';
else                   $genderText = 'Chưa xác định';
?>

<section class="account-info pd-section">
    <div class="container">
        <h2 class="h3 mg-bottom-20">Thông tin tài khoản</h2>

        <div class="account-info__box">
            <div class="account-info__row d-flex">
                <div class="account-info__label">Tên khách hàng:</div>
                <div class="account-info__value"><?php echo e($name); ?></div>
            </div>

            <div class="account-info__row d-flex">
                <div class="account-info__label">Email:</div>
                <div class="account-info__value"><?php echo e($email); ?></div>
            </div>

            <div class="account-info__row d-flex">
                <div class="account-info__label">Số điện thoại:</div>
                <div class="account-info__value"><?php echo e($phone); ?></div>
            </div>

            <div class="account-info__row d-flex">
                <div class="account-info__label">Địa chỉ:</div>
                <div class="account-info__value"><?php echo e($address); ?></div>
            </div>

            <div class="account-info__row d-flex">
                <div class="account-info__label">Giới tính:</div>
                <div class="account-info__value"><?php echo e($genderText); ?></div>
            </div>

            <div class="account-info__row d-flex justify-end">
                <a class="h6 account-info__edit" href="index.php?page=my_account&tab=change_info">
                    Thay đổi thông tin
                </a>
            </div>
        </div>
    </div>
</section>

<style>
    /* Thêm style nhỏ cho cái khung – để chắc chắn luôn có box */
    .account-info__box{
        border: 1px solid #eee;
        border-radius: 4px;
        padding: 24px 32px;
        background: #fff;
        box-shadow: 0 1px 2px rgba(0,0,0,.03);
    }
    .account-info__row{
        padding: 10px 0;
        border-bottom: 1px solid #f1f1f1;
    }
    .account-info__row:last-child{
        border-bottom: none;
    }
    .account-info__label{
        min-width: 120px;
        font-weight: 500;
        margin-right: 16px;
    }
    .account-info__value{
        flex: 1;
    }
    .account-info__edit{
        display: inline-block;
        margin-top: 10px;
    }
</style>
