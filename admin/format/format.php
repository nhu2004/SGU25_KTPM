<?php
function format_order_type($value)
{
  $text = '';
  if ($value == 1) {
    echo "Thanh toán khi nhận hàng (COD)";
  } elseif ($value == 2) {
    echo "Thanh toán MOMO QR CODE";
  } elseif ($value == 3) {
    echo "Thanh toán chuyển khoản MoMo";
  } elseif ($value == 4) {
    echo "Thanh toán chuyển khoản VNPAY";
  } elseif ($value == 5) {
    echo "Mua hàng trực tiếp";
  }
  echo $text;
}

function format_order_status($value)
{
  $text = '';
  if ($value == -1) {
    $text = 'Đơn hàng đã hủy';
  } elseif ($value == 0) {
    $text = 'Đang xử lý';
  } elseif ($value == 1) {
    $text = 'Đang chuẩn bị';
  } elseif ($value == 2) {
    $text = 'Đang giao hàng';
  } elseif ($value == 3) {
    $text = 'Đã giao hàng';
  } elseif ($value == 4) {
    $text = 'Đơn hàng hoàn trả';
  } else {
    $text = 'Đã hoàn thành';
  }
  echo $text;
}

function format_collection_type($value)
{
  $text = '';
  if ($value == 0) {
    $text = 'Tùy chọn sản phẩm';
  } else {
    $text = 'Sắp xếp theo từ khóa';
  }
  echo $text;
}

function format_account_type($value)
{
  $text = '';
  if ($value == 0) {
    $text = 'Khách hàng';
  } elseif ($value == 1) {
    $text = 'Nhân viên';
  } 
  else {
    $text = 'Quản trị viên';
  }
  echo $text;
}

function format_account_status($value)
{
  $text = '';
  if ($value == -1) {
    $text = 'Tạm khóa';
  } else {
    $text = 'Đang hoạt động';
  }
  echo $text;
}

function format_article_status($value)
{
  $text = '';
  if ($value == 0) {
    $text = 'Bản nháp';
  } else {
    $text = 'Xuất bản';
  }
  echo $text;
}

function format_comment_status($value)
{
  $text = '';
  if ($value == 0) {
    $text = 'Cần phê duyệt';
  } else {
    $text = 'Đã phê duệt';
  }
  echo $text;
}

function format_gender($value)
{
  $text = '';
  if ($value == 1) {
    $text = 'Nam';
  } elseif ($value == 2) {
    $text = 'Nữ';
  } else {
    $text = 'Chưa xác định';
  }
  echo $text;
}

//fomat date time 
function format_datetime($value)
{
  $timestamp = strtotime($value);
  $date = new DateTime();
  $date->setTimestamp($timestamp);
  $formattedDate = $date->format('Y-m-d H:i:s');
  echo $formattedDate;
}

// format 
function format_status_style($value) {
  $class = '';
  if ($value == -1)       $class = 'color-bg-red';
  elseif ($value == 0)    $class = 'color-bg-orange';
  elseif ($value == 1)    $class = 'color-bg-yellow';
  elseif ($value == 2)    $class = 'color-bg-blue';
  else                    $class = 'color-bg-green';
  echo $class;
}

function format_quantity_style($value) {
  $class = '';
  if ($value < 5) $class = 'color-t-red';
  echo $class;
}

function format_evaluate_status($value)
{
  $text = '';
  if ($value == -1) $text = 'Tiêu cực';
  else              $text = 'Tích cực';
  echo $text;
}

function format_evaluate_style($value)
{
  $class = '';
  if ($value == -1) $class = 'color-bg-red';
  else              $class = 'color-bg-green';
  echo $class;
}

function img_url_phase1($raw) {
    // SVG placeholder (khỏi 404 nếu thiếu ảnh)
    $svg = '<svg xmlns="http://www.w3.org/2000/svg" width="600" height="600">
      <defs>
        <linearGradient id="g" x1="0" x2="1" y1="0" y2="1">
          <stop stop-color="#eee" offset="0"/>
          <stop stop-color="#ddd" offset="1"/>
        </linearGradient>
      </defs>
      <rect width="100%" height="100%" fill="url(#g)"/>
      <g fill="#999" font-family="Arial,Helvetica,sans-serif" font-size="32" text-anchor="middle">
        <text x="50%" y="50%" dy="-10">No image</text>
        <text x="50%" y="50%" dy="30">available</text>
      </g>
    </svg>';
    $fallback = 'data:image/svg+xml;utf8,' . rawurlencode($svg);

    if (!$raw) return $fallback;

    $path = trim(str_replace('\\','/',$raw));

    // 1) Nếu là URL tuyệt đối thì dùng luôn
    if (preg_match('#^https?://#i', $path)) {
        return $path;
    }

    // 2) Map đường dẫn cũ của admin -> assets/images/products/
    $path = preg_replace('#^/?admin/modules/(product|category)/uploads/#i', 'assets/images/products/', $path);

    // 3) Nếu chỉ là TÊN FILE (không có dấu '/'), tự prepend thư mục ảnh public
    if (strpos($path, '/') === false) {
        $path = 'assets/images/products/' . $path;
    }

    // 4) Chuẩn hoá path tương đối
    if (strpos($path, './') !== 0 && strpos($path, '/') !== 0) {
        $path = './' . $path;
    }

    // 5) Kiểm tra tồn tại thực tế
    $root = realpath(__DIR__ . '/../..'); // project root
    $fs   = $root . DIRECTORY_SEPARATOR . ltrim(str_replace(['./','/'], ['', DIRECTORY_SEPARATOR], $path), DIRECTORY_SEPARATOR);

    return (is_file($fs) ? $path : $fallback);
}


