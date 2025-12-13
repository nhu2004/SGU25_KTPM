<?php
if (session_status() === PHP_SESSION_NONE) {
    session_name('guha');
    session_start();
}

/* ====== DB include nếu thiếu ====== */
if (!isset($mysqli) || !($mysqli instanceof mysqli)) {
  $candidates = [
    __DIR__ . '/../../admin/config/config.php',
    __DIR__ . '/../../../admin/config/config.php',
    __DIR__ . '/admin/config/config.php',
  ];
  foreach ($candidates as $p) { if (is_file($p)) { require_once $p; break; } }
}
if (isset($mysqli) && $mysqli instanceof mysqli) { @$mysqli->set_charset('utf8mb4'); }

/* ===== Helpers ===== */
function vnd($n){ return number_format((float)$n,0,',','.') . '₫'; }
function e($v){ return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
function is_logged_in(): bool {
  return !empty($_SESSION['account_id']) || !empty($_SESSION['account_email']);
}
function effective_price(float $price, float $sale): float {
  if ($sale <= 0) return $price;
  return max(0, $price - ($price * $sale / 100));
}
function resolve_product_image(?string $raw): string {
  if (!$raw) return '/assets/images/no-image.png';
  $raw = trim(str_replace('\\','/',$raw));
  if (preg_match('~^https?://~i',$raw)) return $raw;
  $file = basename($raw);
  $candidates = [
    '/admin/modules/product/uploads/' . $file,
    '/assets/images/products/' . $file,
    '/' . ltrim($raw,'/'),
  ];
  $doc = rtrim($_SERVER['DOCUMENT_ROOT'] ?? '', '/');
  foreach ($candidates as $u){
    if ($doc && file_exists($doc.$u)) return $u;
  }
  return '/assets/images/no-image.png';
}

/* ===== Prefill shipping info ===== */
$prefill = ['customer_name'=>'', 'customer_address'=>'', 'customer_phone'=>''];
$has_any = false;

$acc_id = (int)($_SESSION['account_id'] ?? 0);
$acc_email = (string)($_SESSION['account_email'] ?? '');

/** Try helper: fetch 1 row */
function fetch_one(mysqli $db, string $sql, string $types = '', array $params = []): ?array {
  if (!$db) return null;
  $stmt = $db->prepare($sql);
  if (!$stmt) return null;
  if ($types && $params) $stmt->bind_param($types, ...$params);
  if (!$stmt->execute()) { $stmt->close(); return null; }
  $res = $stmt->get_result();
  $row = $res ? $res->fetch_assoc() : null;
  $stmt->close();
  return $row ?: null;
}

if (is_logged_in() && isset($mysqli) && $mysqli instanceof mysqli) {
  // 1) CUSTOMER theo account_id
  if (!$has_any && $acc_id > 0) {
    if ($row = fetch_one($mysqli,
      "SELECT customer_name, customer_address, customer_phone
       FROM customer WHERE account_id = ? LIMIT 1","i",[$acc_id])) {
      $prefill['customer_name']    = (string)($row['customer_name'] ?? '');
      $prefill['customer_address'] = (string)($row['customer_address'] ?? '');
      $prefill['customer_phone']   = (string)($row['customer_phone'] ?? '');
      $has_any = $prefill['customer_name'] || $prefill['customer_address'] || $prefill['customer_phone'];
    }
  }
  // 1b) CUSTOMER theo email
  if (!$has_any && $acc_email !== '') {
    if ($row = fetch_one($mysqli,
      "SELECT c.customer_name, c.customer_address, c.customer_phone
         FROM customer c
         JOIN account a ON a.account_id = c.account_id
        WHERE a.account_email = ?
        LIMIT 1","s",[$acc_email])) {
      $prefill['customer_name']    = (string)($row['customer_name'] ?? '');
      $prefill['customer_address'] = (string)($row['customer_address'] ?? '');
      $prefill['customer_phone']   = (string)($row['customer_phone'] ?? '');
      $has_any = $prefill['customer_name'] || $prefill['customer_address'] || $prefill['customer_phone'];
    }
  }

  // 2) DELIVERY gần nhất theo account_id/email
  if (!$has_any && $acc_id > 0) {
    if ($row = fetch_one($mysqli,
      "SELECT delivery_name, delivery_address, delivery_phone
         FROM delivery
        WHERE account_id = ?
        ORDER BY delivery_id DESC
        LIMIT 1","i",[$acc_id])) {
      $prefill['customer_name']    = (string)($row['delivery_name'] ?? '');
      $prefill['customer_address'] = (string)($row['delivery_address'] ?? '');
      $prefill['customer_phone']   = (string)($row['delivery_phone'] ?? '');
      $has_any = $prefill['customer_name'] || $prefill['customer_address'] || $prefill['customer_phone'];
    }
  }
  if (!$has_any && $acc_email !== '') {
    if ($row = fetch_one($mysqli,
      "SELECT d.delivery_name, d.delivery_address, d.delivery_phone
         FROM delivery d
         JOIN account a ON a.account_id = d.account_id
        WHERE a.account_email = ?
        ORDER BY d.delivery_id DESC
        LIMIT 1","s",[$acc_email])) {
      $prefill['customer_name']    = (string)($row['delivery_name'] ?? '');
      $prefill['customer_address'] = (string)($row['delivery_address'] ?? '');
      $prefill['customer_phone']   = (string)($row['delivery_phone'] ?? '');
      $has_any = $prefill['customer_name'] || $prefill['customer_address'] || $prefill['customer_phone'];
    }
  }

  // 3) ACCOUNT (name, phone)
  if (!$has_any && ($acc_id > 0 || $acc_email !== '')) {
    $row = null;
    if ($acc_id > 0) {
      $row = fetch_one($mysqli,
        "SELECT account_name, account_phone FROM account WHERE account_id = ? LIMIT 1","i",[$acc_id]);
    } elseif ($acc_email !== '') {
      $row = fetch_one($mysqli,
        "SELECT account_name, account_phone FROM account WHERE account_email = ? LIMIT 1","s",[$acc_email]);
    }
    if ($row) {
      $prefill['customer_name']  = (string)($row['account_name'] ?? '');
      $prefill['customer_phone'] = (string)($row['account_phone'] ?? '');
      $has_any = $prefill['customer_name'] || $prefill['customer_phone'];
    }
  }
}

/* 4) Fallback session key nếu có */
if (!$has_any) {
  if (!empty($_SESSION['account_name']))  $prefill['customer_name']  = (string)$_SESSION['account_name'];
  if (!empty($_SESSION['account_phone'])) $prefill['customer_phone'] = (string)$_SESSION['account_phone'];
  if (!empty($_SESSION['account_address'])) $prefill['customer_address'] = (string)$_SESSION['account_address'];
  $has_any = $prefill['customer_name'] || $prefill['customer_address'] || $prefill['customer_phone'];
}

/* ===== XÁC ĐỊNH DANH SÁCH SẢN PHẨM CHECKOUT ===== */
/*
 * mode=buynow  => dùng session['buynow'] (chỉ 1 sản phẩm vừa bấm "Mua ngay")
 * mặc định     => dùng session['cart'] (checkout toàn bộ giỏ hàng)
 */
$mode = $_SESSION['checkout_mode'] ?? 'cart';
$checkout_items = [];

if ($mode === 'buynow' && !empty($_SESSION['buynow'])) {
    // Đơn MUA NGAY: chỉ 1 sản phẩm vừa bấm "Mua ngay"
    $checkout_items = [ $_SESSION['buynow'] ];
} else {
    // Đơn từ GIỎ HÀNG như cũ
    if (!empty($_SESSION['cart']) && is_array($_SESSION['cart'])) {
        $checkout_items = $_SESSION['cart'];
    }
}
?>
<!-- start checkout -->
<section class="checkout pd-section">
  <div class="container">
    <form action="pages/handle/checkout.php" method="POST">
      <div class="row">
        <div class="col" style="--w-md:7;">
          <h2 class="checkout__title h4 d-flex align-center space-between">Thông tin người nhận hàng:</h2>
          <div class="checkout__infomation">
            <?php if (is_logged_in()) : ?>
              <div class="info__item d-flex">
                <label class="info__title" for="delivery_name">Tên khách hàng:</label>
                <input id="delivery_name" type="text" class="info__input flex-1" name="delivery_name"
                       value="<?= e($prefill['customer_name']) ?>"
                       placeholder="Nhập vào tên người nhận hàng ..." required>
              </div>
              <div class="info__item d-flex">
                <label class="info__title" for="delivery_address">Địa chỉ:</label>
                <input id="delivery_address" type="text" class="info__input flex-1" name="delivery_address"
                       value="<?= e($prefill['customer_address']) ?>"
                       placeholder="Nhập vào địa chỉ nhận hàng ..." required>
              </div>
              <div class="info__item d-flex">
                <label class="info__title" for="delivery_phone">Số điện thoại:</label>
                <input id="delivery_phone" type="tel" class="info__input flex-1" name="delivery_phone"
                       value="<?= e($prefill['customer_phone']) ?>"
                       placeholder="Nhập vào số điện thoại nhận hàng ..."
                       pattern="^0\d{9,10}$" title="SĐT bắt đầu bằng 0 và có 10-11 số" required>
              </div>
              <div class="info__item d-flex">
                <label class="info__title" for="delivery_note">Ghi chú:</label>
                <input id="delivery_note" type="text" class="info__input flex-1"
                       placeholder="Nhập vào ghi chú với người bán ..." name="delivery_note" value="">
              </div>
            <?php else : ?>
              <a href="index.php?page=login">Vui lòng đăng nhập tài khoản</a>
            <?php endif; ?>
          </div>
        </div>

        <div class="col" style="--w-md:5;">
          <div class="checkout__cart">
            <div class="checkout__items">
              <?php
              $total = 0;
              if (!empty($checkout_items)):
                foreach ($checkout_items as $ci):
                  $price = (float)($ci['product_price'] ?? 0);
                  $sale  = (float)($ci['product_sale']  ?? 0);
                  $qty   = (int)  ($ci['product_quantity'] ?? 0);
                  $unit  = effective_price($price, $sale);
                  $line  = $unit * $qty;
                  $total += $line;
                  $imgUrl = resolve_product_image($ci['product_image'] ?? '');
                  $pname  = e($ci['product_name'] ?? '');
                  $pid    = (int)($ci['product_id'] ?? 0);
              ?>
                <div class="checkout__item d-flex align-center">
                  <div class="checkout__image p-relative">
                    <div class="product-quantity align-center d-flex justify-center p-absolute"><span class="h6"><?= $qty ?></span></div>
                    <a href="index.php?page=product_detail&product_id=<?= $pid ?>">
                      <img class="w-100 d-block object-fit-cover ratio-1" src="<?= e($imgUrl) ?>" alt="<?= $pname ?>">
                    </a>
                  </div>
                  <div class="checkout__name flex-1"><h3 class="h6"><?= $pname ?></h3></div>
                  <div class="h6 checkout__price"><?= vnd($unit) ?></div>
                </div>
              <?php endforeach; else: ?>
                <span>Không tồn tại giỏ hàng</span>
              <?php endif; ?>

              <table class="w-100 mg-t-20">
                <tr><td class="h6">Tạm tính:</td><td class="h6 text-right"><?= vnd($total) ?></td></tr>
                <tr><td class="h6">Giảm giá</td><td class="h6 text-right">0đ</td></tr>
                <tr><td class="h6">Phí vận chuyển</td><td class="h6 text-right">Miễn phí</td></tr>
              </table>
            </div>

            <div class="checkout__bottom text-right">
              <div class="checkout__total--amount d-flex align-center space-between">
                <h4 class="h4">Tổng tiền phải thanh toán:</h4>
                <span class="h4 checkout__total"><?= vnd($total) ?></span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Payment -->
      <div class="row">
        <div class="col" style="--w-md: 7">
          <h4 class="h4 payment__heading">Phương thức thanh toán:</h4>
          <div class="payment__items">
            <div class="payment__item checked d-flex align-center">
              <input class="payment__radio" type="radio" name="order_type" id="payment_default" value="1" checked />
              <img class="payment__icon" src="./assets/images/icon/icon-shipcod.png" alt="Ship COD">
              <label class="payment__label w-100 h-100" for="payment_default">
                <span class="d-block">COD</span>
                <span class="d-block">Thanh toán khi nhận hàng</span>
              </label>
            </div>
            <div class="payment__item d-flex align-center">
              <input class="payment__radio" type="radio" name="order_type" id="payment_momo_qr" value="2" />
              <img class="payment__icon" src="./assets/images/payment/qrcode.png" alt="QR CODE" style="width:62px;">
              <label class="payment__label w-100 h-100" for="payment_momo_qr">
                <span class="d-block">QR CODE</span>
                <span class="d-block">Thanh toán MOMO QRCODE</span>
              </label>
            </div>
            <div class="payment__item d-flex align-center">
              <input class="payment__radio" type="radio" name="order_type" id="payment_vnp" value="4" />
              <img class="payment__icon" src="./assets/images/payment/vnpay.png" alt="VNPAY" style="width:62px;">
              <label class="payment__label w-100 h-100" for="payment_vnp">
                <span class="d-block">VNPAY</span>
                <span class="d-block">Thanh toán chuyển khoản VNPAY</span>
              </label>
            </div>
          </div>
        </div>
      </div>

      <!-- Phase 2: luôn khóa nút Đặt hàng -->
      <div class="w-100 pd-top text-left">
        <button type="submit" name="redirect" class="btn btn__solid" disabled>
          Đặt hàng
        </button>
        <a href="index.php?page=cart" class="btn anchor">Trở về giỏ hàng</a>
      </div>
    </form>
  </div>
</section>
<!-- end checkout -->
