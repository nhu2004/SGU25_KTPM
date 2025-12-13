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
/* ✅ Giỏ hàng */
elseif ($action === 'cart') {
    include("./pages/base/cart.php");
}
/* ✅ Đăng nhập */
elseif ($action === 'login') {
    include("./pages/base/login.php");
}
/* ✅ Đăng ký (THÊM MỚI) */
elseif ($action === 'register') {
    include("./pages/base/register.php"); // đảm bảo file này tồn tại
}
/* ✅ Tài khoản */
elseif ($action === 'my_account') {
    $tab = isset($_GET['tab']) ? $_GET['tab'] : 'account_info';

    if ($tab === 'change_info') {
        // Trang THAY ĐỔI THÔNG TIN
        include("./pages/base/account-settings.php");
    } else {
        // Trang THÔNG TIN TÀI KHOẢN (mặc định)
        include("./pages/base/account-info.php");
    }
}
/* ✅ Thanh toán */
elseif ($action === 'checkout') {
    include("./pages/base/checkout.php");
}
/* ✅ Đổi mật khẩu */
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
