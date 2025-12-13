<?php
declare(strict_types=1);

// === Đồng bộ session với toàn hệ thống (guha) ===
if (session_status() === PHP_SESSION_NONE) {
    session_name('guha');      // <-- THÊM DÒNG NÀY
    session_start();           // và giữ session_start()
}

@ini_set('display_errors', '0');
@ini_set('log_errors', '1');

require_once __DIR__ . '/../../admin/config/config.php'; // $mysqli

// Chỉ xử lý POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /index.php?page=login');
    exit;
}

// Lấy dữ liệu POST và chuẩn hoá
$email = trim((string)($_POST['account_email'] ?? ''));
$pass  = (string)($_POST['account_password'] ?? '');
$email = strtolower(preg_replace('/\s+/', '', $email)); // bỏ khoảng trắng + lower

if ($email === '' || $pass === '') {
    header('Location: /index.php?page=login&error=empty');
    exit;
}

try {
    // Đối chiếu email theo LOWER(TRIM(...)) để tránh lỗi email có space/hoa
    $stmt = $mysqli->prepare(
        'SELECT account_id, account_email, account_password, account_name
         FROM account
         WHERE LOWER(TRIM(account_email)) = ?
         LIMIT 1'
    );
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $res = $stmt->get_result();
    $row = $res->fetch_assoc();
    $stmt->close();

    error_log("LOGIN_POST email={$email}");
    if ($row) error_log("LOGIN_ROW email={$row['account_email']}");

    $ok = false;
    $dbHash = '';
    if ($row) {
        $dbHash = (string)($row['account_password'] ?? '');
        $h = strtolower($dbHash);

        // 1) password_hash/bcrypt
        $info = password_get_info($dbHash);
        if (!empty($info['algo'])) {
            $ok = password_verify($pass, $dbHash);
        } else {
            // 2) SHA1 (40 hex) + sha1(md5(pass)) + sha1(lower(pass)) fallback
            if (strlen($h) === 40 && ctype_xdigit($h)) {
                $ok = (sha1($pass) === $h)
                   || (sha1(md5($pass)) === $h)
                   || (sha1(strtolower($pass)) === $h);
            }
            // 3) MD5 (32 hex) + md5(lower(pass)) fallback
            elseif (strlen($h) === 32 && ctype_xdigit($h)) {
                $ok = (md5($pass) === $h) || (md5(strtolower($pass)) === $h);
            }
            // 4) plaintext
            else {
                $ok = hash_equals($dbHash, $pass);
            }
        }
    }

    if ($ok) {
        $_SESSION['account_id']    = (int)$row['account_id'];
        $_SESSION['account_email'] = (string)$row['account_email'];
        $_SESSION['account_name']  = (string)($row['account_name'] ?? '');

        // Ghi chắc session rồi mới redirect (tránh mất session sau 302)
        session_regenerate_id(true);
        session_write_close();

        header('Location: /index.php?page=my_account&tab=account_info');
        exit;
    }

    error_log("LOGIN_FAIL post_email={$email} row_email=" . ($row['account_email'] ?? 'NULL') . " hash_db=" . ($dbHash ?: 'NULL'));
    header('Location: /index.php?page=login&error=invalid');
    exit;

} catch (Throwable $e) {
    error_log('login error: ' . $e->getMessage());
    header('Location: /index.php?page=login&error=server');
    exit;
}
