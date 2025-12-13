<?php
// Bật session nếu chưa có (đồng bộ tên session 'guha')
if (session_status() === PHP_SESSION_NONE) {
    session_name('guha');
    session_start();
}

error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);
// unset($_SESSION['cart']);

/* ===== DEBUG: hiển thị lỗi khi checkout (nếu có) ===== */
if (!empty($_SESSION['checkout_errors'])) {
    echo '<div style="background:#ffecec;color:#c00;padding:10px;margin:10px 0;border:1px solid #c00;">';
    echo '<strong>Lỗi khi đặt hàng:</strong><br>';
    foreach ($_SESSION['checkout_errors'] as $msg) {
        echo '- ' . htmlspecialchars($msg, ENT_QUOTES, 'UTF-8') . '<br>';
    }
    echo '</div>';
    unset($_SESSION['checkout_errors']);
}

/* ===== Helper ảnh ===== */
if (!function_exists('img_url_phase1')) {
    function img_url_phase1($file) {
        $file = trim((string)$file);
        if ($file === '') {
            return './assets/images/no-image.png';
        }
        return 'admin/modules/product/uploads/' . $file;
    }
}
?>

<!-- Style tinh chỉnh footer & nút -->
<style>
  /* Nút trong footer: gọn theo nội dung (desktop), full trên mobile */
  .cart__footer { display: flex; flex-direction: column; align-items: flex-end; gap: 8px; }
  .cart__footer .btn.cart__btn{
    width: auto !important;
    display: inline-flex; align-items: center; justify-content: center;
    padding: 12px 22px;
    border-radius: 8px;
    min-width: 220px; /* có thể giảm xuống 180 nếu muốn gọn hơn */
  }
  /* Nếu muốn nút nhỏ hơn nữa: giảm min-width ở trên */
  @media (max-width: 576px){
    .cart__footer{ align-items: stretch; }
    .cart__footer .btn.cart__btn{ width: 100% !important; }
  }
</style>

<div id="toast_message"></div>
<section class="cart pd-section">
    <div class="container">
        <div class="cart__header d-flex space-between align-center">
            <h1 class="h2">Giỏ hàng của bạn</h1>
            <a class="h4" href="index.php?page=products">Quay lại cửa hàng</a>
        </div>
        <?php
        if (!empty($_SESSION['cart'])) {
            $total = 0;
        ?>
            <div class="cart__container">
                <div class="cart__heading">
                    <div class="cart__item d-grid">
                        <div class="cart__image">
                            <span class="h6">TÊN SẢN PHẨM</span>
                        </div>
                        <div class="cart__title"></div>
                        <div class="cart__quantity">
                            <span class="d-none lg-initital">Số lượng</span>
                        </div>
                        <div class="cart__total">
                            <span class="h6">GIÁ TIỀN</span>
                        </div>
                    </div>
                </div>
                <div class="cart__content">
                    <?php
                    $validate = true;
                    foreach ($_SESSION['cart'] as $cart_item) {
                        $pid = (int)$cart_item['product_id'];
                        $qProduct = mysqli_query($mysqli, "SELECT * FROM product WHERE product_id = '{$pid}' LIMIT 1");
                        $product  = mysqli_fetch_array($qProduct);

                        $priceOld = (float)$cart_item['product_price'];
                        $sale     = (float)$cart_item['product_sale'];
                        $priceNew = $priceOld - ($priceOld * $sale / 100);
                        $qtyLine  = (int)$cart_item['product_quantity'];

                        if ((int)$product['product_quantity'] >= $qtyLine) {
                            $total += $priceNew * $qtyLine;
                    ?>
                            <div class="cart__item d-grid">
                                <div class="cart__image">
                                    <a href="index.php?page=product_detail&product_id=<?php echo $pid; ?>">
                                        <img class="w-100"
                                             src="<?php echo htmlspecialchars(img_url_phase1($cart_item['product_image'] ?? '')); ?>"
                                             alt="product" />
                                    </a>
                                </div>
                                <div class="cart__title">
                                    <h3 class="cart__name h4"><?php echo htmlspecialchars($cart_item['product_name']); ?></h3>
                                    <span class="cart__color">
                                        Dung tích: <strong><?php echo htmlspecialchars($cart_item['capacity_name'] ?? '—'); ?></strong>
                                        &nbsp;&nbsp;|&nbsp;&nbsp;
                                        Còn lại: <span class="product__quantity"><?php echo (int)$product['product_quantity']; ?></span> sản phẩm
                                    </span>
                                </div>
                                <div class="cart__quantity">
                                    <div class="cart__quantity--container d-flex align-center">
                                        <div class="select__number p-relative">
                                            <a href="pages/handle/addtocart.php?div=<?php echo $pid; ?>"
                                               class="select__number--minus cursor-pointer p-absolute d-flex align-center justify-center">
                                                <img src="./assets/images/icon/minus.svg" alt="minus" />
                                            </a>
                                            <!-- NHẬP SỐ LƯỢNG BẰNG TAY -->
                                            <input
                                                type="number"
                                                min="0"
                                                value="<?php echo $qtyLine; ?>"
                                                class="select__number--value heading-6 w-100 h-100"
                                                data-product-id="<?php echo $pid; ?>"
                                            />
                                            <a href="pages/handle/addtocart.php?sum=<?php echo $pid; ?>"
                                               class="select__number--plus cursor-pointer p-absolute d-flex align-center justify-center">
                                                <img src="./assets/images/icon/plus.svg" alt="plus" />
                                            </a>
                                        </div>
                                        <div class="cart__delete cursor-pointer d-flex align-center justify-center">
                                            <a href="pages/handle/addtocart.php?delete=<?php echo $pid; ?>">
                                                <img src="./assets/images/icon/delete.svg" alt="delete" />
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <div class="cart__total h4">
                                    <?php echo number_format($priceNew) . ' ₫'; ?>
                                </div>
                            </div>
                        <?php
                        } else {
                            $validate = false;
                        ?>
                            <div class="cart__item d-grid">
                                <div class="cart__image opacity-50">
                                    <a href="index.php?page=product_detail&product_id=<?php echo $pid; ?>">
                                        <img class="w-100"
                                             src="<?php echo htmlspecialchars(img_url_phase1($cart_item['product_image'] ?? '')); ?>"
                                             alt="product" />
                                    </a>
                                </div>
                                <div class="cart__title">
                                    <h3 class="cart__name h4 opacity-50"><?php echo htmlspecialchars($cart_item['product_name']); ?></h3>
                                    <span class="cart__color color-wanning">
                                        Còn lại: <span class="product__quantity"><?php echo (int)$product['product_quantity']; ?></span> sản phẩm
                                    </span>
                                </div>
                                <div class="cart__quantity">
                                    <div class="cart__quantity--container d-flex align-center">
                                        <div class="select__number p-relative">
                                            <a href="pages/handle/addtocart.php?div=<?php echo $pid; ?>"
                                               class="select__number--minus cursor-pointer p-absolute d-flex align-center justify-center">
                                                <img src="./assets/images/icon/minus.svg" alt="minus" />
                                            </a>
                                            <!-- NHẬP SỐ LƯỢNG BẰNG TAY -->
                                            <input
                                                type="number"
                                                min="0"
                                                value="<?php echo $qtyLine; ?>"
                                                class="select__number--value heading-6 w-100 h-100"
                                                data-product-id="<?php echo $pid; ?>"
                                            />
                                            <a href="pages/handle/addtocart.php?sum=<?php echo $pid; ?>"
                                               class="select__number--plus cursor-pointer p-absolute d-flex align-center justify-center">
                                                <img src="./assets/images/icon/plus.svg" alt="plus" />
                                            </a>
                                        </div>
                                        <div class="cart__delete cursor-pointer d-flex align-center justify-center">
                                            <a href="pages/handle/addtocart.php?delete=<?php echo $pid; ?>">
                                                <img src="./assets/images/icon/delete.svg" alt="delete" />
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <div class="cart__total h4 opacity-50">
                                    <?php echo number_format($priceNew) . ' ₫'; ?>
                                </div>
                            </div>
                    <?php
                        }
                    }
                    ?>
                </div>
            </div>
            <div class="cart__footer w-100 h-100">
                <div class="cart__footer--total h4">
                    Tổng tiền: <?php echo number_format((float)$total) . ' ₫'; ?>
                </div>
                <p class="cart__footer--context">
                    Thuế và phí vận chuyển được tính khi thanh toán
                </p>
                <?php
                if (isset($_SESSION['account_email'])) {
                    if ($validate == true) {
                ?>
                        <a href="index.php?page=checkout" class="btn cart__btn btn__solid text-center">Tiến hành đặt hàng</a>
                    <?php } else { ?>
                        <button class="btn cart__btn btn__solid text-center opacity-50" onclick="showErrorMessage();">Tiến hành đặt hàng</button>
                    <?php } ?>
                <?php } else { ?>
                    <!-- Router tới trang login -->
                    <a class="btn cart__btn btn__outline" href="index.php?page=login">Đăng nhập đặt hàng</a>
                <?php } ?>
            </div>
        <?php
        } else {
        ?>
            <p>Hiện không có sản phẩm nào trong giỏ hàng</p>
        <?php
        }
        ?>
    </div>
</section>
<!-- end cart -->

<script>
    function showSuccessMessage() {
        toast({
            title: "Success",
            message: "Cập nhật thành công",
            type: "success",
            duration: 3000,
        });
    }
    function showErrorMessage() {
        toast({
            title: "Error",
            message: "Số lượng vượt quá tồn kho",
            type: "error",
            duration: 3000,
        });
    }

    // ====== CẬP NHẬT SỐ LƯỢNG ======
    document.addEventListener("DOMContentLoaded", function () {
        var inputs = document.querySelectorAll(".select__number--value");
        inputs.forEach(function (input) {
            input.addEventListener("change", function () {
                var pid = this.getAttribute("data-product-id");
                var qty = parseInt(this.value, 10);

                if (isNaN(qty) || qty < 0) {
                    qty = 0; // server sẽ xử lý: 0 = xóa sản phẩm
                }

                window.location.href =
                    "pages/handle/addtocart.php?update=" + encodeURIComponent(pid) +
                    "&qty=" + encodeURIComponent(qty);
            });
        });
    });
</script>
<?php
if (isset($_GET['message']) && $_GET['message'] == 'success') {
    echo '<script>showSuccessMessage();window.history.pushState(null, "", "index.php?page=cart");</script>';
} elseif (isset($_GET['message']) && $_GET['message'] == 'error') {
    echo '<script>showErrorMessage();window.history.pushState(null, "", "index.php?page=cart");</script>';
}
?>
