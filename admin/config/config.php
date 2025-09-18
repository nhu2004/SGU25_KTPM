<?php
$mysqli = new mysqli("localhost","root","","dbperfume");

if ($mysqli->connect_errno) {
    echo "Failed to connect to MySQL: " . $mysqli->connect_error;
    exit();
} else {
    echo "Kết nối thành công!";
}
?>
