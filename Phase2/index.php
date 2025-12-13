<?php
// ⚠️ Không có ký tự nào trước thẻ PHP này (không BOM, không xuống dòng)
declare(strict_types=1);

/**
 * MỞ SESSION SỚM NHẤT CÓ THỂ
 * Toàn bộ site sẽ dùng duy nhất 1 session name = 'guha'
 * -> header.php, addtocart.php... cũng phải dùng cùng tên này.
 */
if (session_status() === PHP_SESSION_NONE) {
    session_name('guha');
    session_start();
}

/**
 * Bật output buffer (tuỳ chọn):
 * giúp tránh lỗi header already sent nếu các file include bên trong có redirect.
 * Nếu bạn chắc chắn không có echo trước header() thì có thể bỏ.
 */
ob_start();

// Include PHP trước khi in ra HTML
require_once __DIR__ . '/admin/format/format.php';
require_once __DIR__ . '/admin/config/config.php';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Guha Store</title>

  <link rel="shortcut icon" href="./assets/images/icon/favicon.ico" />
  <!-- start css -->
  <link rel="stylesheet" href="./assets/css/helper.css" />
  <link rel="stylesheet" href="./assets/css/layout.css" />
  <link rel="stylesheet" href="./assets/css/main.css" />
  <link rel="stylesheet" href="./assets/css/responsive.css" />
  <link rel="stylesheet" href="./assets/css/login.css">
  <link rel="stylesheet" href="./assets/css/toast.css">
  <!-- end css -->

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@300;400;600&display=swap" rel="stylesheet">

  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
  <script src="./assets/js/toast_message.js"></script>
  <script src="./assets/js/message.js"></script>
  <script src="./assets/js/validator.js"></script>
</head>
<body>
  <div id="wrapper">
    <?php
      // Router chính của Phase2
      include __DIR__ . "/pages/main.php";
    ?>
  </div>

  <div id="toast"></div>

  <script src="./assets/js/main.js"></script>
  <script src="./assets/js/navigation.js"></script>
  <script src="./assets/js/select-number.js"></script>
  <script src="./assets/js/scrollsnap.js"></script>
  <script src="./assets/js/payment.js"></script>
  <script src="./assets/js/inputRange.js"></script>
  <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
  <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
  <script src="https://kit.fontawesome.com/e63ed424f0.js" crossorigin="anonymous"></script>

  <!-- Messenger Plugin chat Code -->
  <div id="fb-root"></div>
  <div id="fb-customer-chat" class="fb-customerchat"></div>
  <script>
    var chatbox = document.getElementById('fb-customer-chat');
    chatbox.setAttribute("page_id", "101046545371764");
    chatbox.setAttribute("attribution", "biz_inbox");
  </script>
  <script>
    window.fbAsyncInit = function() {
      FB.init({ xfbml: true, version: 'v16.0' });
    };
    (function(d, s, id) {
      var js, fjs = d.getElementsByTagName(s)[0];
      if (d.getElementById(id)) return;
      js = d.createElement(s); js.id = id;
      js.src = 'https://connect.facebook.net/vi_VN/sdk/xfbml.customerchat.js';
      fjs.parentNode.insertBefore(js, fjs);
    }(document, 'script', 'facebook-jssdk'));
  </script>
</body>
</html>
<?php
// Kết thúc buffer (nếu đã bật)
ob_end_flush();
