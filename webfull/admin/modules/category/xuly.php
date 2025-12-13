<?php
include('../../config/config.php');

/**
 * Lấy danh sách ID từ tham số ?data=
 */
function get_ids_from_data()
{
    if (!isset($_GET['data']) || $_GET['data'] === '') {
        return [];
    }

    $data    = $_GET['data'];
    $decoded = json_decode($data, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        return [];
    }

    if (is_array($decoded)) {
        return $decoded;
    }

    return [$decoded];
}

$category_id = isset($_GET['category_id']) ? (int)$_GET['category_id'] : 0;

/* ==========================================================
 * 1) THÊM DANH MỤC
 * ========================================================== */
if (isset($_POST['category_add'])) {

    $category_name        = $_POST['category_name']        ?? '';
    $category_description = $_POST['category_description'] ?? '';

    // Xử lý ảnh
    $category_image_name = $_FILES['category_image']['name']     ?? '';
    $category_image_tmp  = $_FILES['category_image']['tmp_name'] ?? '';
    $category_image      = '';

    if ($category_image_name !== '') {
        $category_image = time() . '_' . $category_image_name;
        move_uploaded_file($category_image_tmp, 'uploads/' . $category_image);
    }

    $sql_add = "
        INSERT INTO category(category_name, category_description, category_image)
        VALUE ('{$category_name}', '{$category_description}', '{$category_image}')
    ";

    mysqli_query($mysqli, $sql_add);
    header('Location: ../../index.php?action=category&query=category_list');
    exit;
}

/* ==========================================================
 * 2) SỬA DANH MỤC
 * ========================================================== */
elseif (isset($_POST['category_edit'])) {

    $category_name        = $_POST['category_name']        ?? '';
    $category_description = $_POST['category_description'] ?? '';

    $category_image_name = $_FILES['category_image']['name']     ?? '';
    $category_image_tmp  = $_FILES['category_image']['tmp_name'] ?? '';

    if ($category_image_name !== '') {

        // Tạo tên file mới
        $category_image = time() . '_' . $category_image_name;
        move_uploaded_file($category_image_tmp, 'uploads/' . $category_image);

        // Xóa ảnh cũ
        $sql   = "SELECT category_image FROM category WHERE category_id = '{$category_id}' LIMIT 1";
        $query = mysqli_query($mysqli, $sql);
        $row   = mysqli_fetch_array($query);

        if (!empty($row['category_image']) && file_exists('uploads/' . $row['category_image'])) {
            @unlink('uploads/' . $row['category_image']);
        }

        $sql_update = "
            UPDATE category 
            SET category_name        = '{$category_name}',
                category_description = '{$category_description}',
                category_image       = '{$category_image}'
            WHERE category_id = '{$category_id}'
        ";
    } else {
        // Không đổi ảnh
        $sql_update = "
            UPDATE category 
            SET category_name        = '{$category_name}',
                category_description = '{$category_description}'
            WHERE category_id = '{$category_id}'
        ";
    }

    mysqli_query($mysqli, $sql_update);
    header('Location: ../../index.php?action=category&query=category_list');
    exit;
}

/* ==========================================================
 * 3) XOÁ NHIỀU DANH MỤC (?data=[...])
 * ========================================================== */
else {

    $category_ids = get_ids_from_data();

    foreach ($category_ids as $id) {
        $id = (int)$id;
        if ($id <= 0) continue;

        // Lấy & xóa ảnh
        $sql   = "SELECT category_image FROM category WHERE category_id = '{$id}' LIMIT 1";
        $query = mysqli_query($mysqli, $sql);
        $row   = mysqli_fetch_array($query);

        if (!empty($row['category_image']) && file_exists('uploads/' . $row['category_image'])) {
            @unlink('uploads/' . $row['category_image']);
        }

        // Xóa record
        mysqli_query($mysqli, "DELETE FROM category WHERE category_id = '{$id}'");
    }

    header('Location: ../../index.php?action=category&query=category_list');
    exit;
}

?>
