<?php
// ==== Session đồng bộ với site ====
if (session_status() !== PHP_SESSION_ACTIVE) {
    // Nếu site của bạn KHÔNG dùng session_name tùy biến, có thể bỏ dòng dưới.
    session_name('guha');
    session_start();
}

/* ====== BẮT BUỘC ĐĂNG NHẬP TRƯỚC KHI THÊM GIỎ HÀNG ====== */
if (empty($_SESSION['account_email']) && empty($_SESSION['account_id'])) {
    // Chưa đăng nhập -> báo và chuyển tới trang login
    echo "<script>
            alert('Vui lòng đăng nhập để thêm sản phẩm vào giỏ hàng!');
            window.location.href='../../index.php?page=login';
          </script>";
    exit;
}

// ==== DB config ====
require_once __DIR__ . '/../../admin/config/config.php';

// ---- Helper: quay lại trang trước có message ----
function back_with_message($status = 'success') {
    $ref = $_SERVER['HTTP_REFERER'] ?? '';
    if ($ref) {
        $sep = (strpos($ref, '?') !== false) ? '&' : '?';
        header('Location: ' . $ref . $sep . 'message=' . $status);
    } else {
        header('Location: ../../index.php?page=cart&message=' . $status);
    }
    exit;
}

// ---- Helper: chỉ lấy tên file ảnh để lưu vào giỏ ----
function img_basename(?string $raw): string {
    if (!$raw) return '';
    $raw = str_replace('\\', '/', trim($raw));
    return basename($raw);
}

// Bảo đảm giỏ hàng tồn tại là mảng
if (empty($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

/* ============ TĂNG SỐ LƯỢNG ============ */
if (isset($_GET['sum'])) {
    $status     = 1;
    $product_id = (int)$_GET['sum'];

    // lấy tồn kho tối đa
    $sql = "SELECT product_quantity FROM product WHERE product_id = $product_id LIMIT 1";
    $res = mysqli_query($mysqli, $sql);
    $row = $res ? mysqli_fetch_array($res) : null;
    $qty_max = (int)($row['product_quantity'] ?? 0);

    $product = [];
    foreach ($_SESSION['cart'] as $cart_item) {
        if ((int)$cart_item['product_id'] !== $product_id) {
            // đồng bộ ảnh (nếu trước đó là path dài)
            $cart_item['product_image'] = img_basename($cart_item['product_image'] ?? '');
            $product[] = $cart_item;
            continue;
        }

        $nextQty = (int)$cart_item['product_quantity'] + 1;
        if ($qty_max > 0 && $nextQty > $qty_max) {
            // vượt tồn kho: giữ nguyên số lượng cũ, báo lỗi
            $status = 0;
        } else {
            $cart_item['product_quantity'] = $nextQty;
        }
        // đồng bộ ảnh
        $cart_item['product_image'] = img_basename($cart_item['product_image'] ?? '');
        $product[] = $cart_item;
    }

    $_SESSION['cart'] = $product;
    header('Location: ../../index.php?page=cart&message=' . ($status ? 'success' : 'error'));
    exit;
}

/* ============ GIẢM SỐ LƯỢNG (NÚT "-") ============ */
if (isset($_GET['div'])) {
    $product_id = (int)$_GET['div'];

    $product = [];
    foreach ($_SESSION['cart'] as $cart_item) {
        if ((int)$cart_item['product_id'] !== $product_id) {
            // đồng bộ ảnh
            $cart_item['product_image'] = img_basename($cart_item['product_image'] ?? '');
            $product[] = $cart_item;
            continue;
        }

        $currentQty = (int)$cart_item['product_quantity'];
        $nextQty    = $currentQty - 1;

        if ($nextQty <= 0) {
            // Nếu sau khi trừ còn 0 hoặc âm -> XÓA sản phẩm khỏi giỏ
            continue;
        }

        // Ngược lại: cập nhật số lượng mới
        $cart_item['product_quantity'] = $nextQty;
        $cart_item['product_image']    = img_basename($cart_item['product_image'] ?? '');
        $product[] = $cart_item;
    }

    if (empty($product)) {
        unset($_SESSION['cart']);
    } else {
        $_SESSION['cart'] = $product;
    }

    header('Location: ../../index.php?page=cart&message=success');
    exit;
}

/* ============ CẬP NHẬT SỐ LƯỢNG BẰNG TAY (TC4, TC5, TC7) ============ */
if (isset($_GET['update'])) {
    $product_id = (int)$_GET['update'];
    $qty_input  = isset($_GET['qty']) ? (int)$_GET['qty'] : 0;
    if ($qty_input < 0) $qty_input = 0;

    // lấy tồn kho tối đa
    $sql = "SELECT product_quantity FROM product WHERE product_id = $product_id LIMIT 1";
    $res = mysqli_query($mysqli, $sql);
    $row = $res ? mysqli_fetch_array($res) : null;
    $stock_max = (int)($row['product_quantity'] ?? 0);

    $status  = 1;   // 1 = success, 0 = lỗi (vượt tồn kho)
    $product = [];

    foreach ($_SESSION['cart'] as $cart_item) {
        if ((int)$cart_item['product_id'] !== $product_id) {
            $cart_item['product_image'] = img_basename($cart_item['product_image'] ?? '');
            $product[] = $cart_item;
            continue;
        }

        // ====== TC7: Nhập 0 -> XÓA SẢN PHẨM ======
        if ($qty_input === 0) {
            // không push vào $product => bị xóa
            continue;
        }

        // ====== TC5: Vượt tồn kho ======
        if ($stock_max > 0 && $qty_input > $stock_max) {
            $status = 0;
            // số lượng quay về tồn kho hiện tại
            $cart_item['product_quantity'] = $stock_max;
        } else {
            // ====== TC4: Hợp lệ ======
            $cart_item['product_quantity'] = $qty_input;
        }

        $cart_item['product_image'] = img_basename($cart_item['product_image'] ?? '');
        $product[] = $cart_item;
    }

    if (empty($product)) {
        unset($_SESSION['cart']);
    } else {
        $_SESSION['cart'] = $product;
    }

    header('Location: ../../index.php?page=cart&message=' . ($status ? 'success' : 'error'));
    exit;
}

/* ============ XOÁ SẢN PHẨM ============ */
if (!empty($_SESSION['cart']) && isset($_GET['delete'])) {
    $product_id = (int)$_GET['delete'];

    $product = [];
    foreach ($_SESSION['cart'] as $cart_item) {
        if ((int)$cart_item['product_id'] !== $product_id) {
            // đồng bộ ảnh
            $cart_item['product_image'] = img_basename($cart_item['product_image'] ?? '');
            $product[] = $cart_item;
        }
    }

    if ($product) {
        $_SESSION['cart'] = $product;
    } else {
        unset($_SESSION['cart']); // giỏ rỗng
    }

    header('Location: ../../index.php?page=cart&message=success');
    exit;
}

/* ============ THÊM VÀO GIỎ / MUA NGAY ============ */
if (isset($_POST['addtocart']) || isset($_POST['buynow'])) {
    $product_id       = (int)($_GET['product_id'] ?? 0);
    $product_quantity = max(1, (int)($_POST['product_quantity'] ?? 1));

    // Lấy thông tin sản phẩm + capacity
    $sql = "
        SELECT p.product_id, p.product_name, p.product_price, p.product_sale, p.product_image,
               p.product_quantity AS stock_qty,
               c.capacity_name
        FROM product p
        LEFT JOIN capacity c ON p.capacity_id = c.capacity_id
        WHERE p.product_id = $product_id
        LIMIT 1
    ";
    $res = mysqli_query($mysqli, $sql);
    $row = $res ? mysqli_fetch_array($res) : null;

    if ($row) {
        // ảnh chỉ lưu tên file
        $img_file = img_basename($row['product_image'] ?? '');

        // số lượng thêm không vượt tồn kho (nếu có stock)
        $stock_max = (int)($row['stock_qty'] ?? 0);
        if ($stock_max > 0 && $product_quantity > $stock_max) {
            $product_quantity = $stock_max;
        }

        // Chuẩn hóa item dùng chung cho cả CART và BUY NOW
        $item = [
            'product_id'       => (int)$row['product_id'],
            'product_name'     => $row['product_name'],
            'product_quantity' => $product_quantity,
            'product_price'    => (int)$row['product_price'],
            'product_sale'     => (int)$row['product_sale'],
            'product_image'    => $img_file, // <-- LƯU CHỈ TÊN FILE
            'capacity_name'    => $row['capacity_name'] ?? '',
        ];

        /* ====== MUA NGAY (KHÔNG TÍNH CÁC SẢN PHẨM KHÁC TRONG GIỎ) ====== */
        if (isset($_POST['buynow'])) {
            // Lưu riêng sản phẩm buy now, KHÔNG đụng đến giỏ hàng hiện tại
            $_SESSION['buynow']         = $item;
            $_SESSION['checkout_mode']  = 'buynow';

            // Đi thẳng tới trang checkout
            header('Location: ../../index.php?page=checkout');
            exit;
        }

        /* ====== THÊM VÀO GIỎ HÀNG BÌNH THƯỜNG ====== */
        $new_product = [ $item ];
        $found   = false;
        $product = [];

        foreach ($_SESSION['cart'] as $cart_item) {
            if ((int)$cart_item['product_id'] === $product_id) {
                // cộng dồn số lượng, tôn trọng tồn kho
                $current = (int)$cart_item['product_quantity'];
                $nextQty = $current + $product_quantity;
                if ($stock_max > 0) {
                    $nextQty = min($nextQty, $stock_max);
                }
                $cart_item['product_quantity'] = max(1, $nextQty);

                // cập nhật thông tin mới nhất
                $cart_item['product_sale']  = (int)$row['product_sale'];
                $cart_item['capacity_name'] = !empty($cart_item['capacity_name'])
                    ? $cart_item['capacity_name']
                    : ($row['capacity_name'] ?? '');
                $cart_item['product_image'] = $img_file; // đồng bộ ảnh
                $found = true;
            } else {
                // đồng bộ ảnh các item khác
                $cart_item['product_image'] = img_basename($cart_item['product_image'] ?? '');
            }
            $product[] = $cart_item;
        }

        $_SESSION['cart'] = $found ? $product : array_merge($product, $new_product);
    }

    back_with_message('success');
}

// Không có action hợp lệ -> quay lại
back_with_message('error');
