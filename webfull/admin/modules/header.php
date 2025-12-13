<?php
// Lấy thông tin user nếu có
$admin_name  = $_SESSION['account_name'] ?? 'Admin';
$admin_email = $_SESSION['login'] ?? '';
?>

<nav class="navbar default-layout col-lg-12 col-12 p-0 fixed-top d-flex align-items-top flex-row">
    <div class="text-center navbar-brand-wrapper d-flex align-items-center justify-content-start">
        <div class="me-3">
            <button class="navbar-toggler navbar-toggler align-self-center" type="button" data-bs-toggle="minimize">
                <span class="icon-menu"></span>
            </button>
        </div>
        <div>
            <a class="navbar-brand brand-logo" href="index.php?action=home&query">
                <img src="images/logoadmin.png" alt="logo" />
            </a>
        </div>
    </div>

    <div class="navbar-menu-wrapper d-flex align-items-top">
        <ul class="navbar-nav">
            <li class="nav-item font-weight-semibold d-none d-lg-block ms-0">
                <h1 class="welcome-text m-0">
                    Xin chào,
                    <span class="text-black fw-bold"><?php echo htmlspecialchars($admin_name); ?></span>
                </h1>
            </li>
        </ul>

        <ul class="navbar-nav ms-auto">
            <!-- USER MENU -->
            <li class="nav-item dropdown d-none d-lg-block user-dropdown">
                <a class="nav-link" id="UserDropdown" href="#" data-bs-toggle="dropdown">
                    <img class="img-xs rounded-circle" src="images/user.png" alt="Profile image">
                </a>

                <div class="dropdown-menu dropdown-menu-right navbar-dropdown" aria-labelledby="UserDropdown">
                    <div class="dropdown-header text-center">
                        <img class="img-md rounded-circle" src="images/user.png" alt="Profile image">

                        <p class="mb-1 mt-3 font-weight-semibold">
                            <?php echo htmlspecialchars($admin_name); ?>
                        </p>

                        <p class="fw-light text-muted mb-0">
                            <?php echo htmlspecialchars($admin_email); ?>
                        </p>
                    </div>

                    <a href="index.php?action=account&query=my_account" class="dropdown-item">
                        <i class="dropdown-item-icon mdi mdi-account-outline text-primary me-2"></i>
                        My Profile
                    </a>

                    <a href="index.php?logout=1" class="dropdown-item">
                        <i class="dropdown-item-icon mdi mdi-power text-primary me-2"></i>
                        Đăng xuất
                    </a>
                </div>
            </li>
        </ul>

        <button class="navbar-toggler navbar-toggler-right d-lg-none align-self-center" type="button" data-bs-toggle="offcanvas">
            <span class="mdi mdi-menu"></span>
        </button>
    </div>
</nav>
