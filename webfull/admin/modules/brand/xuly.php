<?php
include('../../config/config.php');

function get_ids_from_data()
{
    if (!isset($_GET['data']) || $_GET['data'] === '') {
        return [];
    }

    $decoded = json_decode($_GET['data'], true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        return [];
    }

    if (is_array($decoded)) return $decoded;
    return [$decoded];
}

$brand_id = isset($_GET['brand_id']) ? (int)$_GET['brand_id'] : 0;

/* ===== 1) THÊM ===== */
if (isset($_POST['brand_add'])) {

    $brand_name = $_POST['brand_name'] ?? '';

    $sql_add = "INSERT INTO brand(brand_name) VALUE ('{$brand_name}')";
    mysqli_query($mysqli, $sql_add);

    header('Location: ../../index.php?action=brand&query=brand_list&message=success');
    exit;
}

/* ===== 2) SỬA ===== */
elseif (isset($_POST['brand_edit'])) {

    $brand_name = $_POST['brand_name'] ?? '';

    $sql_update = "
        UPDATE brand 
        SET brand_name = '{$brand_name}'
        WHERE brand_id = '{$brand_id}'
    ";
    mysqli_query($mysqli, $sql_update);

    header('Location: ../../index.php?action=brand&query=brand_list&message=success');
    exit;
}

/* ===== 3) XOÁ NHIỀU ===== */
else {
    $brand_ids = get_ids_from_data();

    foreach ($brand_ids as $id) {
        $id = (int)$id;
        if ($id <= 0) continue;

        mysqli_query($mysqli, "DELETE FROM brand WHERE brand_id = '{$id}'");
    }

    header('Location: ../../index.php?action=brand&query=brand_list&message=success');
    exit;
}
?>
