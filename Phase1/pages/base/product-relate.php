<?php
// related-products.php (block "Có thể bạn quan tâm")
// Gọi file này ở trang chi tiết sản phẩm

include_once 'recommendation.php';

$customer_id = isset($_SESSION['account_id']) ? (int)$_SESSION['account_id'] : 0;
$product_id  = isset($_GET['product_id']) ? (int)$_GET['product_id'] : 0;

// lấy gợi ý và cắt tối đa 8 sản phẩm
$similar_products = recommend_products($customer_id, $product_id);
$similar_products = is_array($similar_products) ? array_slice($similar_products, 0, 8) : [];
?>
<div class="product-list">
    <div class="container pd-section">
        <div class="row">
            <div class="col">
                <div class="product__title">
                    <h2 class="h3">Có thể bạn quan tâm</h2>
                </div>
            </div>
        </div>
        <div class="row">
            <?php if (count($similar_products) > 0): ?>
                <?php foreach ($similar_products as $row_relatedProc): ?>
                    <div class="col" style="--w: 6; --w-md: 3">
                        <div class="product__card d-flex flex-column">
                            <div class="product__image p-relative">
                                <a href="index.php?page=product_detail&product_id=<?php echo (int)$row_relatedProc['product_id'] ?>">
                                    <?php $img = img_url_phase1($row_relatedProc['product_image'] ?? null); ?>
                                    <img class="w-100 h-100 object-fit-cover" src="<?php echo htmlspecialchars($img); ?>" alt="product image" />
                                </a>
                                <?php if ((int)($row_relatedProc['product_sale'] ?? 0) > 0): ?>
                                    <span class="product__sale h6 p-absolute"> - <?php echo (int)$row_relatedProc['product_sale'] ?>%</span>
                                <?php endif; ?>
                            </div>
                            <div class="product__info">
                                <a href="index.php?page=product_detail&product_id=<?php echo (int)$row_relatedProc['product_id'] ?>">
                                    <h3 class="product__name h5"><?php echo mb_strimwidth(htmlspecialchars($row_relatedProc['product_name'] ?? ''), 0, 50, "...") ?></h3>
                                </a>
                                <span class="review-star-list d-flex">
                                    <?php
                                    $pid = (int)$row_relatedProc['product_id'];
                                    $query_evaluate_rating = mysqli_query($mysqli, "SELECT evaluate_rate FROM evaluate WHERE product_id=".$pid." AND evaluate_status = 1");

                                    $rates=[0,0,0,0,0];
                                    while ($evaluate_rating = mysqli_fetch_assoc($query_evaluate_rating)) {
                                        $r = (int)$evaluate_rating['evaluate_rate'];
                                        if ($r>=1 && $r<=5) $rates[$r-1]++;
                                    }
                                    $total_rate = array_sum($rates);
                                    $rate_avg = 0;
                                    if ($total_rate) {
                                        $sum = 0; for($i=0;$i<5;$i++) $sum += ($i+1)*$rates[$i];
                                        $rate_avg = round($sum / $total_rate, 1);
                                    }
                                    for ($i = 0; $i < 5; $i++) {
                                        echo $i < $rate_avg ? '<div class="rating-star"></div>' : '<div class="rating-star rating-off"></div>';
                                    }
                                    ?>
                                    <span>(<?php echo (int)$total_rate ?>)</span>
                                </span>
                                <a href="index.php?page=product_detail&product_id=<?php echo (int)$row_relatedProc['product_id'] ?>">
                                    <div class="product__price align-center">
                                        <?php
                                        $price = (int)($row_relatedProc['product_price'] ?? 0);
                                        $sale  = (int)($row_relatedProc['product_sale'] ?? 0);
                                        if ($sale > 0) {
                                            echo '<del class="product__price--old h5">'.number_format($price).' ₫</del>';
                                        }
                                        $pNew = $sale>0 ? ($price - ($price*$sale/100)) : $price;
                                        ?>
                                        <span class="product__price--new h4"><?php echo number_format($pNew).' ₫'; ?></span>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>
