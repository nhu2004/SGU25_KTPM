<?php
include('../../config/config.php');

/**
 * Helper: lấy danh sách id từ tham số ?data= (JSON)
 * Trả về mảng, hoặc [] nếu không hợp lệ.
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

// product_id nếu có trên URL
$product_id = isset($_GET['product_id']) ? (int)$_GET['product_id'] : 0;

/* =====================================================
 * 1) THÊM SẢN PHẨM
 * ===================================================== */
if (isset($_POST['product_add'])) {

    $product_name     = $_POST['product_name']     ?? '';
    $product_brand    = $_POST['product_brand']    ?? '';
    $product_capacity = $_POST['product_capacity'] ?? '';
    $product_category = $_POST['product_category'] ?? '';

    // Các field số – ép về int, nếu rỗng thì = 0 để tránh lỗi SQL
    $product_price_import = isset($_POST['product_price_import']) && $_POST['product_price_import'] !== ''
        ? (int)$_POST['product_price_import'] : 0;

    $product_price = isset($_POST['product_price']) && $_POST['product_price'] !== ''
        ? (int)$_POST['product_price'] : 0;

    $product_sale = isset($_POST['product_sale']) && $_POST['product_sale'] !== ''
        ? (int)$_POST['product_sale'] : 0;

    $product_description = $_POST['product_description'] ?? '';
    $product_status      = isset($_POST['product_status']) && $_POST['product_status'] !== ''
        ? (int)$_POST['product_status'] : 0;

    // xử lý ảnh
    $product_image_name = $_FILES['product_image']['name']     ?? '';
    $product_image_tmp  = $_FILES['product_image']['tmp_name'] ?? '';
    $product_image      = '';

    if ($product_image_name !== '') {
        $product_image = time() . '_' . $product_image_name;
        move_uploaded_file($product_image_tmp, 'uploads/' . $product_image);
    }

    $sql_add = "
        INSERT INTO product(
            product_name,
            product_category,
            product_brand,
            capacity_id,
            product_price_import,
            product_price,
            product_sale,
            product_description,
            product_image,
            product_status
        )
        VALUE(
            '{$product_name}',
            '{$product_category}',
            '{$product_brand}',
            '{$product_capacity}',
            '{$product_price_import}',
            '{$product_price}',
            '{$product_sale}',
            '{$product_description}',
            '{$product_image}',
            '{$product_status}'
        )
    ";

    mysqli_query($mysqli, $sql_add);

    header('Location: ../../index.php?action=product&query=product_list&message=success');
    exit;
}

/* =====================================================
 * 2) SỬA SẢN PHẨM
 * ===================================================== */
elseif (isset($_POST['product_edit'])) {

    $product_name     = $_POST['product_name']     ?? '';
    $product_brand    = $_POST['product_brand']    ?? '';
    $product_capacity = $_POST['product_capacity'] ?? '';
    $product_category = $_POST['product_category'] ?? '';

    $product_price_import = isset($_POST['product_price_import']) && $_POST['product_price_import'] !== ''
        ? (int)$_POST['product_price_import'] : 0;

    $product_price = isset($_POST['product_price']) && $_POST['product_price'] !== ''
        ? (int)$_POST['product_price'] : 0;

    $product_sale = isset($_POST['product_sale']) && $_POST['product_sale'] !== ''
        ? (int)$_POST['product_sale'] : 0;

    $product_description = $_POST['product_description'] ?? '';
    $product_status      = isset($_POST['product_status']) && $_POST['product_status'] !== ''
        ? (int)$_POST['product_status'] : 0;

    $product_image_name = $_FILES['product_image']['name']     ?? '';
    $product_image_tmp  = $_FILES['product_image']['tmp_name'] ?? '';

    if ($product_image_name !== '') {
        // upload ảnh mới
        $product_image = time() . '_' . $product_image_name;
        move_uploaded_file($product_image_tmp, 'uploads/' . $product_image);

        // xóa ảnh cũ
        $sql   = "SELECT * FROM product WHERE product_id = '{$product_id}' LIMIT 1";
        $query = mysqli_query($mysqli, $sql);
        while ($row = mysqli_fetch_array($query)) {
            if (!empty($row['product_image']) && file_exists('uploads/' . $row['product_image'])) {
                @unlink('uploads/' . $row['product_image']);
            }
        }

        $sql_update = "
            UPDATE product SET
                product_name         = '{$product_name}',
                product_brand        = '{$product_brand}',
                capacity_id          = '{$product_capacity}',
                product_category     = '{$product_category}',
                product_price_import = '{$product_price_import}',
                product_price        = '{$product_price}',
                product_sale         = '{$product_sale}',
                product_description  = '{$product_description}',
                product_image        = '{$product_image}',
                product_status       = '{$product_status}'
            WHERE product_id        = '{$product_id}'
        ";
    } else {
        // không đổi ảnh
        $sql_update = "
            UPDATE product SET
                product_name         = '{$product_name}',
                product_brand        = '{$product_brand}',
                capacity_id          = '{$product_capacity}',
                product_category     = '{$product_category}',
                product_price_import = '{$product_price_import}',
                product_price        = '{$product_price}',
                product_sale         = '{$product_sale}',
                product_description  = '{$product_description}',
                product_status       = '{$product_status}'
            WHERE product_id        = '{$product_id}'
        ";
    }

    mysqli_query($mysqli, $sql_update);
    header('Location: ../../index.php?action=product&query=product_list&message=success');
    exit;
}

/* =====================================================
 * 3) SET GIẢM GIÁ HÀNG LOẠT (product_sale) CHO NHIỀU SP
 *     - ?data=[id1,id2,...]&product_sale=20
 * ===================================================== */
elseif (isset($_GET['product_sale'])) {

    $sale        = (int)$_GET['product_sale'];
    $product_ids = get_ids_from_data();

    if (!empty($product_ids)) {
        foreach ($product_ids as $id) {
            $id = (int)$id;
            if ($id <= 0) continue;

            $sql_sale = "UPDATE product SET product_sale = {$sale} WHERE product_id = '{$id}'";
            mysqli_query($mysqli, $sql_sale);
        }
    }

    header('Location: ../../index.php?action=product&query=product_list&message=success');
    exit;
}

/* =====================================================
 * 4) XÓA ĐÁNH GIÁ (deleteevaluate = 1)
 *     - ?data=[evaluate_id1,evaluate_id2,...]&deleteevaluate=1
 * ===================================================== */
elseif (isset($_GET['deleteevaluate']) && (int)$_GET['deleteevaluate'] === 1) {

    $evaluate_ids = get_ids_from_data();

    if (!empty($evaluate_ids)) {
        foreach ($evaluate_ids as $id) {
            $id = (int)$id;
            if ($id <= 0) continue;

            $sql_delete_evaluate = "DELETE FROM evaluate WHERE evaluate_id = '{$id}'";
            mysqli_query($mysqli, $sql_delete_evaluate);
        }
    }

    header('Location: ../../index.php?action=product&query=product_edit&product_id=' . $product_id . '&message=success#product_evaluate');
    exit;
}

/* =====================================================
 * 5) ĐÁNH DẤU SPAM ĐÁNH GIÁ (spamevaluate = 1)
 * ===================================================== */
elseif (isset($_GET['spamevaluate']) && (int)$_GET['spamevaluate'] === 1) {

    $evaluate_ids = get_ids_from_data();

    if (!empty($evaluate_ids)) {
        foreach ($evaluate_ids as $id) {
            $id = (int)$id;
            if ($id <= 0) continue;

            $sql_update_evaluate = "UPDATE evaluate SET evaluate_status = -1 WHERE evaluate_id = '{$id}'";
            mysqli_query($mysqli, $sql_update_evaluate);
        }
    }

    header('Location: ../../index.php?action=product&query=product_edit&product_id=' . $product_id . '&message=success#product_evaluate');
    exit;
}

/* =====================================================
 * 6) XOÁ SẢN PHẨM HÀNG LOẠT (MẶC ĐỊNH)
 *     - ?data=[id1,id2,...]
 * ===================================================== */
else {

    $product_ids = get_ids_from_data();

    if (!empty($product_ids)) {
        foreach ($product_ids as $id) {
            $id = (int)$id;
            if ($id <= 0) continue;

            // xóa ảnh
            $sql   = "SELECT * FROM product WHERE product_id = '{$id}' LIMIT 1";
            $query = mysqli_query($mysqli, $sql);
            while ($row = mysqli_fetch_array($query)) {
                if (!empty($row['product_image']) && file_exists('uploads/' . $row['product_image'])) {
                    @unlink('uploads/' . $row['product_image']);
                }
            }

            // xóa record
            $sql_delete = "DELETE FROM product WHERE product_id = '{$id}'";
            mysqli_query($mysqli, $sql_delete);
        }
    }

    header('Location: ../../index.php?action=product&query=product_list&message=success');
    exit;
}
?>
