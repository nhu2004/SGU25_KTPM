<?php
// Bắt đầu session nếu chưa có
if (session_status() === PHP_SESSION_NONE) {
    session_name('guha');
    session_start();
}

// Xử lý ĐĂNG XUẤT: chỉ xoá thông tin đăng nhập, GIỮ giỏ hàng
if (isset($_GET['logout']) && $_GET['logout'] == 1) {

    // Xóa các khóa đăng nhập
    unset($_SESSION['account_email']);
    unset($_SESSION['account_id']);
    unset($_SESSION['account_name']);

    // Nếu có session buy now thì xoá cho sạch
    if (isset($_SESSION['buynow'])) {
        unset($_SESSION['buynow']);
    }

    // KHÔNG xóa $_SESSION['cart'] để giữ lại giỏ hàng
    // KHÔNG session_destroy(), KHÔNG xoá cookie phiên

    header('Location: index.php');
    exit;
}

// Biến tiện dùng trong view
$accountId    = (int)($_SESSION['account_id'] ?? 0);
$accountEmail = $_SESSION['account_email'] ?? '';
$accountName  = trim($_SESSION['account_name'] ?? '');
$isLoggedIn   = ($accountId > 0 || $accountEmail !== '');
$cartCount    = (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) ? count($_SESSION['cart']) : 0;

// Tên hiển thị trên header (nếu chưa có tên thì fallback "Tài khoản")
$displayName = $accountName !== '' ? $accountName : 'Tài khoản';
?>
<style>
    .voice-btn.recognizing .action__icon-on { display: block; }
    .voice-btn.recognizing .action__icon-off { display: none; }

    /* Topbar */
    .header__topbar .topbar-container {
    position: relative;
    text-align: center;
    }

    .header__topbar .topbar-message {
    margin: 0;
    }

    .header__topbar .topbar-login {
    position: absolute;
    right: 0;
    top: 50%;
    transform: translateY(-50%);
    }

    /* Dropdown tài khoản trên header */
    .account-dropdown {
        position: relative;
    }
    .account-toggle {
        border: none;
        background: transparent;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 4px 6px;
    }
    .account-toggle span.account-name {
        font-size: 14px;
    }
    .account-menu {
        position: absolute;
        top: 135%;
        right: 0;
        background: #ffffff;
        border-radius: 8px;
        box-shadow: 0 12px 30px rgba(15, 23, 42, 0.15);
        min-width: 220px;
        padding: 8px 0;
        z-index: 50;
        display: none;
    }
    .account-dropdown.open .account-menu {
        display: block;
    }
    .account-menu a {
        display: block;
        padding: 10px 16px;
        font-size: 14px;
        color: #111827;
        text-decoration: none;
        white-space: nowrap;
    }
    .account-menu a:hover {
        background: #f3f4f6;
    }
</style>

<header class="header">
    <div class="header__topbar">
        <div class="container topbar-container">
            <p class="h5 topbar-message">Miễn phí ship toàn quốc</p>
                <?php if (!$isLoggedIn) { ?>
                    <a class="h5 login-btn topbar-login" href="index.php?page=login">
                    ĐĂNG NHẬP
                    </a>
                <?php } ?>
            </div>
        </div>
    </div>

    <div class="header__main">
        <div class="container">
            <div class="header__container d-grid middle-left">
                <!-- menu button -->
                <div class="header__btn md-none d-flex align-center">
                    <div class="navbar__icons">
                        <div class="navbar__icon"></div>
                    </div>
                </div>

                <!-- LOGO -->
                <div class="header__logo d-flex justify-center align-center">
                    <a href="index.php" class="d-inline-block">
                        <img class="d-block w-100 svg__icon" src="./assets/images/logo/logo_guha.png" alt="Logo" />
                    </a>
                </div>

                <!-- NAV -->
                <nav class="header__nav space-between d-flex">
                    <ul class="nav__list md-flex">
                        <li class="nav__item md-none">
                            <a href="#" class="nav__anchor" style="content: ''"></a>
                        </li>

                        <!-- Cửa hàng -->
                        <li class="nav__item nav__items h7">
                            <span class="nav__anchor p-relative h7 d-flex align-center space-between w-100 cursor-pointer" href="#">
                                Cửa hàng
                                <img class="md-none svg__icon" src="./assets/images/icon/icon-nextlink.svg" alt="next" />
                                <img class="d-none md-block svg__icon" src="./assets/images/icon/icon-chevron-down.svg" alt="back" style="margin-left: 8px" />
                            </span>
                            <ul class="header__subnav p-absolute">
                                <li class="nav__item md-none h5">
                                    <span class="nav__anchor cursor-pointer d-flex align-center" style="content: ''">
                                        <img class="md-none svg__icon" src="./assets/images/icon/arrow-left.svg" alt="" style="margin-right: 8px" />
                                        Cửa hàng
                                    </span>
                                </li>
                                <li class="nav__item">
                                    <a class="nav__anchor h7 d-flex align-center space-between" href="index.php?page=products">
                                        Tất cả sản phẩm
                                    </a>
                                </li>
                                <?php
                                $sql_category_list   = "SELECT * FROM category ORDER BY category_id DESC";
                                $query_category_list = mysqli_query($mysqli, $sql_category_list);
                                while ($row_category = mysqli_fetch_array($query_category_list)) { ?>
                                    <li class="nav__item">
                                        <a class="nav__anchor h7 d-flex align-center space-between"
                                           href="index.php?page=products&category_id=<?php echo $row_category['category_id'] ?>">
                                            <?php echo $row_category['category_name'] ?>
                                        </a>
                                    </li>
                                <?php } ?>
                            </ul>
                        </li>

                        <!-- Thương hiệu -->
                        <li class="nav__item nav__items h7">
                            <span class="nav__anchor p-relative h7 d-flex align-center space-between w-100 cursor-pointer" href="#">
                                Thương hiệu
                                <img class="md-none svg__icon" src="./assets/images/icon/icon-nextlink.svg" alt="next" />
                                <img class="d-none md-block svg__icon" src="./assets/images/icon/icon-chevron-down.svg" alt="back" style="margin-left: 8px" />
                            </span>
                            <ul class="header__subnav p-absolute">
                                <li class="nav__item md-none h5">
                                    <span class="nav__anchor cursor-pointer d-flex align-center" style="content: ''">
                                        <img class="md-none svg__icon" src="./assets/images/icon/arrow-left.svg" alt="" style="margin-right: 8px" />
                                        Thương hiệu
                                    </span>
                                </li>
                                <?php
                                $sql_brand_list   = "SELECT * FROM brand ORDER BY brand_id DESC";
                                $query_brand_list = mysqli_query($mysqli, $sql_brand_list);
                                while ($row_brand = mysqli_fetch_array($query_brand_list)) { ?>
                                    <li class="nav__item">
                                        <a class="nav__anchor h7 d-flex align-center space-between"
                                           href="index.php?page=products&brand_id=<?php echo $row_brand['brand_id'] ?>">
                                            <?php echo $row_brand['brand_name'] ?>
                                        </a>
                                    </li>
                                <?php } ?>
                            </ul>
                        </li>

                        <li class="nav__item">
                            <a class="nav__anchor h7 d-flex align-center space-between" href="index.php?page=about">
                                Giới thiệu
                            </a>
                        </li>
                        <li class="nav__item">
                            <a class="nav__anchor h7 d-flex align-center space-between" href="index.php?page=contact">
                                Liên hệ
                            </a>
                        </li>
                    </ul>

                    <div class="flex-1"></div>

                    <!-- Sidebar footer (mobile) -->
                    <div class="header__footer md-none">
                        <div class="person-login d-flex align-center">
                            <img class="svg__icon" src="./assets/images/icon/icon-person.svg" alt="person" />
                            <?php if ($isLoggedIn) { ?>
                                <a class="h5 login-btn" href="index.php?page=my_account&tab=account_info">TÀI KHOẢN</a>
                            <?php } else { ?>
                                <a class="h5 login-btn" href="index.php?page=login">ĐĂNG NHẬP</a>
                            <?php } ?>
                        </div>
                        <ul class="social__items d-flex align-center">
                            <li class="social__item"><a href="#"><img class="svg__icon d-block" src="./assets/images/icon/twitter.svg" alt="" /></a></li>
                            <li class="social__item"><a href="#"><img class="svg__icon d-block" src="./assets/images/icon/facebook.svg" alt="" /></a></li>
                            <li class="social__item"><a href="#"><img class="svg__icon d-block" src="./assets/images/icon/instagram.svg" alt="" /></a></li>
                            <li class="social__item"><a href="#"><img class="svg__icon d-block" src="./assets/images/icon/tiktok.svg" alt="" /></a></li>
                            <li class="social__item"><a href="#"><img class="svg__icon d-block" src="./assets/images/icon/youtube.svg" alt="" /></a></li>
                        </ul>
                    </div>
                </nav>

                <!-- ACTIONS (search + account + cart) -->
                <div class="header__action d-flex align-center">
                    <!-- Search -->
                    <div class="header__action--item d-flex align-center p-relative">
                        <form action="index.php?page=search" method="POST" class="d-flex align-center" id="search-box">
                            <input type="text" placeholder="Tìm kiếm sản phẩm ..." id="input-search" name="keyword" class="search__input" required>
                            <button type="submit" name="search" class="header__action--link search-btn p-absolute d-inline-block">
                                <img class="action__icon svg__icon d-block" src="./assets/images/icon/icon-search.svg" alt="search" />
                            </button>
                            <button type="button" class="header__action--link voice-btn p-absolute d-inline-block" id="search-btn" onclick="voiceInput();">
                                <img class="action__icon action__icon-off svg__icon d-block" src="./assets/images/icon/voice-icon.png" alt="search" />
                                <img class="action__icon action__icon-on svg__icon d-none" src="./assets/images/icon/mic-on.png" alt="search" />
                            </button>
                        </form>
                    </div>

                    <!-- Account dropdown (desktop) -->
                    <div class="header__action--item align-center d-none md-flex">
                        <?php if ($isLoggedIn) { ?>
                            <div class="account-dropdown">
                                <button type="button" class="header__action--link account-toggle">
                                    <img class="action__icon svg__icon d-block" src="./assets/images/icon/icon-person.svg" alt="person" />
                                    <span class="account-name">
                                        <?php echo htmlspecialchars($displayName, ENT_QUOTES, 'UTF-8'); ?>
                                    </span>
                                    <img class="svg__icon d-block" src="./assets/images/icon/icon-chevron-down.svg" alt="▼" />
                                </button>
                                <div class="account-menu">
                                    <a href="index.php?page=my_account&tab=account_info">Thông tin tài khoản</a>
                                    <a href="index.php?page=my_account&tab=account_order">Đơn hàng đang xử lý</a>
                                    <a href="index.php?page=my_account&tab=account_history">Lịch sử mua hàng</a>
                                    <a href="index.php?page=my_account&tab=account_settings">Cài đặt tài khoản</a>
                                    <a href="index.php?logout=1">Đăng xuất</a>
                                </div>
                            </div>
                        <?php } else { ?>
                            <a class="header__action--link d-inline-block" href="index.php?page=login">
                                <img class="action__icon svg__icon d-block" src="./assets/images/icon/icon-person.svg" alt="person" />
                            </a>
                        <?php } ?>
                    </div>

                    <!-- Cart -->
                    <div class="header__action--item d-flex align-center">
                        <a class="header__action--link d-inline-block" href="index.php?page=cart">
                            <?php if ($isLoggedIn && $cartCount > 0) { ?>
                                <div class="icon-cart d-flex align-center justify-center p-relative">
                                    <img class="action__icon svg__icon d-block" src="./assets/images/icon/cart-open.svg" alt="cart">
                                    <span class="cart-count p-absolute d-flex align-center justify-center h6"><?php echo $cartCount ?></span>
                                </div>
                            <?php } else { ?>
                                <img class="action__icon svg__icon d-block" src="./assets/images/icon/icon-cart.svg" alt="cart">
                            <?php } ?>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="header-nav-overlay"></div>
    </div>
</header>

<script>
    function voiceInput() {
        var inputSearch = document.getElementById('input-search');
        var searchBtn = document.querySelector('.voice-btn');
        const recognition = new webkitSpeechRecognition();
        recognition.lang = 'vi-VN';
        recognition.continuous = false;

        recognition.onresult = function(event) {
            const transcript = event.results[0][0].transcript;
            inputSearch.value = transcript;
        };

        recognition.onstart = function() {
            searchBtn.classList.add('recognizing');
        };

        recognition.onend = function() {
            searchBtn.classList.remove('recognizing');
        };

        recognition.onerror = function(event) {
            console.error(event.error);
        };

        recognition.start();
    }

    // Dropdown tài khoản: click để mở / click ra ngoài để đóng
    document.addEventListener('click', function (e) {
        const dropdown = document.querySelector('.account-dropdown');
        if (!dropdown) return;

        const toggle = dropdown.querySelector('.account-toggle');

        if (toggle.contains(e.target)) {
            dropdown.classList.toggle('open');
            return;
        }

        if (!dropdown.contains(e.target)) {
            dropdown.classList.remove('open');
        }
    });
</script>
