<?php
include './inc/head.php';
$userId = $_SESSION['user_id'] ?? null;

if ($userId) {
    // Fetch orders for this user
    $booker = $model->getRows("users_mart", ["user_id" => $userId, "return_type" => "single"]);
}
?>

<body class="checkout_page">
    <div id="ec-overlay">
        <div class="ec-ellipsis">
            <div></div>
            <div></div>
            <div></div>
            <div></div>
        </div>
    </div>

    <?php include './inc/header.php'; ?>
    <?php include './inc/category.php'; ?>

    <section class="ec-page-content section-space-p" id="booking_page">
        <div class="container">
            <div class="row">
                <div class="sticky-header-next-sec ec-breadcrumb section-space-mb">
                    <div class="container">
                        <div class="row">
                            <div class="col-12">
                                <div class="row ec_breadcrumb_inner">
                                    <div class="col-md-6">
                                        <h2 class="ec-breadcrumb-title">Book Appointment</h2>
                                    </div>
                                    <div class="col-md-6 text-md-end">
                                        <ul class="ec-breadcrumb-list">
                                            <li class="ec-breadcrumb-item"><a href="index.php">Home</a></li>
                                            <li class="ec-breadcrumb-item active">Appointment</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-10 offset-1 col-md-12">
                    <div class="ec-checkout-content">
                        <div class="ec-checkout-inner">
                            <div class="ec-checkout-wrap">
                                <div class="ec-checkout-block ec-check-bill">
                                    <h3 class="ec-checkout-title mb-4">Book Your Appointment</h3>

                                    <form id="appointmentForm" method="post" action="../app/bookingHandler.php">
                                        <?php
                                        $services = $model->getRows("services", [
                                            "where" => ["status" => "active"],
                                            "order_by" => "name ASC"
                                        ]);
                                        ?>

                                        <!-- SERVICE SELECTION -->
                                        <div class="mb-4">
                                            <label class="mb-3"><strong>Select One or More Services*</strong></label>
                                            <?php if ($services): ?>
                                                <div class="row g-3">
                                                    <?php foreach ($services as $srv): ?>
                                                        <div class="col-md-6 col-lg-4">
                                                            <label class="service-card d-block text-center p-3 border rounded position-relative">
                                                                <div class="service-img mb-2">
                                                                    <img
                                                                        src="<?= !empty($srv['image'])
                                                                                    ? './assets/images' . htmlspecialchars($srv['image'])
                                                                                    : './assets/images/default-service.jpg'; ?>"
                                                                        alt="<?= htmlspecialchars($srv['name']); ?>"
                                                                        class="img-fluid rounded"
                                                                        style="height: 160px; object-fit: cover; width: 100%;">
                                                                </div>

                                                                <a href="../view/viewservice.php?slug=<?= $srv['slug']; ?>">
                                                                    <h6 class="mt-2 mb-1"><?= htmlspecialchars($srv['name']); ?></h6>
                                                                </a>
                                                                <small class="text-muted d-block"><i><?= htmlspecialchars($srv['description']); ?></i></small>
                                                                <small class="text-secondary d-block mb-1">Duration: <?= htmlspecialchars($srv['duration']); ?></small>
                                                                <div class="fw-bold text-success">£<?= number_format($srv['base_price'], 2); ?></div>
                                                                <hr>
                                                                <input
                                                                    type="checkbox"
                                                                    class="form-check-input position-absolute top-2 end-2 service-checkbox"
                                                                    name="service_ids[]"
                                                                    value="<?= $srv['id']; ?>"
                                                                    data-price="<?= $srv['base_price']; ?>"
                                                                    data-name="<?= htmlspecialchars($srv['name']); ?>"
                                                                    style="transform: scale(1.3);">
                                                            </label>
                                                        </div>
                                                    <?php endforeach; ?>
                                                </div>
                                            <?php else: ?>
                                                <p class="text-danger mt-3">No services available at the moment.</p>
                                            <?php endif; ?>
                                        </div>


                                        <!-- PRIVACY -->
                                        <div class="form-check mb-4">
                                            <input type="checkbox" class="form-check-input" id="privacy_consent" name="privacy_consent" required>
                                            <label class="form-check-label" for="privacy_consent">
                                                I agree to data processing under the
                                                <a href="privacy-policy.php" target="_blank"><b>Privacy Policy</b></a>.
                                            </label>
                                        </div>

                                        <!-- SUMMARY -->
                                        <div class="ec-sidebar-wrap mb-4">
                                            <div class="ec-sidebar-block p-3 border rounded">
                                                <h4 class="mb-3">Appointment Summary</h4>
                                                <ul id="summary-service-list" class="list-group mb-3"></ul>
                                                <h5 class="mt-3">Total: <strong>£<span id="total-cost">0.00</span></strong></h5>
                                            </div>
                                        </div>

                                        <!-- SUBMIT -->
                                        <button type="submit" class="btn btn-success btn-lg w-100">Confirm Appointment</button>
                                    </form>
                                </div>
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