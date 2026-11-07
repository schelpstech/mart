<?php
$bookingStyles = true;
include './inc/head.php';
require_once __DIR__ . '/../controller/booking_helpers.php';
$escape = static function ($value) { return htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); };
$services = $model->getRows('services', ['where' => ['status' => 'active'], 'order_by' => 'name ASC']);
$cartSummary = $cart->getCartSummary();
$cartQuantities = [];
foreach ($cartSummary['service_items'] as $line) $cartQuantities[(int)$line['service_id']] = max(1, (int)$line['quantity']);
$form = $_SESSION['booking_form'] ?? [];
$draft = $_SESSION['booking_draft'] ?? [];
$errors = $_SESSION['booking_errors'] ?? [];
unset($_SESSION['booking_errors']);
$selectedIds = $form['service_ids'] ?? array_keys($cartQuantities);
$activeIds = array_map(static function ($service) { return (int)$service['id']; }, $services);
$selectedIds = array_values(array_intersect($selectedIds, $activeIds));
$requestedService = filter_var($_GET['service'] ?? '', FILTER_VALIDATE_INT);
if ($requestedService && in_array($requestedService, $activeIds, true)) $selectedIds = array_unique(array_merge($selectedIds, [$requestedService]));
$date = $form['appointment_date'] ?? ($draft['appointment_date'] ?? '');
$time = $form['appointment_time'] ?? ($draft['appointment_time'] ?? '');
$total = 0;
$duration = 0;
foreach ($services as $service) {
    if (in_array((int)$service['id'], $selectedIds, true)) {
        $quantity = $cartQuantities[(int)$service['id']] ?? 1;
        $total += (float)$service['base_price'] * $quantity;
        $duration += qs_booking_duration_minutes($service['duration']) * $quantity;
    }
}
$today = qs_booking_now()->format('Y-m-d');
?>
<body class="booking-page">
    <div id="ec-overlay"><div class="ec-ellipsis"><div></div><div></div><div></div><div></div></div></div>
    <?php include './inc/header.php'; include './inc/category.php'; ?>

    <main id="booking_page" class="booking-shell">
        <div class="container">
            <nav aria-label="Breadcrumb" class="booking-breadcrumb"><a href="index.php">Home</a><span aria-hidden="true">/</span><span>Salon appointments</span></nav>
            <header class="booking-intro">
                <div>
                    <p class="booking-eyebrow">QUEENZY SALON</p>
                    <h1>Book your <span>appointment.</span></h1>
                    <p class="booking-intro-copy">Choose your services and preferred time. Review everything before you pay.</p>
                </div>
                <div class="booking-intro-note"><span class="booking-note-mark" aria-hidden="true">✦</span><div><strong>A little time for you</strong><span>Clear prices. A choice of services.<br>A personal touch from our salon.</span></div></div>
            </header>

            <ol class="booking-progress" aria-label="Booking steps">
                <li class="is-current" id="booking-step-services"><span>1</span><div>Choose services<small>Pick your favourites</small></div></li>
                <li id="booking-step-time"><span>2</span><div>Preferred time<small>Tell us what suits you</small></div></li>
                <li><span>3</span><div>Checkout<small>Review details &amp; pay</small></div></li>
            </ol>

            <?php $utility->displayFlash(); ?>
            <?php if ($errors): ?>
                <div class="booking-error-summary" role="alert" tabindex="-1">
                    <strong>Please check your booking</strong>
                    <ul><?php foreach ($errors as $field => $error): ?><li><a href="<?= $field === 'services' ? '#services-title' : ($field === 'privacy' ? '#privacy_consent' : '#appointment_date'); ?>"><?= $escape($error); ?></a></li><?php endforeach; ?></ul>
                </div>
            <?php endif; ?>

            <form id="appointmentForm" method="post" action="../app/bookingHandler.php">
                <input type="hidden" name="csrf_token" value="<?= $escape(qs_booking_csrf_token()); ?>">
                <div class="booking-layout">
                    <div class="booking-main">
                        <section class="booking-panel" aria-labelledby="services-title">
                            <div class="booking-section-heading"><span class="booking-section-number">01</span><div><h2 id="services-title" tabindex="-1">Choose your services</h2><p>You can select more than one. Prices are per service.</p></div></div>
                            <?php if ($services): ?>
                                <div class="booking-tools">
                                    <div class="booking-search"><label for="service-search" class="visually-hidden">Search services</label><svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="10.5" cy="10.5" r="6.5"></circle><path d="m16 16 4.5 4.5"></path></svg><input type="search" id="service-search" placeholder="Try braids, nails or brows…" autocomplete="off"></div>
                                    <div class="booking-filters" role="group" aria-label="Show services"><button type="button" class="is-active" data-booking-filter="all" aria-pressed="true">All services <span><?= count($services); ?></span></button><button type="button" data-booking-filter="selected" aria-pressed="false">Selected <span id="booking-filter-count"><?= count($selectedIds); ?></span></button></div>
                                </div>
                                <p id="booking-services-error" class="booking-field-error" <?= empty($errors['services']) ? 'hidden' : ''; ?>><?= $escape($errors['services'] ?? 'Choose at least one service to continue.'); ?></p>
                                <div class="booking-service-grid">
                                    <?php foreach ($services as $index => $service):
                                        $id = (int)$service['id'];
                                        $selected = in_array($id, $selectedIds, true);
                                        $quantity = $cartQuantities[$id] ?? 1;
                                        $minutes = qs_booking_duration_minutes($service['duration']);
                                        $initials = mb_strtoupper(mb_substr($service['name'], 0, 1));
                                    ?>
                                        <article class="booking-service <?= $selected ? 'is-selected' : ''; ?>" data-service-card>
                                            <label class="booking-service-choice" for="service-<?= $id; ?>">
                                                <input type="checkbox" id="service-<?= $id; ?>" class="service-checkbox" name="service_ids[]" value="<?= $id; ?>" <?= $selected ? 'checked' : ''; ?>
                                                    data-name="<?= $escape($service['name']); ?>" data-price="<?= $escape($service['base_price']); ?>" data-quantity="<?= $quantity; ?>" data-minutes="<?= $minutes; ?>" aria-describedby="booking-services-error">
                                                <span class="booking-service-symbol tone-<?= $index % 3; ?>" aria-hidden="true"><?= $escape($initials); ?><small>✦</small></span>
                                                <span class="booking-service-copy"><strong><?= $escape($service['name']); ?></strong><span class="booking-service-duration"><svg viewBox="0 0 20 20" aria-hidden="true"><circle cx="10" cy="10" r="7"></circle><path d="M10 5.5V10l3 2"></path></svg><?= $escape(qs_booking_duration_label($minutes)); ?></span><span class="booking-service-price"><?= qs_money($service['base_price']); ?><?php if ($quantity > 1): ?><small> × <?= $quantity; ?> in your cart</small><?php endif; ?></span></span>
                                                <span class="booking-choice-mark" aria-hidden="true"></span>
                                                <span class="booking-choice-label"><?= $selected ? 'Selected' : 'Select service'; ?></span>
                                            </label>
                                            <?php if (trim((string)$service['description']) !== ''): ?><details class="booking-service-about"><summary>About this service</summary><p><?= nl2br($escape($service['description'])); ?></p></details><?php endif; ?>
                                        </article>
                                    <?php endforeach; ?>
                                </div>
                                <div class="booking-no-results" hidden><strong>No matching services</strong><p>Try a different name or show all services.</p><button type="button" id="booking-clear-search">Clear filters</button></div>
                                <p class="booking-results" id="booking-results" role="status" aria-live="polite"><?= count($services); ?> services to choose from</p>
                            <?php else: ?>
                                <div class="booking-empty"><span aria-hidden="true">✦</span><h3>Our service list is being updated</h3><p>Please contact the salon for help with an appointment.</p><a href="tel:+447767918847">Call +44 7767918847</a></div>
                            <?php endif; ?>
                        </section>

                        <section class="booking-panel booking-time-panel" aria-labelledby="time-title">
                            <div class="booking-section-heading"><span class="booking-section-number">02</span><div><h2 id="time-title">When suits you?</h2><p>Choose a preferred appointment. All times are UK time.</p></div></div>
                            <div class="booking-time-fields">
                                <div><label for="appointment_date">Preferred date <span aria-hidden="true">*</span></label><input type="date" name="appointment_date" id="appointment_date" value="<?= $escape($date); ?>" min="<?= $today; ?>" required aria-describedby="booking-time-hint booking-appointment-error"></div>
                                <div><label for="appointment_time">Preferred time <span aria-hidden="true">*</span></label><input type="time" name="appointment_time" id="appointment_time" value="<?= $escape($time); ?>" required aria-describedby="booking-time-hint booking-appointment-error"></div>
                            </div>
                            <p class="booking-field-error" id="booking-appointment-error" <?= empty($errors['appointment']) ? 'hidden' : ''; ?>><?= $escape($errors['appointment'] ?? ''); ?></p>
                            <p class="booking-time-hint" id="booking-time-hint"><svg viewBox="0 0 20 20" aria-hidden="true"><circle cx="10" cy="10" r="7"></circle><path d="M10 9v5M10 5.5v1"></path></svg>Your preferred time is a request. The salon will confirm availability.</p>
                            <label class="booking-privacy" for="privacy_consent"><input type="checkbox" id="privacy_consent" name="privacy_consent" value="1" required><span>I agree to the <a href="privacy-policy.php" target="_blank" rel="noopener">Privacy Policy</a>.</span></label>
                            <?php if (!empty($errors['privacy'])): ?><p class="booking-field-error"><?= $escape($errors['privacy']); ?></p><?php endif; ?>
                        </section>
                    </div>

                    <aside class="booking-summary" aria-labelledby="booking-summary-title">
                        <p class="booking-eyebrow">YOUR VISIT</p><h2 id="booking-summary-title">A plan just for you</h2>
                        <div id="booking-summary-empty" <?= $selectedIds ? 'hidden' : ''; ?>><div class="booking-summary-symbol" aria-hidden="true">✦</div><strong>Start with a service you love</strong><p>Your choices and total will appear here as you select.</p></div>
                        <ul id="summary-service-list" class="booking-summary-list">
                            <?php foreach ($services as $service): if (!in_array((int)$service['id'], $selectedIds, true)) continue; $quantity = $cartQuantities[(int)$service['id']] ?? 1; ?>
                                <li><div><strong><?= $escape($service['name']); ?></strong><small><?= $escape(qs_booking_duration_label(qs_booking_duration_minutes($service['duration']))); ?><?= $quantity > 1 ? ' · quantity ' . $quantity : ''; ?></small></div><span><?= qs_money((float)$service['base_price'] * $quantity); ?></span></li>
                            <?php endforeach; ?>
                        </ul>
                        <div class="booking-summary-meta"><span>Approx. duration</span><strong id="booking-duration"><?= $duration ? $escape(qs_booking_duration_label($duration)) : '—'; ?></strong></div>
                        <div class="booking-summary-meta"><span>Preferred visit</span><strong id="booking-visit">Choose a date &amp; time</strong></div>
                        <div class="booking-total" role="status" aria-live="polite" aria-atomic="true"><span>Services total</span><strong id="total-cost"><?= qs_money($total); ?></strong></div>
                        <?php if ($cartSummary['product_items']): ?><p class="booking-cart-note">Your shopping items stay in your cart. You’ll review the full total at checkout.</p><?php endif; ?>
                        <button class="booking-continue" type="submit" <?= !$services ? 'disabled' : ''; ?>>Continue to checkout <span aria-hidden="true">→</span></button>
                        <p class="booking-summary-footnote"><?= empty($_SESSION['user_id']) ? 'Choose your services now and sign in at checkout. Your choices and preferred time will be kept.' : 'Review your details and payment at the next step. You can still change your selection.'; ?></p>
                    </aside>
                </div>
                <div class="booking-mobile-action"><div><span id="booking-mobile-count"><?= count($selectedIds); ?> selected</span><strong id="booking-mobile-total"><?= qs_money($total); ?></strong></div><button type="submit" class="booking-continue" <?= !$services ? 'disabled' : ''; ?>>Continue <span aria-hidden="true">→</span></button></div>
                <noscript><p class="booking-noscript">Select your services, date, time, and privacy agreement, then continue. Your selection and current prices will be checked on the next step.</p></noscript>
            </form>
        </div>
    </main>
    <?php include './inc/footer.php'; ?>
