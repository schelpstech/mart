<?php
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
require_once __DIR__ . '/../controller/env.php';
require_once __DIR__ . '/../controller/booking_helpers.php';
$checks = 0;
function checkBooking($condition, $message) {
    global $checks;
    if (!$condition) throw new RuntimeException($message);
    $checks++;
}
function rejectsBooking($callback, $message) {
    try { $callback(); } catch (InvalidArgumentException $e) { checkBooking(true, $message); return; }
    throw new RuntimeException($message);
}
$_SESSION = [];
$token = qs_booking_csrf_token();
checkBooking(strlen($token) === 64 && qs_booking_valid_csrf($token), 'Session-bound form token.');
checkBooking(!qs_booking_valid_csrf(null) && !qs_booking_valid_csrf([$token]) && !qs_booking_valid_csrf('wrong'), 'Reject missing, malformed, and invalid form tokens.');
checkBooking(qs_booking_service_ids(['12', '12', '16']) === [12, 16], 'Deduplicate service selection.');
rejectsBooking(function () { qs_booking_service_ids('12'); }, 'Reject scalar service input.');
rejectsBooking(function () { qs_booking_service_ids([['12']]); }, 'Reject nested service input.');
rejectsBooking(function () { qs_booking_service_ids(['12 OR 1=1']); }, 'Reject non-numeric service input.');
$now = new DateTimeImmutable('2026-11-07 12:00', new DateTimeZone('Europe/London'));
checkBooking(qs_booking_appointment('2026-11-08', '14:30', $now)['appointment_time'] === '14:30', 'Valid future appointment.');
rejectsBooking(function () use ($now) { qs_booking_appointment('2026-02-31', '14:30', $now); }, 'Reject impossible date.');
rejectsBooking(function () use ($now) { qs_booking_appointment('2026-11-07', '11:59', $now); }, 'Reject past time on same day.');
rejectsBooking(function () use ($now) { qs_booking_appointment('2027-03-28', '01:30', $now); }, 'Reject time skipped by UK daylight saving.');
rejectsBooking(function () use ($now) { qs_booking_appointment([], '14:30', $now); }, 'Reject malformed date without a PHP error.');
checkBooking(qs_booking_duration_minutes('04:30') === 270 && qs_booking_duration_label(90) === '1 hr 30 min', 'Readable duration arithmetic.');
if (!in_array('--http', $argv, true)) { echo "$checks booking unit checks passed.\n"; exit; }
if (!in_array(env_value('DB_HOST', 'localhost'), ['localhost', '127.0.0.1', '::1'], true)) throw new RuntimeException('Local QA only.');
$pdo = new PDO('mysql:host=' . env_value('DB_HOST', 'localhost') . ';dbname=' . env_value('DB_NAME', 'mart_a99_queenzy') . ';charset=utf8mb4', env_value('DB_USER', 'root'), env_value('DB_PASSWORD', ''), [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
$cookie = tempnam(sys_get_temp_dir(), 'mart-booking-qa-');
$tag = 'QABOOK' . bin2hex(random_bytes(4));
$serviceIds = [];
$userId = null;
$sessionId = null;
$otherCartId = null;
function bookingRequest($path, $data = null) {
    global $cookie;
    $ch = curl_init('http://localhost/mart/' . $path);
    $headers = [];
    curl_setopt_array($ch, [CURLOPT_RETURNTRANSFER => true, CURLOPT_COOKIEFILE => $cookie, CURLOPT_COOKIEJAR => $cookie, CURLOPT_TIMEOUT => 20, CURLOPT_HEADERFUNCTION => function ($ch, $line) use (&$headers) { $headers[] = $line; return strlen($line); }]);
    if ($data !== null) curl_setopt_array($ch, [CURLOPT_POST => true, CURLOPT_POSTFIELDS => http_build_query($data)]);
    $body = curl_exec($ch); $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    if ($body === false) throw new RuntimeException(curl_error($ch));
    curl_close($ch);
    return [$status, $body, implode('', $headers)];
}
function bookingRows($pdo, $cartId) {
    $stmt = $pdo->prepare('SELECT service_id,quantity,price FROM service_cart_items WHERE cart_id=? ORDER BY service_id');
    $stmt->execute([$cartId]); return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
try {
    $categoryId = (int)$pdo->query('SELECT categoryTbl_id FROM categories ORDER BY categoryTbl_id LIMIT 1')->fetchColumn();
    $insert = $pdo->prepare('INSERT INTO services (services_categoryID,name,slug,duration,base_price,status) VALUES (?,?,?,?,?,?)');
    foreach ([[' ' . $tag . ' <img src=x onerror=alert(1)>', 9.75, 'active'], [$tag . ' Second', 15.25, 'active'], [$tag . ' Hidden', 20, 'inactive']] as $index => [$name, $price, $state]) {
        $insert->execute([$categoryId, $name, strtolower($tag) . '-' . $index, '01:30', $price, $state]);
        $serviceIds[] = (int)$pdo->lastInsertId();
    }
    $ordersBefore = (int)$pdo->query('SELECT COUNT(*) FROM orders_mart')->fetchColumn();
    [$status, $page] = bookingRequest('view/booking.php');
    checkBooking($status === 200 && strpos($page, 'Book your <span>appointment.') !== false, 'Booking page loads.');
    checkBooking(strpos($page, '&lt;img src=x onerror=alert(1)&gt;') !== false && strpos($page, '<img src=x onerror=alert(1)>') === false, 'Escape service names in rendered HTML.');
    checkBooking(strpos($page, $tag . ' Hidden') === false, 'Inactive services are not offered.');
    preg_match('/name="csrf_token" value="([a-f0-9]+)"/', $page, $match); $token = $match[1] ?? '';
    foreach (file($cookie, FILE_IGNORE_NEW_LINES) as $line) { $parts = explode("\t", $line); if (count($parts) >= 7 && $parts[5] === 'PHPSESSID') $sessionId = $parts[6]; }
    $stmt = $pdo->prepare('SELECT cart_id FROM cart WHERE session_id=?'); $stmt->execute([$sessionId]); $cartId = (int)$stmt->fetchColumn();
    checkBooking($cartId > 0 && strlen($token) === 64, 'Independent guest cart and form token.');
    $date = qs_booking_now()->modify('+2 days')->format('Y-m-d');
    $valid = ['csrf_token' => $token, 'service_ids' => [$serviceIds[0], $serviceIds[1]], 'appointment_date' => $date, 'appointment_time' => '14:30', 'privacy_consent' => '1', 'price' => '0.01'];
    foreach (['csrf_token' => 'invalid', 'appointment_date' => '2000-01-01', 'privacy_consent' => '0', 'service_ids' => [[$serviceIds[0]]]] as $field => $bad) {
        [$status] = bookingRequest('app/bookingHandler.php', array_replace($valid, [$field => $bad]));
        checkBooking($status === 303 && bookingRows($pdo, $cartId) === [], 'Reject invalid ' . $field . ' without changing services.');
    }
    [$status, $page] = bookingRequest('view/booking.php');
    checkBooking(strpos($page, 'Please check your booking') !== false && strpos($page, 'value="' . $date . '"') !== false, 'Errors preserve entered appointment fields.');
    // Keep a product line to prove the booking flow does not replace shopping items.
    $productId = (int)$pdo->query("SELECT product_id FROM products WHERE status='Active' ORDER BY product_id LIMIT 1")->fetchColumn();
    bookingRequest('app/ajax/cart_action.php', ['action' => 'add', 'product_id' => $productId, 'quantity' => 2]);
    [$status, , $headers] = bookingRequest('app/bookingHandler.php', $valid);
    $rows = bookingRows($pdo, $cartId);
    checkBooking($status === 303 && strpos($headers, '../view/checkout.php') !== false && count($rows) === 2, 'Valid guest booking reaches checkout.');
    checkBooking((float)$rows[0]['price'] === 9.75 && (float)$rows[1]['price'] === 15.25, 'Ignore submitted prices and use active catalog prices.');
    bookingRequest('app/bookingHandler.php', array_replace($valid, ['service_ids' => [$serviceIds[0], $serviceIds[0], $serviceIds[1]]]));
    checkBooking(bookingRows($pdo, $cartId) === $rows, 'Repeated submission and duplicate IDs do not increase quantities.');
    $pdo->prepare('UPDATE service_cart_items SET quantity=2 WHERE cart_id=? AND service_id=?')->execute([$cartId, $serviceIds[0]]);
    bookingRequest('app/bookingHandler.php', array_replace($valid, ['service_ids' => [$serviceIds[0]]]));
    $rows = bookingRows($pdo, $cartId);
    checkBooking(count($rows) === 1 && (int)$rows[0]['quantity'] === 2, 'Deselect removes only unwanted services and retains deliberate quantities.');
    $stmt = $pdo->prepare('SELECT COUNT(*) FROM cart_items WHERE cart_id=? AND product_id=? AND quantity=2'); $stmt->execute([$cartId, $productId]);
    checkBooking((int)$stmt->fetchColumn() === 1, 'Product cart is preserved.');
    bookingRequest('app/bookingHandler.php', array_replace($valid, ['service_ids' => [$serviceIds[0], $serviceIds[2]]]));
    checkBooking(bookingRows($pdo, $cartId) === $rows, 'Unavailable service rejects the entire selection without partial changes.');
    $pdo->prepare('INSERT INTO cart (session_id,created_at) VALUES (?,NOW())')->execute([$tag . '-other']); $otherCartId = (int)$pdo->lastInsertId();
    $pdo->prepare('INSERT INTO service_cart_items (cart_id,service_id,quantity,price) VALUES (?,?,7,9.75)')->execute([$otherCartId, $serviceIds[0]]);
    bookingRequest('app/bookingHandler.php', array_replace($valid, ['cart_id' => $otherCartId]));
    checkBooking((int)bookingRows($pdo, $otherCartId)[0]['quantity'] === 7, 'Submitted cart ID cannot modify another cart.');
    $email = strtolower($tag) . '@example.invalid'; $password = bin2hex(random_bytes(14));
    $pdo->prepare('INSERT INTO users_mart (email,phone,password_hash,verified) VALUES (?,?,?,1)')->execute([$email, '+447700900000', password_hash($password, PASSWORD_BCRYPT)]); $userId = (int)$pdo->lastInsertId();
    bookingRequest('app/user_access_action.php', ['action' => bin2hex(base64_encode('login')), 'email' => $email, 'password' => $password]);
    [$status, $checkout] = bookingRequest('view/checkout.php');
    checkBooking($status === 200 && strpos($checkout, 'id="appointment_date" min="') !== false && strpos($checkout, 'value="' . $date . '"') !== false && strpos($checkout, 'value="14:30"') !== false, 'Appointment draft survives login and prefills checkout.');
    preg_match('/name="csrf_token" value="([a-f0-9]+)"/', $checkout, $checkoutToken);
    $order = ['action' => bin2hex(base64_encode('place_order')), 'firstname' => 'QA', 'lastname' => 'Booking', 'email' => $email, 'phone' => '+447700900000', 'privacy_consent' => '1', 'fulfilment_type' => 'pickup', 'appointment_date' => $date, 'appointment_time' => '14:30'];
    bookingRequest('app/orderHandler.php', $order);
    checkBooking((int)$pdo->query('SELECT COUNT(*) FROM orders_mart')->fetchColumn() === $ordersBefore, 'Checkout rejects a missing form token before order creation.');
    bookingRequest('app/orderHandler.php', array_replace($order, ['csrf_token' => $checkoutToken[1] ?? '', 'appointment_date' => '2000-01-01']));
    checkBooking((int)$pdo->query('SELECT COUNT(*) FROM orders_mart')->fetchColumn() === $ordersBefore, 'Checkout rejects past appointment before order/payment creation.');
    echo "$checks booking checks passed: validation, CSRF, prices, replay safety, cart ownership, preserved products, and draft retention.\n";
} finally {
    $stmt = $pdo->prepare('SELECT cart_id FROM cart WHERE session_id=? OR session_id=? OR user_id=?'); $stmt->execute([$sessionId, $tag . '-other', $userId]);
    foreach ($stmt->fetchAll(PDO::FETCH_COLUMN) as $cartId) foreach (['cart_items', 'service_cart_items', 'cart'] as $table) $pdo->prepare("DELETE FROM `$table` WHERE cart_id=?")->execute([$cartId]);
    foreach ($serviceIds as $id) $pdo->prepare('DELETE FROM services WHERE id=?')->execute([$id]);
    if ($userId) $pdo->prepare('DELETE FROM users_mart WHERE user_id=?')->execute([$userId]);
    if (is_file($cookie)) unlink($cookie);
}
