<?php
// ⚠️ KHÔNG có ký tự nào trước thẻ PHP này (không BOM/space)
declare(strict_types=1);

// --- Session config: đặt TRƯỚC session_start() ---
// --- Session config: đặt TRƯỚC session_start() ---
ini_set('session.use_strict_mode', '1');
ini_set('session.use_only_cookies', '1');
ini_set('session.cookie_httponly', '1');

if (PHP_VERSION_ID >= 70300) {
    session_set_cookie_params([
        'lifetime' => 0,
        'path'     => '/',   // cookie áp dụng cho toàn site
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
} else {
    session_set_cookie_params(0, '/');
}

// Bật buffer để tránh output sớm từ file include
ob_start();

// ✅ DÙNG CHUNG session_name('guha') giống Phase2 / các file khác
if (session_status() === PHP_SESSION_NONE) {
    session_name('guha');
    session_start();
}


// Include PHP trước khi xuất HTML
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

  <!-- start google font -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@300;400;600&display=swap" rel="stylesheet">
  <!-- end google font -->

  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
  <script src="./assets/js/toast_message.js"></script>
  <script src="./assets/js/message.js"></script>
  <script src="./assets/js/validator.js"></script>
</head>

<body>
  <div id="wrapper">
    <?php include __DIR__ . "/pages/main.php"; ?>
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
<?php ob_end_flush(); ?>
