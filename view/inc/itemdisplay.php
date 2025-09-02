<?php
$productId   = $product['product_id'];
$name        = $product['product_name'];
$price       = $product['price'];
$oldPrice    = $product['discount_price'] ?? null;
$imageMain   = $product['image_main'];
$imageHover  = $product['image_gallery'];
$category    = $product['category_name'];
$slug        = "viewproduct.php?id=" . $productId;
?>

<div class="col-lg-3 col-md-6 col-sm-6 col-xs-6 mb-6 pro-gl-content">
    <div class="ec-product-inner">
        <div class="ec-pro-image-outer">
            <div class="ec-pro-image">
                <a href="<?= $slug ?>" class="image">
                    <img class="main-image" src="assets/images/product/<?= $imageMain ?>" alt="<?= htmlspecialchars($name) ?>" />
                    <?php if ($imageHover) { ?>
                        <img class="hover-image" src="assets/images/product/<?= $imageHover ?>" alt="<?= htmlspecialchars($name) ?>" />
                    <?php } ?>
                </a>
                <a href="#" class="quickview" data-link-action="quickview"
                    title="Quick view" data-bs-toggle="modal"
                    data-bs-target="#ec_quickview_modal"><i class="fi-rr-eye"></i></a>
                <div class="ec-pro-actions">
                    <button title="Add To Cart" class="add-to-cart" data-id="<?= $productId ?>">
                        <i class="fi-rr-shopping-basket"></i> Add To Cart
                    </button>
                </div>
            </div>
        </div>
        <div class="ec-pro-content">
            <h5 class="ec-pro-title"><a href="<?= $slug ?>"><?= htmlspecialchars($name) ?></a></h5>
            <div class="ec-pro-rating">
                <i class="ecicon eci-star fill"></i>
                <i class="ecicon eci-star fill"></i>
                <i class="ecicon eci-star fill"></i>
                <i class="ecicon eci-star fill"></i>
                <i class="ecicon eci-star"></i>
            </div>
            <div class="ec-pro-list-desc">
                <?= substr($product['product_description'], 0, 80) ?>...
            </div>
            <span class="ec-price">
                <?php if ($oldPrice) { ?>
                    <span class="old-price">£<?= number_format($oldPrice) ?></span>
                <?php } ?>
                <span class="new-price">£<?= number_format($price) ?></span>
            </span>
        </div>
    </div>
</div>
