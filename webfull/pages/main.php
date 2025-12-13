<!-- start header -->
<?php
include("./pages/base/header.php");
?>
<!-- end header -->

<?php
// Lấy tham số page
$action = isset($_GET['page']) ? $_GET['page'] : '';

if ($action === 'about') {
    include("./pages/main/about.php");
}
elseif ($action === 'article') {
    include("./pages/main/article.php");
}
elseif ($action === 'contact') {
    include("./pages/main/contact.php");
}
elseif ($action === 'products') {
    include("./pages/main/products.php");
}
elseif ($action === 'search') {
    include("./pages/main/search.php");
}
elseif ($action === 'product_detail') {
    include("./pages/main/product_detail.php");
}
elseif ($action === 'product_brand') {
    include("./pages/main/product_brand.php");
}
elseif ($action === 'product_category') {
    include("./pages/main/product_category.php");
}
/* Giỏ hàng */
elseif ($action === 'cart') {
    include("./pages/base/cart.php");
}
/* Đăng nhập */
elseif ($action === 'login') {
    include("./pages/base/login.php");
}
/* Đăng ký (THÊM MỚI) */
elseif ($action === 'register') {
    include("./pages/base/register.php"); 
}
/* Tài khoản */
elseif ($action === 'my_account') {
    // Dùng router my_account.php mới với các tab account_info, account_order, account_history, account_settings
    include("./pages/main/my_account.php");
}
/* Thanh toán */
elseif ($action === 'checkout') {
    include("./pages/base/checkout.php");
}
/* Trang cảm ơn sau đặt hàng (Phase 3) */
elseif ($action === 'thankiu') {
    include("./pages/main/thankiu.php");
}
/* Cổng thanh toán MOMO GIẢ LẬP (Phase 3) */
elseif ($action === 'payment_momo_fake') {
    include("./pages/main/payment_momo_fake.php");
}
/* Cổng thanh toán VNPAY GIẢ LẬP (Phase 3) */
elseif ($action === 'payment_vnpay_fake') {
    include("./pages/main/payment_vnpay_fake.php");
}
/* Chi tiết đơn hàng */
elseif ($action === 'order_detail') {
    include("./pages/base/order-detail.php");
}
/* Đổi mật khẩu */
elseif ($action === 'password_change') {
    include("./pages/base/password-change.php");
}
elseif ($action === 'forget_password') {
    include("./pages/base/forget-password.php");
}
elseif ($action === '404') {
    include("./pages/main/404.php");
}
else {
    include("./pages/main/home.php");
}
?>

<!-- start footer -->
<?php
include("./pages/base/footer.php");
?>
<!-- end footer -->
