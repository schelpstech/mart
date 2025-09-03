<!-- Category Sidebar start -->
<div class="ec-side-cat-overlay"></div>
<div class="col-lg-3 category-sidebar sidebar-dis-991" data-animation="fadeIn">
    <div class="cat-sidebar">
        <div class="cat-sidebar-box">
            <div class="ec-sidebar-wrap">
                <!-- Sidebar Category Block -->
                <div class="ec-sidebar-block">
                    <div class="ec-sb-title">
                        <h3 class="ec-sidebar-title">Category<button class="ec-close">×</button></h3>
                    </div>
                    <div class="ec-sb-block-content">
                        <ul>
                            <?php
                            // Fetch all categories
                            $categories = $model->getRows('categories');
                            if ($categories) {
                                foreach ($categories as $cat) {
                                    $catId   = $cat['categoryTbl_id'];
                                    $catName = htmlspecialchars($cat['category_name']);


                            ?>
                                    <li>
                                        <div class="ec-sidebar-block-item">
                                            <img src="assets/images/icons/dress-8.png" class="svg_img" alt="<?= $catName ?>" />
                                            <?= $catName ?> 
                                        </div>

                                        <?php
                                        // Fetch products for this category
                                        $products = $model->getRows('products', [
                                            'where' => ['category_id' => $catId]
                                        ]);

                                        if ($products) {
                                        ?>
                                            <ul style="display: block;">
                                                <?php foreach ($products as $product) { ?>
                                                    <li>
                                                        <div class="ec-sidebar-sub-item">
                                                            <a href="viewproduct.php?id=<?= $product['product_id'] ?>">
                                                                <?= htmlspecialchars($product['product_name']) ?>
                                                                <span title="Available Stock">- <?= (int)$product['stock_quantity'] ?></span>
                                                            </a>
                                                        </div>
                                                    </li>
                                                <?php } ?>
                                            </ul>
                                        <?php } ?>
                                    </li>
                            <?php
                                }
                            }
                            ?>
                        </ul>
                    </div>
                </div>
                <!-- Sidebar Category Block -->
            </div>
        </div>
    </div>
</div>