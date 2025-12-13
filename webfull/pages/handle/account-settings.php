<?php
// Đồng bộ session
if (session_status() === PHP_SESSION_NONE) {
    session_name('guha');
    session_start();
}

/* Kết nối DB */
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
if (isset($mysqli) && $mysqli instanceof mysqli) {
    @$mysqli->set_charset('utf8mb4');
}

$accId    = (int)($_SESSION['account_id']    ?? 0);
$accEmail = (string)($_SESSION['account_email'] ?? '');

if ($accId <= 0) {
    header('Location: ../../index.php?page=login');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['info_change'])) {

    $name    = trim($_POST['customer_name']    ?? '');
    $phone   = trim($_POST['customer_phone']   ?? '');
    $address = trim($_POST['customer_address'] ?? '');
    $gCode   = isset($_POST['customer_gender']) ? (int)$_POST['customer_gender'] : 0;

    if ($name !== '' && $phone !== '' && isset($mysqli) && $mysqli instanceof mysqli) {

        // 1. Cập nhật bảng account (name + phone)
        $stmt = $mysqli->prepare(
            "UPDATE account
             SET account_name = ?, account_phone = ?
             WHERE account_id = ?"
        );
        if ($stmt) {
            $stmt->bind_param("ssi", $name, $phone, $accId);
            $stmt->execute();
            $stmt->close();
        }

        // 2. Upsert vào bảng customer
        $stmt = $mysqli->prepare("SELECT customer_id FROM customer WHERE account_id = ? LIMIT 1");
        $hasCustomer = false;
        if ($stmt) {
            $stmt->bind_param("i", $accId);
            $stmt->execute();
            $res = $stmt->get_result();
            $hasCustomer = (bool)$res->fetch_assoc();
            $stmt->close();
        }

        if ($hasCustomer) {
            // update
            $stmt = $mysqli->prepare(
                "UPDATE customer
                 SET customer_name   = ?,
                     customer_phone  = ?,
                     customer_address= ?,
                     customer_gender = ?,
                     customer_email  = ?
                 WHERE account_id = ?"
            );
            if ($stmt) {
                $stmt->bind_param("sssisi", $name, $phone, $address, $gCode, $accEmail, $accId);
                $stmt->execute();
                $stmt->close();
            }
        } else {
            // insert mới
            $stmt = $mysqli->prepare(
                "INSERT INTO customer
                    (customer_name, customer_phone, customer_address, customer_gender, customer_email, account_id)
                 VALUES (?,?,?,?,?,?)"
            );
            if ($stmt) {
                $stmt->bind_param("sssisi", $name, $phone, $address, $gCode, $accEmail, $accId);
                $stmt->execute();
                $stmt->close();
            }
        }

        // 3. Cập nhật session cho tiện
        $_SESSION['account_name']    = $name;
        $_SESSION['account_phone']   = $phone;
        $_SESSION['account_address'] = $address;
    }

    // Quay lại trang thông tin tài khoản
    header('Location: ../../index.php?page=my_account&tab=account_info&message=update_success');
    exit;
}

// Nếu vào file mà không POST hợp lệ -> về lại my_account
header('Location: ../../index.php?page=my_account&tab=account_info');
exit;
