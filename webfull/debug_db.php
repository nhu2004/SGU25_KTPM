<?php
require __DIR__ . '/admin/config/config.php';
$r = $mysqli->query('SELECT DATABASE() AS db, NOW() as now');
$row = $r->fetch_assoc();
echo "DB đang dùng: {$row['db']}<br>Thời điểm: {$row['now']}<br>";
