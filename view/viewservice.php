<?php
include './inc/head.php';
?>

<body class="service_page">
    <div id="ec-overlay">
        <div class="ec-ellipsis">
            <div></div><div></div><div></div><div></div>
        </div>
    </div>

    <?php
    include './inc/header.php';
    include './inc/cart.php';
    include './inc/category.php';

    if (isset($_GET['slug']) && !empty($_GET['slug'])) {
        $serviceSlug = $_GET['slug'];

        // Fetch service details from DB
        $serviceData = $model->getRows("services", [
            "where" => ["slug" => $serviceSlug],
            "return_type" => "single"
        ]);

        if (!$serviceData) {
            $utility->setFlash("danger", "Service not found.");
            header("Location: servicemgr.php");
            exit;
        }
    } else {
        $utility->setFlash("danger", "No service slug provided.");
        header("Location: servicemgr.php");
        exit;
    }
    ?>

    <!-- Single Service Page -->
    <section class="ec-page-content section-space-p">
        <div class="container">
            <div class="row">
                <div class="sticky-header-next-sec ec-breadcrumb section-space-mb">
                    <div class="container">
                        <div class="row">
                            <div class="col-12">
                                <div class="row ec_breadcrumb_inner">
                                    <div class="col-md-6 col-sm-12">
                                        <h2 class="ec-breadcrumb-title"><?= htmlspecialchars($serviceData['name']); ?></h2>
                                    </div>
                                    <div class="col-md-6 col-sm-12">
                                        <ul class="ec-breadcrumb-list">
                                            <li class="ec-breadcrumb-item"><a href="index.php">Home</a></li>
                                            <li class="ec-breadcrumb-item active"><?= htmlspecialchars($serviceData['name']); ?></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="ec-pro-rightside ec-common-rightside col-lg-12 col-md-12">
                    <div class="single-pro-block">
                        <div class="single-pro-inner">
                            <div class="row">
                                <!-- Service Image -->
                                <div class="single-pro-img single-pro-img-no-sidebar">
                                    <div class="single-product-scroll">
                                        <div class="single-product-cover">
                                            <div class="single-slide">
                                                <img class="img-responsive"
                                                     src="assets/images<?= htmlspecialchars($serviceData['image']); ?>"
                                                     alt="<?= htmlspecialchars($serviceData['slug']); ?>">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Service Description -->
                                <div class="single-pro-desc single-pro-desc-no-sidebar">
                                    <div class="single-pro-content">
                                        <h5 class="ec-single-title"><?= htmlspecialchars($serviceData['name']); ?></h5>

                                        <div class="ec-single-rating-wrap">
                                            <div class="ec-single-rating">
                                                <i class="ecicon eci-star fill"></i>
                                                <i class="ecicon eci-star fill"></i>
                                                <i class="ecicon eci-star fill"></i>
                                                <i class="ecicon eci-star fill"></i>
                                                <i class="ecicon eci-star"></i>
                                            </div>
                                            <span class="text-muted small"> 4.0 (Average Rating)</span>
                                        </div>

                                        <div class="ec-single-desc mt-2">
                                            <?= nl2br(htmlspecialchars($serviceData['description'])); ?>
                                        </div>

                                        <div class="ec-single-price-stoke mt-4">
                                            <div class="ec-single-price">
                                                <span class="ec-single-ps-title">Service Price:</span>
                                                <span class="new-price">£ <?= number_format($serviceData['base_price'], 2); ?></span>
                                            </div>
                                            <div class="ec-single-stoke">
                                                <span class="ec-single-ps-title">Duration:</span>
                                                <span class="text-muted"><?= htmlspecialchars($serviceData['duration']); ?> hrs</span>
                                            </div>
                                        </div>

                                        <!-- Book Appointment -->
                                        <div class="ec-single-qty mt-4">
                                            <div class="ec-single-cart">
                                                <a href="booking.php?>"
                                                   class="btn btn-primary">
                                                   <i class="fi-rr-calendar"></i> Book Appointment
                                                </a>
                                            </div>
                                        </div>

                                        <!-- Social Media -->
                                        <div class="ec-single-social mt-4">
                                            <ul class="mb-0">
                                                <li class="list-inline-item facebook"><a href="#"><i class="ecicon eci-facebook"></i></a></li>
                                                <li class="list-inline-item instagram"><a href="#"><i class="ecicon eci-instagram"></i></a></li>
                                                <li class="list-inline-item twitter"><a href="#"><i class="ecicon eci-twitter"></i></a></li>
                                                <li class="list-inline-item whatsapp"><a href="#"><i class="ecicon eci-whatsapp"></i></a></li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                <!-- End Description -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php include './inc/footer.php'; ?>
</body>
</html>
