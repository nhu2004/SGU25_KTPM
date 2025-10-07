<?php
if (isset($_GET['logout']) && $_GET['logout'] == 1) {
    unset($_SESSION['account_email']);
    unset($_SESSION['account_id']);
    header('Location:index.php');
    exit;
}
?>
<style>
  /* chỉ giữ style nhỏ cho voice-btn, phần dropdown đã chuyển qua main.css */
  .voice-btn.recognizing .action__icon-on { display: block; }
  .voice-btn.recognizing .action__icon-off { display: none; }
</style>

<header class="header">
  <div class="header__topbar">
    <div class="container p-relative d-flex space-between align-center">
      <p class="h5">Miễn phí ship toàn quốc</p>
      <?php if (isset($_SESSION['account_email'])) { ?>
        <span class="h5 login-btn p-absolute">Tài khoản (Phase 2)</span>
      <?php } else { ?>
        <span class="h5 login-btn p-absolute">Đăng nhập (Phase 2)</span>
      <?php } ?>
    </div>
  </div>

  <div class="header__main">
    <div class="container">
      <div class="header__container d-grid middle-left">
        <!-- menu button -->
        <div class="header__btn md-none d-flex align-center">
          <div class="navbar__icons"><div class="navbar__icon"></div></div>
        </div>

        <!-- logo -->
        <div class="header__logo d-flex justify-center align-center">
          <a href="index.php" class="d-inline-block">
            <img class="d-block w-100 svg__icon" src="./assets/images/logo/logo_guha.png" alt="Logo" />
          </a>
        </div>

        <!-- navigation -->
        <nav class="header__nav space-between d-flex">
          <ul class="nav__list md-flex">

            <!-- CỬA HÀNG -->
            <li class="nav__item nav__items h7">
              <span class="nav__anchor nav__toggle p-relative h7 d-flex align-center space-between w-100 cursor-pointer">
                Cửa hàng
                <img class="md-none svg__icon" src="./assets/images/icon/icon-nextlink.svg" alt="next" />
                <img class="d-none md-block svg__icon" src="./assets/images/icon/icon-chevron-down.svg" alt="down" style="margin-left:8px" />
              </span>
              <ul class="header__subnav p-absolute">
                <li class="nav__item md-none h5">
                  <span class="nav__anchor cursor-pointer d-flex align-center">
                    <img class="md-none svg__icon" src="./assets/images/icon/arrow-left.svg" alt="" style="margin-right:8px" />
                    Cửa hàng
                  </span>
                </li>
                <li class="nav__item">
                  <a class="nav__anchor h7 d-flex align-center space-between" href="index.php?page=products">Tất cả sản phẩm</a>
                </li>
                <?php
                // Chỉ whitelist 2 danh mục: Nước hoa nữ, Nước hoa nam
                $whitelist = ['Nước hoa nữ','Nước hoa nam'];
                $sql = "SELECT category_id, category_name FROM category ORDER BY category_id DESC";
                if ($q = mysqli_query($mysqli, $sql)) {
                  while ($c = mysqli_fetch_assoc($q)) {
                    if (!in_array($c['category_name'], $whitelist, true)) continue; ?>
                    <li class="nav__item">
                      <a class="nav__anchor h7 d-flex align-center space-between"
                         href="index.php?page=products&category_id=<?= (int)$c['category_id']; ?>">
                         <?= htmlspecialchars($c['category_name']); ?>
                      </a>
                    </li>
                <?php }} ?>
              </ul>
            </li>

            <!-- THƯƠNG HIỆU -->
            <li class="nav__item nav__items h7">
              <span class="nav__anchor nav__toggle p-relative h7 d-flex align-center space-between w-100 cursor-pointer">
                Thương hiệu
                <img class="md-none svg__icon" src="./assets/images/icon/icon-nextlink.svg" alt="next" />
                <img class="d-none md-block svg__icon" src="./assets/images/icon/icon-chevron-down.svg" alt="down" style="margin-left:8px" />
              </span>
              <ul class="header__subnav p-absolute">
                <li class="nav__item md-none h5">
                  <span class="nav__anchor cursor-pointer d-flex align-center">
                    <img class="md-none svg__icon" src="./assets/images/icon/arrow-left.svg" alt="" style="margin-right:8px" />
                    Thương hiệu
                  </span>
                </li>
                <?php
                $sql = "SELECT brand_id, brand_name FROM brand ORDER BY brand_id DESC";
                if ($q = mysqli_query($mysqli, $sql)) {
                  while ($b = mysqli_fetch_assoc($q)) { ?>
                    <li class="nav__item">
                      <a class="nav__anchor h7 d-flex align-center space-between"
                         href="index.php?page=products&brand_id=<?= (int)$b['brand_id']; ?>">
                         <?= htmlspecialchars($b['brand_name']); ?>
                      </a>
                    </li>
                <?php }} ?>
              </ul>
            </li>

            <li class="nav__item"><a class="nav__anchor h7 d-flex align-center space-between" href="index.php?page=about">Giới thiệu</a></li>
            <li class="nav__item"><a class="nav__anchor h7 d-flex align-center space-between" href="index.php?page=blog">Blog</a></li>
            <li class="nav__item"><a class="nav__anchor h7 d-flex align-center space-between" href="index.php?page=contact">Liên hệ</a></li>
          </ul>
          <div class="flex-1"></div>
        </nav>

        <!-- actions: search, account, cart -->
        <div class="header__action d-flex align-center">
          <!-- Search -->
          <div class="header__action--item d-flex align-center p-relative">
            <form action="index.php?page=search" method="POST" class="d-flex align-center" id="search-box">
              <input type="text" placeholder="Tìm kiếm sản phẩm ..." id="input-search" name="keyword" class="search__input" required>
              <button type="submit" name="search" class="header__action--link search-btn p-absolute d-inline-block">
                <img class="action__icon svg__icon d-block" src="./assets/images/icon/icon-search.svg" alt="search" />
              </button>
              <button type="button" class="header__action--link voice-btn p-absolute d-inline-block" id="search-btn" onclick="voiceInput();">
                <img class="action__icon action__icon-off svg__icon d-block" src="./assets/images/icon/voice-icon.png" alt="voice" />
                <img class="action__icon action__icon-on svg__icon d-none" src="./assets/images/icon/mic-on.png" alt="voice-on" />
              </button>
            </form>
          </div>

          <!-- Account -->
          <div class="header__action--item align-center d-none md-flex">
            <a class="header__action--link d-inline-block" href="<?=
              isset($_SESSION['account_email'])
                ? 'index.php?page=my_account&tab=account_info'
                : 'index.php?page=login';
            ?>">
              <img class="action__icon svg__icon d-block" src="./assets/images/icon/icon-person.svg" alt="person" />
            </a>
          </div>

          <!-- Cart placeholder -->
          <div class="header__action--item d-flex align-center">
            <span class="h7 text-muted">Giỏ hàng (Phase 2)</span>
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
  recognition.onresult = function(e){ inputSearch.value = e.results[0][0].transcript; };
  recognition.onstart  = function(){ searchBtn.classList.add('recognizing'); };
  recognition.onend    = function(){ searchBtn.classList.remove('recognizing'); };
  recognition.onerror  = function(e){ console.error(e.error); };
  recognition.start();
}

// Dropdown: click mở/đóng; tự đóng menu khác; click ra ngoài thì đóng
document.addEventListener('DOMContentLoaded', function () {
  document.querySelectorAll('.nav__toggle').forEach(function(btn){
    btn.setAttribute('tabindex','0');
    ['click','keydown'].forEach(function(evt){
      btn.addEventListener(evt, function(e){
        if (e.type==='keydown' && !(e.key==='Enter'||e.key===' ')) return;
        e.preventDefault(); e.stopPropagation();
        const current = btn.closest('.nav__items');
        document.querySelectorAll('.nav__items.open').forEach(function(li){
          if (li !== current) li.classList.remove('open');
        });
        current.classList.toggle('open');
      });
    });
  });

  document.addEventListener('click', function(e){
    const inside = e.target.closest('.nav__items');
    if (!inside) document.querySelectorAll('.nav__items.open').forEach(function(li){ li.classList.remove('open'); });
  });
});
</script>


