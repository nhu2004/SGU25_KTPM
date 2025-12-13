<?php
// ==== Session đồng bộ với site ====
if (session_status() !== PHP_SESSION_ACTIVE) {
    // Nếu site của bạn KHÔNG dùng session_name tùy biến, có thể bỏ dòng dưới.
    session_name('guha');
    session_start();
}

// ==== DB config ====
require_once __DIR__ . '/../../admin/config/config.php'; // chỉnh lại path nếu khác

/* ===========================
   POST → REDIRECT → GET (PRG)
   =========================== */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['search'])) {
    $kw = isset($_POST['keyword']) ? trim($_POST['keyword']) : '';
    header('Location: index.php?page=search&keyword=' . urlencode($kw));
    exit;
}

/* ===========================
   Helper URL ảnh tuyệt đối
   =========================== */
if (!function_exists('img_url_phase1')) {
    function img_url_phase1($file) {
        $file = trim((string)$file);
        if ($file === '' || $file === 'null') return './assets/images/no-image.png';
        $host   = $_SERVER['HTTP_HOST'] ?? 'localhost';
        $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        // Sửa 8080 nếu server ảnh của bạn chạy cổng khác
        return "{$scheme}://{$host}:8080/admin/modules/product/uploads/{$file}";
    }
}

/* ===========================
   Nhận keyword qua GET
   =========================== */
$keyword = isset($_GET['keyword']) ? trim($_GET['keyword']) : '';
$kwLike  = '%' . mysqli_real_escape_string($mysqli, $keyword) . '%';

/* ===========================
   Truy vấn sản phẩm
   =========================== */
$sql_product_list   = "SELECT * FROM product WHERE product_name LIKE '{$kwLike}' ORDER BY product_id DESC";
$query_product_list = mysqli_query($mysqli, $sql_product_list);
?>
<div class="product-list">
  <div class="container pd-section">
    <div class="row">
      <div class="col">
        <div class="product__title text-center">
          <h2 class="h2">Danh sách các sản phẩm</h2>
          <?php if ($keyword !== ''): ?>
            <p class="h9">tìm kiếm có liên quan đến "<strong><?php echo htmlspecialchars($keyword); ?></strong>"</p>
          <?php else: ?>
            <p class="h9">Vui lòng nhập từ khóa để tìm sản phẩm</p>
          <?php endif; ?>
        </div>
      </div>

      <?php
      $count = 0;
      if ($query_product_list) {
        while ($row = mysqli_fetch_array($query_product_list)) {
          $count++;

          $pid      = (int)$row['product_id'];
          $name     = (string)$row['product_name'];
          $sale     = (float)$row['product_sale'];
          $priceOld = (float)$row['product_price'];
          $priceNew = $priceOld - ($priceOld * $sale / 100);

          // Ảnh sản phẩm (không bị mất hình)
          $imgUrl   = img_url_phase1($row['product_image'] ?? '');

          // Lấy rating
          $rate1=$rate2=$rate3=$rate4=$rate5=0;
          $qr = mysqli_query($mysqli, "SELECT evaluate_rate FROM evaluate WHERE product_id={$pid} AND evaluate_status=1");
          if ($qr) {
            while ($ev = mysqli_fetch_assoc($qr)) {
              $r = (int)$ev['evaluate_rate'];
              if     ($r === 1) $rate1++;
              elseif ($r === 2) $rate2++;
              elseif ($r === 3) $rate3++;
              elseif ($r === 4) $rate4++;
              elseif ($r === 5) $rate5++;
            }
          }
          $total_rate = $rate1 + $rate2 + $rate3 + $rate4 + $rate5;
          $rate_avg   = $total_rate ? round(($rate1*1 + $rate2*2 + $rate3*3 + $rate4*4 + $rate5*5) / $total_rate, 1) : 0;
      ?>
      <div class="col" style="--w: 6; --w-md: 3">
        <div class="product__card d-flex flex-column">
          <div class="product__image p-relative">
            <a href="index.php?page=product_detail&product_id=<?php echo $pid; ?>">
              <img class="w-100 h-100 object-fit-cover"
                   src="<?php echo htmlspecialchars($imgUrl); ?>"
                   alt="<?php echo htmlspecialchars($name); ?>"
                   onerror="this.src='./assets/images/no-image.png'">
            </a>
            <?php if ($sale > 0): ?>
              <span class="product__sale h6 p-absolute">- <?php echo (int)$sale; ?>%</span>
            <?php endif; ?>
          </div>

          <div class="product__info">
            <a href="index.php?page=product_detail&product_id=<?php echo $pid; ?>">
              <h3 class="product__name h5"><?php echo htmlspecialchars($name); ?></h3>
            </a>

            <span class="review-star-list d-flex">
              <?php for ($i = 0; $i < 5; $i++): ?>
                <?php if ($i < floor($rate_avg)): ?>
                  <div class="rating-star"></div>
                <?php else: ?>
                  <div class="rating-star rating-off"></div>
                <?php endif; ?>
              <?php endfor; ?>
              <!-- ĐÃ BỎ (<?php echo (int)$total_rate; ?>) -->
            </span>

            <a href="index.php?page=product_detail&product_id=<?php echo $pid; ?>">
              <div class="product__price align-center">
                <?php if ($sale > 0): ?>
                  <del class="product__price--old h5"><?php echo number_format($priceOld) . ' ₫'; ?></del>
                <?php endif; ?>
                <span class="product__price--new h4"><?php echo number_format($priceNew) . ' vnđ'; ?></span>
              </div>
            </a>
          </div>
        </div>
      </div>
      <?php
        } // while
      } // if query ok
      ?>

      <?php if ($count === 0): ?>
        <div class="col">
          <p class="text-center">Không tìm thấy sản phẩm phù hợp.</p>
        </div>
      <?php endif; ?>

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
