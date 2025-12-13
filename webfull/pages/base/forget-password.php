<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Xử lý form "Quên mật khẩu"
if (isset($_POST['reset_password'])) {
    $account_email    = trim($_POST['account_email'] ?? '');
    $password         = trim($_POST['account_password'] ?? '');
    $password_confirm = trim($_POST['account_password_confirm'] ?? '');

    // 1. Validate cơ bản
    if ($account_email === '' || $password === '' || $password_confirm === '') {
        echo '<script>alert("Vui lòng nhập đầy đủ thông tin.");</script>';
    } elseif ($password !== $password_confirm) {
        echo '<script>alert("Mật khẩu nhập lại không khớp.");</script>';
    } else {
        // 2. Kiểm tra email có tồn tại không
        $account_email_safe = mysqli_real_escape_string($mysqli, $account_email);
        $sql_getacc = "
            SELECT * FROM account 
            WHERE account_email = '" . $account_email_safe . "'
            LIMIT 1
        ";
        $query_getacc = mysqli_query($mysqli, $sql_getacc);
        $count        = mysqli_num_rows($query_getacc);

        if ($count == 1) {
            // 3. Cập nhật mật khẩu (giữ md5 cho đồng bộ project)
            $account_password_hash = md5($password);

            $sql_forget = "
                UPDATE account 
                   SET account_password = '$account_password_hash' 
                 WHERE account_email    = '" . $account_email_safe . "'
                LIMIT 1
            ";
            $query_forget = mysqli_query($mysqli, $sql_forget);

            if ($query_forget) {
                // Alert + redirect về login bằng JS để tránh lỗi header
                echo '<script>
                        alert("Lấy lại mật khẩu thành công. Vui lòng đăng nhập lại.");
                        window.location = "index.php?page=login";
                      </script>';
                exit;
            } else {
                echo '<script>alert("Có lỗi khi cập nhật mật khẩu. Vui lòng thử lại sau.");</script>';
            }
        } else {
            // Không tìm thấy email trong hệ thống
            echo '<script>alert("Email không tồn tại trong hệ thống. Vui lòng kiểm tra lại.");</script>';
        }
    }
}
?>
<script src="./assets/js/form_register.js"></script>

<section class="register pd-bottom">
    <div class="container">
        <div class="w-100 text-center p-relative">
            <div class="title">Lấy lại mật khẩu</div>
            <div class="title-line"></div>
        </div>
        <div class="content">
            <form class="register__form" action="" method="POST">
                <div class="user-details">
                    <div class="input-box">
                        <span class="details">Email</span>
                        <input
                            class="input-form"
                            onchange="getInputChange();"
                            type="email"
                            name="account_email"
                            placeholder="Nhập vào địa chỉ email"
                            required
                        >
                    </div>
                    <div class="input-box">
                        <span class="details">Mật khẩu mới</span>
                        <input
                            class="input-form"
                            onchange="getInputChange();"
                            type="password"
                            name="account_password"
                            placeholder="Nhập vào mật khẩu mới"
                            required
                        >
                    </div>
                    <div class="input-box">
                        <span class="details">Nhập lại mật khẩu mới</span>
                        <input
                            class="input-form"
                            onchange="getInputChange();"
                            type="password"
                            name="account_password_confirm"
                            placeholder="Nhập lại mật khẩu mới"
                            required
                        >
                    </div>
                </div>
                <div class="button">
                    <input type="submit" name="reset_password" value="Đổi mật khẩu">
                </div>
            </form>
            <div class="w-100 text-center">
                <p class="h4">
                    Đã có tài khoản?
                    <a class="text-login" href="index.php?page=login">Đăng nhập</a>
                </p>
            </div>
        </div>
    </div>
</section>
