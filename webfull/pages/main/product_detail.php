<?php
// NÚT QUAY LẠI CỬA HÀNG
// Hiện trên đầu trang chi tiết sản phẩm
?>
<div class="container pd-section">
    <a href="index.php?page=products" class="btn btn__outline">
        ← Quay lại cửa hàng
    </a>
</div>

<!-- start product detail -->
<?php
include __DIR__ . '/../base/product-detail.php';
?>
<!-- end product detail -->

<?php
if (isset($_SESSION['account_id'])) {
    // Nếu đã đăng nhập: hiện phần lọc / gợi ý theo hành vi
    ?>
    <!-- start product filtering -->
    <?php
    include __DIR__ . '/../base/product_filtering.php';
    ?>
    <!-- end product filtering -->
    <?php
} else {
    // Nếu chưa đăng nhập: hiện sản phẩm liên quan
    ?>
    <!-- start product list -->
    <?php
    include __DIR__ . '/../base/product-relate.php';
    ?>
    <!-- end product list -->
    <?php
}
?>
