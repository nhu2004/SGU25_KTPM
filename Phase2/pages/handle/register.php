<?php
// pages/handle/register.php

// Đồng bộ session với index.php
if (session_status() === PHP_SESSION_NONE) {
    session_name('guha');
    session_start();
}

// Kết nối DB
require_once __DIR__ . '/../../admin/config/config.php';
$mysqli->set_charset('utf8mb4');

/**
 * Helper: quay về trang register với message
 */
function back_register(string $status = 'error') {
    header('Location: ../../index.php?page=register&message=' . urlencode($status));
    exit;
}

// 👉 CHỈ CẦN KIỂM TRA METHOD LÀ POST, KHÔNG CHECK $_POST['register'] NỮA
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    back_register('error');
}

// Lấy dữ liệu từ form
$name       = trim($_POST['account_name'] ?? '');
$email      = trim($_POST['account_email'] ?? '');
$phone      = trim($_POST['account_phone'] ?? '');
$address    = trim($_POST['customer_address'] ?? '');
$password   = trim($_POST['account_password'] ?? '');
$password2  = trim($_POST['account_password_confirn'] ?? '');
$gender     = isset($_POST['account_gender']) ? (int)$_POST['account_gender'] : 0;

// Validate sơ bộ
if ($name === '' || $email === '' || $phone === '' || $address === '' || $password === '' || $password2 === '') {
    back_register('invalid');     // thiếu thông tin
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    back_register('invalid_email');
}

if ($password !== $password2) {
    back_register('password_mismatch');
}

// Kiểm tra email đã tồn tại chưa
$stmt = $mysqli->prepare("SELECT account_id FROM account WHERE account_email = ? LIMIT 1");
if (!$stmt) back_register('error');

$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    // Email đã tồn tại
    $stmt->close();
    back_register('exists');      // message=exists
}
$stmt->close();

// Hash mật khẩu
$hash = md5($password); // (có thể đổi sang password_hash nếu muốn an toàn hơn)

// ================== INSERT ACCOUNT ==================
$stmt = $mysqli->prepare("
    INSERT INTO account
    (account_name, account_password, account_email, account_phone, account_type, account_status)
    VALUES (?, ?, ?, ?, 0, 1)
");
if (!$stmt) back_register('error');

$stmt->bind_param("ssss", $name, $hash, $email, $phone);

if (!$stmt->execute()) {
    $stmt->close();
    back_register('error');
}

$account_id = (int)$stmt->insert_id;
$stmt->close();

// ================== INSERT CUSTOMER ==================
$stmt = $mysqli->prepare("
    INSERT INTO customer
    (customer_name, customer_email, customer_address, customer_phone, customer_gender, account_id)
    VALUES (?, ?, ?, ?, ?, ?)
");
if (!$stmt) back_register('error');

$stmt->bind_param("ssssii", $name, $email, $address, $phone, $gender, $account_id);

if (!$stmt->execute()) {
    $stmt->close();
    back_register('error');
}
$stmt->close();

// ===== ĐĂNG KÝ THÀNH CÔNG: CHUYỂN ĐẾN TRANG ĐĂNG NHẬP =====
header('Location: ../../index.php?page=login&message=register_success');
exit;
