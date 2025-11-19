<?php
// Auto-switch DB config: Docker vs XAMPP
$isDocker = getenv('DOCKERIZED') === '1';

if ($isDocker) {
    // 👉 Đang chạy trong Docker
    $host = 'db';
    $port = 3306;
    $user = 'root';
    $pass = 'root';
    $db   = 'dbperfume';
} else {
    // 👉 Đang chạy XAMPP (local)
    $host = '127.0.0.1';
    $port = 3306;
    $user = 'root';
    $pass = '';  // XAMPP mặc định không mật khẩu
    $db   = 'dbperfume';
}

$mysqli = @new mysqli($host, $user, $pass, $db, $port);
if ($mysqli->connect_errno) {
    die('MySQL connect error ['.$mysqli->connect_errno.']: '.$mysqli->connect_error);
}
$mysqli->set_charset('utf8mb4');
