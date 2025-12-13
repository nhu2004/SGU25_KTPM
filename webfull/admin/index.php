<?php
session_start();

/* =======================
 * XỬ LÝ LOGOUT
 * ======================= */
if (isset($_GET['logout']) && $_GET['logout'] == 1) {
  // Xóa session đăng nhập
  unset($_SESSION['login'], $_SESSION['account_name'], $_SESSION['account_type']);

  // Điều hướng về trang login
  header('Location: login.php');
  exit;
}

/* =======================
 * CHẶN TRUY CẬP NẾU CHƯA LOGIN
 * ======================= */
if (!isset($_SESSION['login'])) {
  header('Location: login.php');
  exit;
}

/* =======================
 * CHẶN TOÀN CỤC CÁC CHỨC NĂNG KHÔNG DÙNG NỮA
 * ======================= */
if (isset($_GET['action'], $_GET['query'])) {
  $cur = $_GET['action'] . ':' . $_GET['query'];

  $ban = [
    'order:order_live',           // Đơn hàng tại quầy
    'product:product_inventory',  // Hàng tồn kho
    'inventory:inventory_list',   // Phiếu nhập kho
    'inventory:inventory_add',
    'inventory:inventory_detail',
    'article:article_add',        // Bài viết
    'article:article_list',
    'article:article_edit',
    'dashboard:dashboard'         // Thống kê
  ];

  if (in_array($cur, $ban, true)) {
    http_response_code(404);
    exit('404 Not Found');
  }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <!-- Required meta tags -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>Admin</title>

  <link rel="shortcut icon" href="../assets/images/icon/favicon.ico" />
  <link rel="shortcut icon" href="images/favicon.png" />

  <!-- plugins:css -->
  <link rel="stylesheet" href="vendors/feather/feather.css">
  <link rel="stylesheet" href="vendors/mdi/css/materialdesignicons.min.css">
  <link rel="stylesheet" href="vendors/ti-icons/css/themify-icons.css">
  <link rel="stylesheet" href="vendors/typicons/typicons.css">
  <link rel="stylesheet" href="vendors/simple-line-icons/css/simple-line-icons.css">
  <link rel="stylesheet" href="vendors/css/vendor.bundle.base.css">

  <!-- custom css -->
  <link rel="stylesheet" href="css/customize.css">
  <link rel="stylesheet" href="css/toast.css">
  <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/morris.js/0.5.1/morris.css">

  <!-- Plugin css for this page -->
  <link rel="stylesheet" href="vendors/datatables.net-bs4/dataTables.bootstrap4.css">
  <link rel="stylesheet" href="js/select.dataTables.min.css">

  <!-- main theme css -->
  <link rel="stylesheet" href="css/vertical-layout-light/style.css">

  <!-- auto complete -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/chosen/1.8.7/chosen.min.css">

  <!-- FIX: Toast trong admin bị mờ -> ép hiển thị rõ, chữ đậm -->
  <style>
    #toast,
    #toast * {
      opacity: 1 !important;
      filter: none !important;
      color: #000 !important;
    }
  </style>

  <!-- JS libs (cần cho toàn admin) -->
  <script src="js/toast_message.js"></script>
  <script src="https://kit.fontawesome.com/a2e1cc550d.js" crossorigin="anonymous"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.0/jquery.min.js"></script>
  <script src="https://cdn.ckeditor.com/4.21.0/standard/ckeditor.js"></script>
  <script src="js/validator.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/chosen/1.8.7/chosen.jquery.min.js"></script>
</head>

<body>
  <!-- Toast container -->
  <div id="toast"></div>

  <div class="container-scroller">
    <?php
    include('config/config.php');
    include('format/format.php');

    // Header + Menu + Main content
    include('./modules/header.php');
    ?>
    <div class="container-fluid page-body-wrapper">
      <?php include('./modules/menu.php'); ?>
      <?php include('./modules/main.php'); ?>
    </div>
  </div>

  <!-- plugins:js -->
  <script src="vendors/js/vendor.bundle.base.js"></script>

  <!-- Plugin js for this page -->
  <script src="vendors/chart.js/Chart.min.js"></script>
  <script src="vendors/bootstrap-datepicker/bootstrap-datepicker.min.js"></script>
  <script src="vendors/progressbar.js/progressbar.min.js"></script>

  <!-- inject:js -->
  <script src="js/off-canvas.js"></script>
  <script src="js/hoverable-collapse.js"></script>
  <script src="js/template.js"></script>
  <script src="js/settings.js"></script>
  <script src="js/todolist.js"></script>

  <!-- Ionicons -->
  <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
  <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>

  <!-- Custom js for this page -->
  <script src="js/dashboard.js"></script>
  <script src="js/Chart.roundedBarCharts.js"></script>

  <!-- morris dashboard -->
  <script src="//cdnjs.cloudflare.com/ajax/libs/raphael/2.1.0/raphael-min.js"></script>
  <script src="//cdnjs.cloudflare.com/ajax/libs/morris.js/0.5.1/morris.min.js"></script>
</body>

</html>
