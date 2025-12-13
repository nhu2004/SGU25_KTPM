<?php
// Đọc message từ query để hiển thị thông báo lỗi/ thành công
$message = $_GET['message'] ?? '';
$register_message = '';

switch ($message) {
    case 'exists':
        $register_message = 'Email đã được sử dụng.';
        break;
    case 'password_mismatch':
        $register_message = 'Mật khẩu nhập lại không khớp.';
        break;
    case 'invalid_email':
        $register_message = 'Định dạng email không hợp lệ.';
        break;
    case 'invalid':
        $register_message = 'Vui lòng nhập đầy đủ thông tin.';
        break;
    case 'error':
        $register_message = 'Đã xảy ra lỗi, vui lòng thử lại.';
        break;
    default:
        $register_message = '';
}
?>
<script src="./assets/js/form_register.js"></script>

<section class="register pd-section">
    <div class="container">
        <div class="w-100 text-center p-relative">
            <div class="title">
                <h3 class="heading h3">Thành viên đăng ký</h3>
                <p class="desc">Đăng ký tài khoản ngay để mua hàng tại Guha Perfume ❤️</p>
            </div>
            <div class="title-line"></div>
        </div>

        <?php if (!empty($register_message)): ?>
            <div class="alert alert-error" style="margin: 16px 0; padding: 10px; border: 1px solid #f00; color: #c00;">
                <?php echo htmlspecialchars($register_message, ENT_QUOTES, 'UTF-8'); ?>
            </div>
        <?php endif; ?>

        <div class="content">
            <form class="register__form"
                  action="pages/handle/register.php"
                  id="form-register"
                  method="POST"
                  autocomplete="off">
                <div class="user-details">
                    <div class="input-box form-group">
                        <label class="details form-label">Họ Tên</label>
                        <input class="input-form" id="account_name" onchange="getInputChange();"
                               type="text" name="account_name" placeholder="Nhập vào tên của bạn"
                               required autocomplete="off">
                        <span class="form-message"></span>
                    </div>
                    <div class="input-box form-group">
                        <label class="details form-label">Địa chỉ</label>
                        <input class="input-form" id="account_address" onchange="getInputChange();"
                               type="text" name="customer_address" placeholder="Nhập vào địa chỉ của bạn"
                               required autocomplete="off">
                        <span class="form-message"></span>
                    </div>
                    <div class="input-box form-group">
                        <label class="details form-label">Email</label>
                        <input class="input-form" id="account_email" onchange="getInputChange();"
                               type="email" name="account_email" placeholder="Nhập vào địa chỉ email"
                               required autocomplete="off">
                        <span class="form-message"></span>
                    </div>
                    <div class="input-box form-group">
                        <label class="details form-label">Số điện thoại</label>
                        <input class="input-form" id="account_phone" onchange="getInputChange();"
                               type="text" name="account_phone" placeholder="Nhập vào số điện thoại"
                               required autocomplete="off">
                        <span class="form-message"></span>
                    </div>
                    <div class="input-box form-group">
                        <label class="details form-label">Mật khẩu</label>
                        <input class="input-form" id="account_password" onchange="getInputChange();"
                               type="password" name="account_password" placeholder="Nhập vào mật khẩu"
                               required autocomplete="new-password">
                        <span class="form-message"></span>
                    </div>
                    <div class="input-box form-group">
                        <label class="details form-label">Nhập lại mật khẩu</label>
                        <input class="input-form" id="account_password2" onchange="getInputChange();"
                               type="password" name="account_password_confirn" placeholder="Nhập lại mật khẩu"
                               required autocomplete="new-password">
                        <span class="form-message"></span>
                    </div>
                </div>
                <div class="gender-details">
                    <input type="radio" name="account_gender" value="0" id="dot-1" checked>
                    <input type="radio" name="account_gender" value="1" id="dot-2">
                    <input type="radio" name="account_gender" value="2" id="dot-3">
                    <span class=" form-label">Giới tính:</span>
                    <div class="category">
                        <label for="dot-1">
                            <span class="dot one"></span>
                            <span class="gender">Không xác định</span>
                        </label>
                        <label for="dot-2">
                            <span class="dot two"></span>
                            <span class="gender">Nam</span>
                        </label>
                        <label for="dot-3">
                            <span class="dot three"></span>
                            <span class="gender">Nữ</span>
                        </label>
                    </div>
                </div>
                <div class="button">
                    <input type="submit" name="register" value="Đăng ký">
                </div>
            </form>
            <div class="w-100 text-center">
                <p class="h5">Đã có tài khoản <a class="text-login" href="index.php?page=login">Đăng nhập</a></p>
            </div>
        </div>
    </div>
</section>

<script>
    Validator({
        form: '#form-register',
        errorSelector: '.form-message',
        rules: [
            Validator.isRequired('#account_name', 'Vui lòng nhập tên đầy đủ của bạn'),
            Validator.isRequired('#account_email', 'Vui lòng nhập email'),
            Validator.isEmail('#account_email', 'Định dạng email không hợp lệ'),
            Validator.isRequired('#account_address'),
            Validator.isRequired('#account_phone'),
            Validator.isRequired('#account_password'),
            Validator.minLength('#account_password', 6),
            Validator.isRequired('#account_password2'),
            Validator.isConfirmed('#account_password2', function() {
                return document.querySelector('#form-register #account_password').value;
            }, 'Mật khẩu nhập lại không khớp')
        ],
        onSubmit: function (data) {
            // tất cả hợp lệ → submit form xuống PHP
            document.getElementById('form-register').submit();
        }
    });
</script>
