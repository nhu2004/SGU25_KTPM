<?php
// ====== CONFIG & INPUTS ======
$page = isset($_GET['pagenumber']) ? (int)$_GET['pagenumber'] : 1;
$page = max(1, $page);
$begin = ($page - 1) * 9;

$DEFAULT_MIN = 0;
$DEFAULT_MAX = 15000000;

// sort theo GIÁ SAU GIẢM
$dirParam = (!empty($_GET['pricesort']) && in_array($_GET['pricesort'], ['asc','desc'], true)) ? $_GET['pricesort'] : '';
$EFFECTIVE_PRICE = "(product_price * (100 - COALESCE(product_sale,0)) / 100.0)";
$sortby = $dirParam ? " ORDER BY $EFFECTIVE_PRICE " . strtoupper($dirParam) . " " : '';
$url_sort = $dirParam ? "&pricesort=".$dirParam : '';

$category_id = isset($_GET['category_id']) ? (int)$_GET['category_id'] : 0;
$brand_id    = isset($_GET['brand_id'])    ? (int)$_GET['brand_id']    : 0;

$price_from = isset($_GET['pricefrom']) ? (int)$_GET['pricefrom'] : $DEFAULT_MIN;
$price_to   = isset($_GET['priceto'])   ? (int)$_GET['priceto']   : $DEFAULT_MAX;
$price_from = max($DEFAULT_MIN, min($DEFAULT_MAX, $price_from));
$price_to   = max($DEFAULT_MIN, min($DEFAULT_MAX, $price_to));
if ($price_from > $price_to) { $t=$price_from; $price_from=$price_to; $price_to=$t; }

$url_price    = "&pricefrom=$price_from&priceto=$price_to";
$url_category = $category_id ? "&category_id=$category_id" : '';
$url_brand    = $brand_id ? "&brand_id=$brand_id" : '';

$category_label = '';
if ($category_id > 0) {
  if ($st = $mysqli->prepare("SELECT category_name FROM category WHERE category_id=? LIMIT 1")) {
    $st->bind_param('i', $category_id); $st->execute();
    if ($row = $st->get_result()->fetch_assoc()) $category_label = $row['category_name'];
  }
}
$brand_label = '';
if ($brand_id > 0) {
  if ($st = $mysqli->prepare("SELECT brand_name FROM brand WHERE brand_id=? LIMIT 1")) {
    $st->bind_param('i', $brand_id); $st->execute();
    if ($row = $st->get_result()->fetch_assoc()) $brand_label = $row['brand_name'];
  }
}

$show_price_tag = !($price_from == $DEFAULT_MIN && $price_to == $DEFAULT_MAX);

// ====== QUERY (LIST & COUNT CHUNG WHERE) ======
$where = " WHERE product_status=1 ";
if ($category_id) $where .= " AND product_category = $category_id ";
if ($brand_id)    $where .= " AND product_brand = $brand_id ";
$where .= " AND $EFFECTIVE_PRICE BETWEEN $price_from AND $price_to ";

$sql_product_list   = "SELECT * FROM product $where $sortby LIMIT $begin,9";
$query_product_list = mysqli_query($mysqli, $sql_product_list);
?>
<div class="product-list">
  <div class="container pd-bottom">
    <div class="row">
      <!-- SIDEBAR -->
      <div class="col" style="--w-md:3;">
        <div class="product__sidebar">
          <!-- Danh mục (không whitelist) -->
          <div class="sidebar__item w-100">
            <div class="sidebar__item--heading"><h3 class="h3">Danh mục</h3></div>
            <div class="sidebar__item--content">
              <?php
              $qcat = mysqli_query($mysqli, "SELECT category_id,category_name FROM category ORDER BY category_id DESC");
              while($c=mysqli_fetch_assoc($qcat)){
                $active = ($category_id === (int)$c['category_id']) ? 'category__active' : '';
                $href = "index.php?page=products"
                      . "&category_id={$c['category_id']}"
                      . ($brand_id ? "&brand_id=$brand_id" : '')
                      . ($show_price_tag ? $url_price : '')
                      . $url_sort;
                echo '<a href="'.$href.'" class="sidebar__item--label d-block '.$active.'">'.htmlspecialchars($c['category_name']).'</a>';
              }
              ?>
            </div>
          </div>

          <!-- Lọc theo giá -->
          <div class="sidebar__item w-100">
            <div class="sidebar__item--heading"><h3 class="h3">Lọc theo giá</h3></div>
            <div class="sidebar__item--content product-detail__variant--items d-flex">
              <div class="price__range">
                <div class="slider"><div class="progress"></div></div>
                <div class="range-input">
                  <input type="range" class="range-min" id="minPrice" min="<?php echo $DEFAULT_MIN; ?>" max="<?php echo $DEFAULT_MAX; ?>" value="<?php echo $price_from; ?>" step="1000">
                  <input type="range" class="range-max" id="maxPrice" min="<?php echo $DEFAULT_MIN; ?>" max="<?php echo $DEFAULT_MAX; ?>" value="<?php echo $price_to;   ?>" step="1000">
                </div>
                <div class="price-input d-flex space-between">
                  <div class="field">
                    <input type="number" id="price-from" class="input-min h4" value="<?php echo $price_from; ?>" min="<?php echo $DEFAULT_MIN; ?>" max="<?php echo $DEFAULT_MAX; ?>" step="1000">
                    <span class="h6 min-value">đ</span>
                  </div>
                  <div class="separator">&mdash;</div>
                  <div class="field">
                    <input type="number" id="price-to" class="input-max h4" value="<?php echo $price_to; ?>" min="<?php echo $DEFAULT_MIN; ?>" max="<?php echo $DEFAULT_MAX; ?>" step="1000">
                    <span class="h6 max-value">đ</span>
                  </div>
                </div>
                <a href="#" class="btn btn__solid btn__filter text-right" onclick="setUrlPrice();return false;">Lọc</a>
              </div>
            </div>
          </div>

          <!-- Thương hiệu -->
          <div class="sidebar__item w-100">
            <div class="sidebar__item--heading"><h3 class="h3">Thương hiệu</h3></div>
            <div class="sidebar__item--content">
              <div class="product-detail__variant--items d-flex">
                <?php
                $qbrand = mysqli_query($mysqli, "SELECT brand_id,brand_name FROM brand ORDER BY brand_id DESC");
                while($b=mysqli_fetch_assoc($qbrand)){
                  $active = ($brand_id === (int)$b['brand_id']) ? 'variant__active' : '';
                  $href = "index.php?page=products"
                        . "&brand_id={$b['brand_id']}"
                        . ($category_id ? "&category_id=$category_id" : '')
                        . ($show_price_tag ? $url_price : '')
                        . $url_sort;
                  echo '<a href="'.$href.'" class="custom-label product-detail__variant--item '.$active.'">'.htmlspecialchars($b['brand_name']).'</a>';
                }
                ?>
              </div>
            </div>
          </div>

        </div>
      </div>

      <!-- LIST -->
      <div class="col" style="--w-md:9;">

        <!-- TAG -->
        <div class="row"><div class="col">
          <div class="product__tag d-flex">
            <?php if ($show_price_tag) { ?>
              <a class="tag__item" href="index.php?page=products<?php echo $url_category.$url_brand.$url_sort; ?>">
                <div class="d-flex align-center">
                  <div class="btn__tag d-flex align-center"><img class="icon-close" src="./assets/images/icon/icon-close.png" alt=""></div>
                  <div class="tag__content d-flex align-center">
                    <span class="tag__name h5">Giá <?php echo number_format($price_from) ?>đ - <?php echo number_format($price_to) ?>đ</span>
                  </div>
                </div>
              </a>
            <?php } ?>
            <?php if ($category_id > 0) { ?>
              <a class="tag__item" href="index.php?page=products<?php echo $url_brand.($show_price_tag?$url_price:'').$url_sort; ?>">
                <div class="d-flex align-center">
                  <div class="btn__tag d-flex align-center"><img class="icon-close" src="./assets/images/icon/icon-close.png" alt=""></div>
                  <div class="tag__content d-flex align-center">
                    <span class="tag__name h5"><?php echo htmlspecialchars($category_label ?: 'Danh mục'); ?></span>
                  </div>
                </div>
              </a>
            <?php } ?>
            <?php if ($brand_id > 0) { ?>
              <a class="tag__item" href="index.php?page=products<?php echo $url_category.($show_price_tag?$url_price:'').$url_sort; ?>">
                <div class="d-flex align-center">
                  <div class="btn__tag d-flex align-center"><img class="icon-close" src="./assets/images/icon/icon-close.png" alt=""></div>
                  <div class="tag__content d-flex align-center">
                    <span class="tag__name h5"><?php echo htmlspecialchars($brand_label ?: 'Thương hiệu'); ?></span>
                  </div>
                </div>
              </a>
            <?php } ?>
          </div>
        </div></div>

        <!-- GRID -->
        <div class="row">
          <?php while ($row = mysqli_fetch_assoc($query_product_list)) { ?>
            <div class="col" style="--w: 9; --w-md: 4">
              <div class="product__card d-flex flex-column">
                <div class="product__image p-relative">
                  <a href="index.php?page=product_detail&product_id=<?php echo (int)$row['product_id'] ?>">
                    <img class="w-100 h-100 object-fit-cover" src="admin/modules/product/uploads/<?php echo htmlspecialchars($row['product_image']); ?>" alt="<?php echo htmlspecialchars($row['product_name']); ?>" />
                  </a>
                  <?php if ((int)$row['product_sale'] > 0) { ?>
                    <span class="product__sale h6 p-absolute"> - <?php echo (int)$row['product_sale'] ?>%</span>
                  <?php } ?>
                </div>
                <div class="product__info">
                  <a href="index.php?page=product_detail&product_id=<?php echo (int)$row['product_id'] ?>">
                    <h3 class="product__name h5"><?php echo htmlspecialchars($row['product_name']) ?></h3>
                  </a>
                  <span class="review-star-list d-flex">
                    <?php
                    $q = mysqli_query($mysqli, "SELECT evaluate_rate FROM evaluate WHERE product_id=".(int)$row['product_id']." AND evaluate_status=1");
                    $sum=0; $total=0;
                    while($er=mysqli_fetch_assoc($q)){ $r=(int)$er['evaluate_rate']; if($r>=1 && $r<=5){ $sum+=$r; $total++; } }
                    $avg = $total ? round($sum/$total,1) : 0;
                    for($i=0;$i<5;$i++){ echo '<div class="rating-star'.($i < $avg ? '' : ' rating-off').'"></div>'; }
                    ?>
                  </span>
                  <a href="index.php?page=product_detail&product_id=<?php echo (int)$row['product_id'] ?>">
                    <div class="product__price align-center">
                      <?php
                      $sale = (int)$row['product_sale'];
                      $pOld = (int)$row['product_price'];
                      $pNew = $sale>0 ? ($pOld - ($pOld*$sale/100)) : $pOld;
                      if ($sale>0) echo '<del class="product__price--old h5">'.number_format($pOld).' ₫</del>';
                      ?>
                      <span class="product__price--new h4"><?php echo number_format($pNew) ?> ₫</span>
                    </div>
                  </a>
                </div>
              </div>
            </div>
          <?php } ?>
        </div>

        <!-- PAGINATION -->
        <div class="row"><div class="col"><div class="pagination">
          <?php
          $sql_count = "SELECT product_id FROM product $where";
          $rs_count  = mysqli_query($mysqli, $sql_count);
          $row_count = mysqli_num_rows($rs_count);
          $totalpage = (int)ceil($row_count / 9);
          if ($row_count > 9) {
            $params = $_GET; $params['page']='products'; unset($params['pagenumber']);
            $base = 'index.php?'.http_build_query($params); ?>
            <ul class="pagination__items d-flex align-center justify-center">
              <?php if ($page > 1) { ?>
                <li class="pagination__item"><a class="d-flex align-center" href="<?php echo $base.'&pagenumber='.($page-1) ?>"><img src="./assets/images/icon/arrow-left.svg" alt=""></a></li>
              <?php } ?>
              <?php for ($i=1; $i<=$totalpage; $i++) { ?>
                <li class="pagination__item">
                  <a class="pagination__anchor <?php echo ($page==$i?'active':''); ?>" href="<?php echo $base.'&pagenumber='.$i ?>"><?php echo $i ?></a>
                </li>
              <?php } ?>
              <?php if ($page < $totalpage) { ?>
                <li class="pagination__item"><a class="d-flex align-center" href="<?php echo $base.'&pagenumber='.($page+1) ?>"><img src="./assets/images/icon/icon-nextlink.svg" alt=""></a></li>
              <?php } ?>
            </ul>
          <?php } ?>
        </div></div></div>

        <div class="row"><div class="col"><div class="text-center pd-top">
          <a class="btn btn__view--all btn__outline" href="index.php?page=products">Xem tất cả</a>
        </div></div></div>
      </div>
    </div>
  </div>
</div>

<!-- JS FILTER -->
<script>
(function(){
  const DEF_MIN = <?php echo $DEFAULT_MIN; ?>;
  const DEF_MAX = <?php echo $DEFAULT_MAX; ?>;
  const minR = document.getElementById('minPrice');
  const maxR = document.getElementById('maxPrice');
  const minI = document.getElementById('price-from');
  const maxI = document.getElementById('price-to');
  const progress = document.querySelector('.slider .progress');

  function clamp(v){ v=parseInt(v??0,10); if(isNaN(v)) v=DEF_MIN; return Math.max(DEF_MIN, Math.min(DEF_MAX, v)); }
  function renderProgress(){
    const left  = ((clamp(minR.value)-DEF_MIN)/(DEF_MAX-DEF_MIN))*100;
    const right = 100 - ((clamp(maxR.value)-DEF_MIN)/(DEF_MAX-DEF_MIN))*100;
    if(progress){ progress.style.left=left+'%'; progress.style.right=right+'%'; }
  }
  function syncFromRange(){ let a=clamp(minR.value), b=clamp(maxR.value); if(a>b){const t=a;a=b;b=t;} minR.value=a; maxR.value=b; minI.value=a; maxI.value=b; renderProgress(); }
  function syncFromInput(){ let a=clamp(minI.value), b=clamp(maxI.value); if(a>b){const t=a;a=b;b=t;} minR.value=a; maxR.value=b; minI.value=a; maxI.value=b; renderProgress(); }
  [minR,maxR].forEach(i=>i.addEventListener('input',syncFromRange));
  [minI,maxI].forEach(i=>i.addEventListener('input',syncFromInput));
  syncFromRange();

  (function(){
    const p = new URLSearchParams(window.location.search);
    p.set('page','products');
    <?php if($category_id): ?>p.set('category_id','<?php echo $category_id; ?>');<?php else: ?>p.delete('category_id');<?php endif; ?>
    <?php if($brand_id): ?>p.set('brand_id','<?php echo $brand_id; ?>');<?php else: ?>p.delete('brand_id');<?php endif; ?>
    <?php if($dirParam): ?>p.set('pricesort','<?php echo $dirParam; ?>');<?php else: ?>p.delete('pricesort');<?php endif; ?>
    if (<?php echo $price_from; ?>===DEF_MIN && <?php echo $price_to; ?>===DEF_MAX){ p.delete('pricefrom'); p.delete('priceto'); }
    else { p.set('pricefrom','<?php echo $price_from; ?>'); p.set('priceto','<?php echo $price_to; ?>'); }
    history.replaceState({}, '', 'index.php?'+p.toString());
  })();

  window.setUrlPrice = function(){
    const p = new URLSearchParams(window.location.search);
    p.set('page','products');
    <?php if($category_id): ?>p.set('category_id','<?php echo $category_id; ?>');<?php else: ?>p.delete('category_id');<?php endif; ?>
    <?php if($brand_id): ?>p.set('brand_id','<?php echo $brand_id; ?>');<?php else: ?>p.delete('brand_id');<?php endif; ?>
    <?php if($dirParam): ?>p.set('pricesort','<?php echo $dirParam; ?>');<?php else: ?>p.delete('pricesort');<?php endif; ?>
    const a = clamp(minI.value), b = clamp(maxI.value);
    if (a===DEF_MIN && b===DEF_MAX){ p.delete('pricefrom'); p.delete('priceto'); } else { p.set('pricefrom', a); p.set('priceto', b); }
    window.location.href = 'index.php?'+p.toString();
  };
})();
</script>
