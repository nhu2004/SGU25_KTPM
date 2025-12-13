<?php
if (session_status() === PHP_SESSION_NONE) {
    session_name('guha');
    session_start();
}

$account_id = (int)($_SESSION['account_id'] ?? 0);

if ($account_id <= 0) {
    ?>
    <div class="my-account__content">
        <h2 class="my-account__title h3">Lịch sử đơn hàng</h2>
        <p>Bạn cần đăng nhập để xem lịch sử đơn hàng.</p>
    </div>
    <?php
    exit;
}

/*
 * LỊCH SỬ ĐƠN HÀNG
 * ----------------
 * Gồm:
 *   - Đơn đã giao hàng (order_status = 3)
 *   - Đơn đã huỷ (order_status = -1)
 */
$sql_order_list = "
    SELECT *
    FROM orders
    WHERE account_id = {$account_id}
      AND order_status IN (3, -1)
    ORDER BY order_id DESC
";

$query_order_list = mysqli_query($mysqli, $sql_order_list);
?>
<div class="my-account__content">
    <h2 class="my-account__title h3">Lịch sử đơn hàng</h2>

    <div class="order__items">
        <?php while ($order = mysqli_fetch_array($query_order_list)) { ?>
            <a href="index.php?page=order_detail&order_code=<?php echo $order['order_code'] ?>">
                <div class="order__item">
                    <div class="order__header d-flex align-center space-between">

                        <div class="order__info">
                            <h5 class="order__code">#<?php echo $order['order_code'] ?></h5>
                            <span class="h6 d-block"><?php echo $order['order_date'] ?></span>

                            <!-- 1 dòng hiển thị phương thức thanh toán -->
                            <span class="h6 d-block">
                                Thanh toán: <?php echo format_order_type($order['order_type']); ?>
                            </span>
                        </div>

                        <div class="text-right">
                            <!-- Trạng thái đơn: Đã giao hàng / Đã huỷ -->
                            <span class="order__status h6 d-block">
                                <?php echo format_order_status($order['order_status']); ?>
                            </span>

                            <?php
                            // Chỉ hiển thị "Đã thanh toán ..." khi đơn đã giao thành công (status = 3)
                            $status = (int)$order['order_status'];
                            $type   = (int)$order['order_type'];

                            if ($status === 3) {
                                if ($type === 1) {
                                    echo '<span class="h6" style="color:#28a745;">Đã thanh toán COD</span>';
                                } else {
                                    echo '<span class="h6" style="color:#28a745;">Đã thanh toán online</span>';
                                }
                            }
                            // status = -1 (Đã huỷ): không cần thêm dòng gì nữa,
                            // vì phía trên đã có "Đã huỷ" rồi.
                            ?>
                        </div>

                    </div>

                    <div class="order__container">
                        <?php
                        $sql_order_detail_list = "
                            SELECT od.order_detail_id,
                                   p.product_id,
                                   p.product_name,
                                   od.product_quantity,
                                   od.product_price,
                                   od.product_sale,
                                   p.product_image
                            FROM order_detail od
                            JOIN product p ON od.product_id = p.product_id
                            WHERE od.order_code = '" . $order['order_code'] . "'
                            ORDER BY od.order_detail_id DESC
                        ";
                        $query_order_detail_list = mysqli_query($mysqli, $sql_order_detail_list);

                        while ($od = mysqli_fetch_array($query_order_detail_list)) {
                        ?>
                            <div class="cart__item d-flex align-center">
                                <div class="cart__image p-relative">
                                    <img
                                        class="w-100 d-block object-fit-cover ratio-1"
                                        src="admin/modules/product/uploads/<?php echo $od['product_image'] ?>"
                                        alt="product"
                                    />
                                </div>

                                <div class="flex-1">
                                    <h3 class="cart__name h4">
                                        <?php echo $od['product_name'] ?>
                                    </h3>
                                    <span class="cart__quantity h6 d-block">
                                        x <?php echo $od['product_quantity'] ?>
                                    </span>
                                </div>

                                <div class="h5 cart__price">
                                    <?php echo number_format($od['product_price']) ?>₫
                                </div>
                            </div>
                        <?php } ?>
                    </div>

                    <div class="order__footer d-flex align-center space-between">
                        <span class="h5">
                            Thành tiền: <?php echo number_format($order['total_amount']) ?>₫
                        </span>
                    </div>
                </div>
            </a>
        <?php } ?>
    </div>
</div>
