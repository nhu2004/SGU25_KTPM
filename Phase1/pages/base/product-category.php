<?php
// base/product-category.php
// Danh sách sản phẩm theo danh mục

$category_id = isset($_GET['category_id']) ? (int)$_GET['category_id'] : 0;

/* ---- Xử lý sắp xếp theo giá ---- */
$priceSort = strtolower($_GET['pricesort'] ?? ''); // asc | desc | ''
switch ($priceSort) {
    case 'asc':
        // Giá sau giảm tăng dần
        $orderBy = ' ORDER BY (product_price - product_price * product_sale / 100) ASC ';
        break;
    case 'desc':
        // Giá sau giảm giảm dần
        $orderBy = ' ORDER BY (product_price - product_price * product_sale / 100) DESC ';
        break;
    default:
        // Mặc định: sản phẩm mới nhất
        $orderBy = ' ORDER BY product_id DESC ';
        break;
}

if ($category_id > 0) {
    $sql_product_list = "
        SELECT *
        FROM product
        WHERE product_status = 1
          AND product_category = {$category_id}
        {$orderBy}
    ";
    $query_product_list = mysqli_query($mysqli, $sql_product_list);
    $total_product = $query_product_list ? mysqli_num_rows($query_product_list) : 0;
} else {
    $query_product_list = false;
    $total_product = 0;
}
?>
<div class="product-list">
    <div class="container pd-section">
        <div class="row">
            <div class="col">
                <div class="product__title">
                    <h2 class="h2">Sản phẩm theo danh mục</h2>
                    <p class="h9">
                        Hiện có:
                        <strong><?php echo (int)$total_product; ?></strong> sản phẩm
                    </p>
                </div>
            </div>
        </div>

        <div class="row">
            <?php if ($query_product_list && $total_product > 0): ?>
                <?php while ($row = mysqli_fetch_assoc($query_product_list)) { ?>
                    <div class="col" style="--w: 6; --w-md: 3">
                        <div class="product__card d-flex flex-column">
                            <div class="product__image p-relative">
                                <a href="index.php?page=product_detail&product_id=<?php echo (int)$row['product_id'] ?>">
                                    <?php
                                    $img = img_url_phase1($row['product_image'] ?? null);
                                    ?>
                                    <img class="w-100 h-100 object-fit-cover"
                                         src="<?php echo htmlspecialchars($img); ?>"
                                         alt="<?php echo htmlspecialchars($row['product_name']); ?>" />
                                </a>
                                <?php if ((int)$row['product_sale'] > 0) { ?>
                                    <span class="product__sale h6 p-absolute">
                                        - <?php echo (int)$row['product_sale'] ?>%
                                    </span>
                                <?php } ?>
                            </div>

                            <div class="product__info">
                                <a href="index.php?page=product_detail&product_id=<?php echo (int)$row['product_id'] ?>">
                                    <h3 class="product__name h5">
                                        <?php echo mb_strimwidth($row['product_name'], 0, 50, "...") ?>
                                    </h3>
                                </a>

                                <!-- rating -->
                                <span class="review-star-list d-flex">
                                    <?php
                                    $q = mysqli_query(
                                        $mysqli,
                                        "SELECT evaluate_rate 
                                         FROM evaluate 
                                         WHERE product_id=" . (int)$row['product_id'] . " 
                                           AND evaluate_status = 1"
                                    );

                                    $rates = [0, 0, 0, 0, 0];
                                    while ($er = mysqli_fetch_assoc($q)) {
                                        $r = (int)$er['evaluate_rate'];
                                        if ($r >= 1 && $r <= 5) {
                                            $rates[$r - 1]++;
                                        }
                                    }

                                    $total_rate = array_sum($rates);
                                    $avg = 0;
                                    if ($total_rate) {
                                        $sum = 0;
                                        for ($i = 0; $i < 5; $i++) {
                                            $sum += ($i + 1) * $rates[$i];
                                        }
                                        $avg = round($sum / $total_rate, 1);
                                    }

                                    for ($i = 0; $i < 5; $i++) {
                                        $off = ($i < $avg) ? '' : ' rating-off';
                                        echo '<div class="rating-star' . $off . '"></div>';
                                    }
                                    ?>
                                </span>

                                <!-- price -->
                                <a href="index.php?page=product_detail&product_id=<?php echo (int)$row['product_id'] ?>">
                                    <div class="product__price align-center">
                                        <?php
                                        $sale = (int)$row['product_sale'];
                                        $pOld = (int)$row['product_price'];
                                        $pNew = $sale > 0 ? ($pOld - ($pOld * $sale / 100)) : $pOld;

                                        if ($sale > 0) {
                                            echo '<del class="product__price--old h5">'
                                                . number_format($pOld) . ' ₫</del>';
                                        }
                                        ?>
                                        <span class="product__price--new h4">
                                            <?php echo number_format($pNew); ?> ₫
                                        </span>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            <?php else: ?>
                <div class="col">
                    <p>Không tìm thấy sản phẩm nào cho danh mục này.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
