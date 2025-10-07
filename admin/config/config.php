<?php
// admin/config/config.php
$host = '127.0.0.1'; // dùng 127.0.0.1 thay vì localhost để tránh socket
$user = 'root';
$pass = '';          // XAMPP mặc định root không có password
$db   = 'dbperfume';
$port = 3306;        // nếu bạn dùng port khác (VD 3307), đổi ở đây

$mysqli = new mysqli($host, $user, $pass, $db, $port);
if ($mysqli->connect_errno) {
    die('MySQL connect error ['.$mysqli->connect_errno.']: '.$mysqli->connect_error);
}
$mysqli->set_charset('utf8mb4');
