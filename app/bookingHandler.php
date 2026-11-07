<?php
require_once __DIR__ . '/query.php';
require_once __DIR__ . '/../controller/booking_helpers.php';

function booking_redirect()
{
    header('Location: ../view/booking.php', true, 303);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $utility->setFlash('danger', 'Please choose your services on the booking page.');
    booking_redirect();
}
if (!qs_booking_valid_csrf($_POST['csrf_token'] ?? null)) {
    $utility->setFlash('danger', 'Your booking form has expired. Please review your choices and try again.');
    booking_redirect();
}

$errors = [];
$ids = [];
$services = [];
$appointment = null;
try {
    $ids = qs_booking_service_ids($_POST['service_ids'] ?? null);
    $services = $model->getRows('services', ['where' => ['id' => $ids, 'status' => 'active'], 'order_by' => 'name ASC']);
    if (count($services) !== count($ids)) $errors['services'] = 'A selected service is no longer available. Please review your choices.';
} catch (InvalidArgumentException $e) {
    $errors['services'] = $e->getMessage();
} catch (Throwable $e) {
    error_log('Booking catalog lookup failed: ' . $e->getMessage());
    $errors['services'] = 'We could not check the service list. Please try again.';
}
try {
    $appointment = qs_booking_appointment($_POST['appointment_date'] ?? null, $_POST['appointment_time'] ?? null);
} catch (InvalidArgumentException $e) {
    $errors['appointment'] = $e->getMessage();
}
if (($_POST['privacy_consent'] ?? '') !== '1') $errors['privacy'] = 'Please agree to the Privacy Policy to continue.';

$_SESSION['booking_form'] = [
    'service_ids' => $ids,
    'appointment_date' => is_string($_POST['appointment_date'] ?? null) ? substr($_POST['appointment_date'], 0, 10) : '',
    'appointment_time' => is_string($_POST['appointment_time'] ?? null) ? substr($_POST['appointment_time'], 0, 5) : ''
];
if ($errors) {
    $_SESSION['booking_errors'] = $errors;
    booking_redirect();
}

try {
    // Services and prices come exclusively from the current active catalog.
    $cart->setBookingServices($services);
    $_SESSION['booking_draft'] = $appointment;
    unset($_SESSION['booking_form'], $_SESSION['booking_errors']);
    $_SESSION['order_type'] = 'service';
    $utility->setFlash('success', 'Your services and preferred time are ready. Review your details at checkout.');
    header('Location: ../view/checkout.php', true, 303);
    exit;
} catch (Throwable $e) {
    error_log('Booking selection failed: ' . $e->getMessage());
    $utility->setFlash('danger', 'We could not save your selection. Your choices are kept below. Please try again.');
    booking_redirect();
}
