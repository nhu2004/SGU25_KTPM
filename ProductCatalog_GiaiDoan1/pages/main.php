<!-- start header -->
<?php
include("./pages/base/header.php");
?>
<!-- end header -->

<?php
    $action = isset($_GET['page']) ? $_GET['page'] : '';

    if ($action == 'about') {
        include("./pages/main/about.php");
    }
    elseif ($action == 'blog') {
        include("./pages/main/blog.php");
    }
    elseif ($action == 'article') {
        include("./pages/main/article.php");
    }
    elseif ($action == 'contact') {
        include("./pages/main/contact.php");
    }
    elseif ($action == 'products') {
        include("./pages/main/products.php");
    }
    elseif ($action == 'search') {
        include("./pages/main/search.php");
    }
    elseif ($action == 'product_detail') {
        include("./pages/main/product_detail.php");
    }
    elseif ($action == 'product_brand') {
        include("./pages/main/product_brand.php");
    }
    elseif ($action == 'product_category') {
        include("./pages/main/product_category.php");
    }
    elseif ($action == '404'){
        include("./pages/main/404.php");
    }
    else {
        include("./pages/main/home.php");
    }
?>

<!-- start footer -->
<?php
include("./pages/base/footer.php");
?>
<!-- end footer -->
