<?php
// Đồng bộ session với phần còn lại (tên session "guha")
if (session_status() === PHP_SESSION_NONE) {
    session_name('guha');
    session_start();
}

// Kiểm tra đăng nhập
$loggedIn = !empty($_SESSION['account_id']) || !empty($_SESSION['account_email']);
if (!$loggedIn) {
    header('Location: index.php?page=login');
    exit;
}

// Tab hiện tại
$tab = isset($_GET['tab']) ? trim($_GET['tab']) : 'account_info';
$allowTabs = ['account_info', 'account_order', 'account_history', 'account_settings'];
if (!in_array($tab, $allowTabs, true)) {
    $tab = 'account_info';
}
?>
<section class="my-account pd-section">
    <div class="container">
        <h3 class="h4 my-account__heading">Chào mừng bạn đến với trang tổng quan tài khoản</h3>
        <div class="my-account__container">
            <div class="row">
                <!-- MENU BÊN TRÁI -->
                <div class="col" style="--w-md: 3">
                    <ul class="my-account__menu">
                        <li class="my-account__item <?php if($tab === 'account_info') echo 'active'; ?>">
                            <a href="index.php?page=my_account&tab=account_info">Thông tin tài khoản</a>
                        </li>
                        <li class="my-account__item <?php if($tab === 'account_order') echo 'active'; ?>">
                            <a href="index.php?page=my_account&tab=account_order">Đơn hàng đang xử lý</a>
                        </li>
                        <li class="my-account__item <?php if($tab === 'account_history') echo 'active'; ?>">
                            <a href="index.php?page=my_account&tab=account_history">Lịch sử mua hàng</a>
                        </li>
                        <li class="my-account__item <?php if($tab === 'account_settings') echo 'active'; ?>">
                            <a href="index.php?page=my_account&tab=account_settings">Cài đặt tài khoản</a>
                        </li>
                        <li class="my-account__item">
                            <a href="index.php?logout=1" onclick="return confirm('Bạn có muốn đăng xuất không?')">Đăng xuất</a>
                        </li>
                    </ul>
                </div>

                <!-- NỘI DUNG BÊN PHẢI -->
                <div class="col" style="--w-md: 9">
                    <?php
                        switch ($tab) {
                            case 'account_order':
                                include('./pages/base/account-order.php');
                                break;

                            case 'account_history':
                                include('./pages/base/account-history.php');
                                break;

                            case 'account_settings':
                                include('./pages/base/account-settings.php');
                                break;

                            case 'account_info':
                            default:
                                include('./pages/base/account-info.php');
                                break;
                        }
                    ?>
                </div>
            </div>
        </div>
    </div>
</section>
