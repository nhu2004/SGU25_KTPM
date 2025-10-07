<?php
// === Lấy id danh mục Nam / Nữ
$cat_male_id = $cat_female_id = 0;
if ($res = mysqli_query($mysqli, "SELECT category_id, category_name FROM category")) {
    while ($r = mysqli_fetch_assoc($res)) {
        if ($r['category_name'] === 'Nước hoa nam')  $cat_male_id   = (int)$r['category_id'];
        if ($r['category_name'] === 'Nước hoa nữ')   $cat_female_id = (int)$r['category_id'];
    }
}

// === Đường dẫn uploads
$uploadsRel = 'admin/modules/category/uploads';

// === Gán 3 ảnh
$female_img = '1684378870_nuoc-hoa-duoc-yeu-thich-nhat.jpg'; // Nữ
$male_img   = '1684378880_qebq3g-16571737703471658983260.webp'; // Nam
$banner_img = '1684378853_pexels-anis-salmani-11711835.jpg'; // Chai vàng (ảnh lớn bên trái)

// === Tạo URL
$female_url = $uploadsRel . '/' . rawurlencode($female_img);
$male_url   = $uploadsRel . '/' . rawurlencode($male_img);
$banner_url = $uploadsRel . '/' . rawurlencode($banner_img);

// === Link danh mục
$link_female = $cat_female_id ? "index.php?page=products&category_id={$cat_female_id}" : "index.php?page=products";
$link_male   = $cat_male_id   ? "index.php?page=products&category_id={$cat_male_id}"   : "index.php?page=products";
?>

<section class="category" style="margin-top:32px">
  <div class="container">
    <h2 class="h2" style="margin-bottom:20px;">Danh mục</h2>

    <div class="category-grid"
         style="
           display:grid;
           grid-template-columns: 1.25fr 1fr;
           grid-template-rows: repeat(2, 1fr);
           gap:24px;
           align-items: stretch;
           min-height: 440px;
         ">

      <!-- Ảnh lớn bên trái: chai vàng -->
      <div style="
           grid-row: 1 / span 2;
           display:block;
           position:relative;
           border-radius:16px;
           overflow:hidden;
           width:100%;
           height:100%;
         ">
        <img src='<?= htmlspecialchars($banner_url) ?>' alt='Nước hoa vàng'
             style='width:100%;height:100%;object-fit:cover;display:block;'>
      </div>

      <!-- Ảnh trên phải: Nước hoa nam -->
      <a href='<?= htmlspecialchars($link_male) ?>'
         style='display:block;position:relative;border-radius:16px;overflow:hidden;width:100%;height:100%;'>
        <img src='<?= htmlspecialchars($male_url) ?>' alt='Nước hoa nam'
             style='width:100%;height:100%;object-fit:cover;display:block;'>
        <span style="
          position:absolute;left:16px;bottom:16px;
          background:rgba(0,0,0,.55);color:#fff;
          padding:10px 16px;border-radius:999px;
          font-weight:600;font-size:15px;
        ">Nước hoa nam</span>
      </a>

      <!-- Ảnh dưới phải: Nước hoa nữ -->
      <a href='<?= htmlspecialchars($link_female) ?>'
         style='display:block;position:relative;border-radius:16px;overflow:hidden;width:100%;height:100%;'>
        <img src='<?= htmlspecialchars($female_url) ?>' alt='Nước hoa nữ'
             style='width:100%;height:100%;object-fit:cover;display:block;'>
        <span style="
          position:absolute;left:16px;bottom:16px;
          background:rgba(0,0,0,.55);color:#fff;
          padding:10px 16px;border-radius:999px;
          font-weight:600;font-size:15px;
        ">Nước hoa nữ</span>
      </a>

    </div>
  </div>
</section>
