<?php
// base/product-list.php
// Home: block "Sản phẩm nổi bật" – chỉ hiển thị sản phẩm đang bật và còn hàng

$sql_product_list = "
  SELECT *
  FROM product
  WHERE product_status = 1 AND product_quantity > 0
  ORDER BY product_id DESC
  LIMIT 8
";
$query_product_list = mysqli_query($mysqli, $sql_product_list);
?>
<div class="product-list">
    <div class="container pd-section">
        <div class="row">
            <div class="col">
                <div class="product__title">
                    <h2 class="h2">Sản phẩm nổi bật</h2>
                    <p class="h9">Một số sản phẩm mới được cập nhật tại cửa hàng</p>
                </div>
            </div>
        </div>
        <div class="row">
            <?php while ($row = mysqli_fetch_assoc($query_product_list)) { ?>
                <div class="col" style="--w: 6; --w-md: 3">
                    <div class="product__card d-flex flex-column">
                        <div class="product__image p-relative">
                            <a href="index.php?page=product_detail&product_id=<?php echo (int)$row['product_id'] ?>">
                                <?php $img = img_url_phase1($row['product_image'] ?? null); ?>
                                <img class="w-100 h-100 object-fit-cover" src="<?php echo htmlspecialchars($img); ?>" alt="<?php echo htmlspecialchars($row['product_name']); ?>" />
                            </a>
                            <?php if ((int)$row['product_sale'] > 0) { ?>
                                <span class="product__sale h6 p-absolute"> - <?php echo (int)$row['product_sale'] ?>%</span>
                            <?php } ?>
                        </div>
                        <div class="product__info">
                            <a href="index.php?page=product_detail&product_id=<?php echo (int)$row['product_id'] ?>">
                                <h3 class="product__name h5"><?php echo mb_strimwidth($row['product_name'], 0, 50, "...") ?></h3>
                            </a>
                            <span class="review-star-list d-flex">
                                <?php
                                $q = mysqli_query($mysqli, "SELECT evaluate_rate FROM evaluate WHERE product_id=".(int)$row['product_id']." AND evaluate_status = 1");
                                $rates=[0,0,0,0,0];
                                while($er = mysqli_fetch_assoc($q)){ $r=(int)$er['evaluate_rate']; if($r>=1 && $r<=5) $rates[$r-1]++; }
                                $total=array_sum($rates);
                                $avg=0; if($total){ $sum=0; for($i=0;$i<5;$i++) $sum+=($i+1)*$rates[$i]; $avg=round($sum/$total,1); }
                                for($i=0;$i<5;$i++){
                                    echo '<div class="rating-star'.($i < $avg ? '' : ' rating-off').'"></div>';
                                }
                                ?>
                            </span>
                            <a href="index.php?page=product_detail&product_id=<?php echo (int)$row['product_id'] ?>">
                                <div class="product__price align-center">
                                    <?php
                                    $sale=(int)$row['product_sale'];
                                    $pOld=(int)$row['product_price'];
                                    $pNew=$sale>0?($pOld-($pOld*$sale/100)):$pOld;
                                    if ($sale>0) { echo '<del class="product__price--old h5">'.number_format($pOld).' ₫</del>'; }
                                    ?>
                                    <span class="product__price--new h4"><?php echo number_format($pNew) ?> ₫</span>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>
        <div class="row">
            <div class="col">
                <div class="text-center pd-top">
                    <a class="btn btn__view--all btn__outline" href="index.php?page=products">Xem tất cả</a>
                </div>
            </div>
        </div>
    </div>
</div>
