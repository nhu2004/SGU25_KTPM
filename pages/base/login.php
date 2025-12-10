<?php
// Bắt đầu session (đồng bộ với index.php)
if (session_status() === PHP_SESSION_NONE) {
    session_name('guha');
    session_start();
}

/* ====== Kết nối DB nếu chưa có ====== */
if (!isset($mysqli) || !($mysqli instanceof mysqli)) {
    $candidates = [
        __DIR__ . '/../../admin/config/config.php',
        __DIR__ . '/../../../admin/config/config.php',
        __DIR__ . '/admin/config/config.php',
    ];
    foreach ($candidates as $p) {
        if (is_file($p)) {
            require_once $p;
            break;
        }
    }
}
if (isset($mysqli) && $mysqli instanceof mysqli) {
    @$mysqli->set_charset('utf8mb4');
}

// Biến lưu thông báo lỗi để hiển thị trên form
$login_error = '';

/* ====== Xử lý POST ĐĂNG NHẬP ====== */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $email    = isset($_POST['account_email']) ? trim($_POST['account_email']) : '';
    $password = isset($_POST['account_password']) ? trim($_POST['account_password']) : '';

    if ($email === '' || $password === '' || !isset($mysqli) || !($mysqli instanceof mysqli)) {
        $login_error = 'Tên đăng nhập hoặc mật khẩu không đúng.';
    } else {
        // Tìm account theo email
        $stmt = $mysqli->prepare("
            SELECT account_id, account_email, account_password, account_name, account_phone, account_type
            FROM account
            WHERE account_email = ?
            LIMIT 1
        ");
        $user = null;
        if ($stmt) {
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();
            $user   = $result ? $result->fetch_assoc() : null;
            $stmt->close();
        }

        if (!$user) {
            // Email không tồn tại
            $login_error = 'Tên đăng nhập hoặc mật khẩu không đúng.';
        } else {
            $hashInDb = (string)($user['account_password'] ?? '');
            $ok       = false;

            if ($hashInDb !== '') {
                // 1) DB dùng password_hash()
                if (password_verify($password, $hashInDb)) {
                    $ok = true;
                }
                // 2) DB lưu plain-text
                elseif ($password === $hashInDb) {
                    $ok = true;
                }
                // 3) DB lưu MD5 (đúng với luồng register hiện tại)
                elseif (strlen($hashInDb) === 32 && ctype_xdigit($hashInDb) && md5($password) === $hashInDb) {
                    $ok = true;
                }
            }

            if (!$ok) {
                $login_error = 'Tên đăng nhập hoặc mật khẩu không đúng.';
            } else {
                // === ĐĂNG NHẬP THÀNH CÔNG ===
                $_SESSION['account_id']    = (int)$user['account_id'];
                $_SESSION['account_email'] = (string)$user['account_email'];
                $_SESSION['account_name']  = (string)($user['account_name']  ?? '');
                $_SESSION['account_phone'] = (string)($user['account_phone'] ?? '');

                // Người mua → về Trang chủ (Phase3 cũng ok cho TC5)
                header('Location: index.php?page=home&message=success');
                exit;
            }
        }
    }
}
?>
<section class="login pd-section">
    <div class="form-box">
        <div class="form-value">

            <?php if (!empty($login_error)): ?>
                <div class="alert alert-error"
                     style="margin-bottom:16px;
                            padding:10px 12px;
                            border-radius:6px;
                            border:1px solid #ffbebe;
                            background:#ffecec;
                            color:#d93025;
                            font-weight:500;">
                    <?php echo htmlspecialchars($login_error, ENT_QUOTES, 'UTF-8'); ?>
                </div>
            <?php endif; ?>

            <!-- Self-post: action="" để nộp về chính trang này -->
            <form action="" autocomplete="on" method="POST">
                <h2 class="login-title">Đăng nhập</h2>

                <div class="inputbox">
                    <ion-icon name="mail-outline"></ion-icon>
                    <input type="email" name="account_email" required
                           value="<?php echo htmlspecialchars($_POST['account_email'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                    <label for="">Email</label>
                </div>

                <div class="inputbox">
                    <ion-icon name="lock-closed-outline"></ion-icon>
                    <input type="password" name="account_password" required>
                    <label for="">Password</label>
                </div>

                <div class="forget">
                    <label><input type="checkbox">Remember Me</label>
                    <a class="forget-link" href="index.php?page=forget_password">Forget Password</a>
                </div>

                <button type="submit" name="login">Đăng nhập</button>

                <div class="register">
                    <p>Chưa có tài khoản <a href="index.php?page=register">Đăng ký</a></p>
                </div>
            </form>
        </div>
    </div>
</section>

<script>
    // Nếu vẫn muốn toast khi đăng nhập thành công (từ redirect ?message=success)
    function showToast(type, message) {
        toast({
            title: type === "success" ? "Success" : "Error",
            message: message,
            type: type,
            duration: 3000,
        });
    }
    function showSuccessMessage() {
        showToast("success", "Đăng nhập thành công");
    }
</script>

<?php
// Toast khi ?message=success (đăng nhập thành công)
if (isset($_GET['message']) && $_GET['message'] == 'success') {
    echo '<script>';
    echo 'showSuccessMessage();';
    echo 'window.history.pushState(null, "", "index.php?page=login");';
    echo '</script>';
}
?>
