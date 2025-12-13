<?php
date_default_timezone_set('Asia/Ho_Chi_Minh');

/**
 * File cấu hình VNPAY — đọc từ ENV, có fallback sandbox.
 * Dùng chung cho nhánh VNPAY trong checkout (webfull).
 */

// App URL Phase3 (mặc định cổng 8083)
$appUrl = getenv('APP_URL') ?: 'http://localhost:8083';

// Thông tin VNPAY từ ENV (fallback theo bản bạn đang có để chạy ngay)
$vnp_TmnCode    = getenv('VNP_TMN_CODE')    ?: 'MCG9RE1Q'; // Terminal Id
$vnp_HashSecret = getenv('VNP_HASH_SECRET') ?: 'BPZPZWGMUSPMKQAUFMWOYVLKTVYBBWLX'; // Secret key

// Endpoint & URL trả về
$vnp_Url       = getenv('VNP_URL')         ?: 'https://sandbox.vnpayment.vn/paymentv2/vpcpay.html';
$vnp_Returnurl = getenv('VNP_RETURN_URL')  ?: ($appUrl . '/index.php?page=thankiu');

// (Các API khác nếu webfull có dùng)
$vnp_apiUrl = getenv('VNP_API_URL') ?: 'http://sandbox.vnpayment.vn/merchant_webapi/merchant.html';
$apiUrl     = getenv('VNP_API_TXN_URL') ?: 'https://sandbox.vnpayment.vn/merchant_webapi/api/transaction';

// Thời gian hiệu lực giao dịch (VD: 15 phút)
$startTime = date("YmdHis");
$expire    = date('YmdHis', strtotime('+15 minutes', strtotime($startTime)));
