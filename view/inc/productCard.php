<div class="col-lg-3 col-md-6 col-sm-6 col-xs-6 mb-6 pro-gl-content">
    <div class="ec-product-inner">
        <div class="ec-pro-image-outer">
            <div class="ec-pro-image">
                <a href="viewproduct.php?id=<?= $productId ?>" class="image">
                    <img class="main-image" src="assets/images/product/<?= $productImage ?>" alt="Product" />
                    <img class="hover-image" src="assets/images/product/<?= $productImage ?>" alt="Product" />
                </a>
                <span class="flags"><span class="new">New</span></span>
                <div class="ec-pro-actions">
                    <a href="#" class="ec-btn-group quickview" data-id="<?= $productId ?>" title="Quick view"
                        data-bs-toggle="modal" data-bs-target="#ec_quickview_modal">
                        <i class="fi-rr-eye"></i>
                    </a>
                    <a href="javascript:void(0)" title="<?= $cartBtnTitle ?>"
                        class="ec-btn-group <?= $cartBtnClass ?>"
                        data-productid="<?= $productId ?>"
                        data-cartitemid="<?= $cartItemId ?>"
                        data-quantity="1"
                        data-action="add">
                        <i class="<?= $cartBtnIcon ?>"></i>
                    </a>
                </div>
            </div>
        </div>
        <div class="ec-pro-content">
            <a href="viewcategory.php?id=<?= $categoryId ?>">
                <h6 class="ec-pro-stitle"><?= $productCategory ?></h6>
            </a>
            <h5 class="ec-pro-title"><a href="viewproduct.php?id=<?= $productId ?>"><?= $productName ?></a></h5>
            <div class="ec-pro-rat-price">
                <span class="ec-pro-rating">
                    <i class="ecicon eci-star fill"></i>
                    <i class="ecicon eci-star fill"></i>
                    <i class="ecicon eci-star fill"></i>
                    <i class="ecicon eci-star fill"></i>
                    <i class="ecicon eci-star fill"></i>
                </span>
                <span class="ec-price">
                    <span class="new-price">£<?= $priceNew ?></span>
                    <?php if ($priceOld) echo "<span class='old-price'>£{$priceOld}</span>"; ?>
                </span>
            </div>
        </div>
    </div>
</div>