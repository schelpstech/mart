<?php
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
require_once __DIR__ . '/../controller/env.php';
require_once __DIR__ . '/../controller/store_helpers.php';
require_once __DIR__ . '/../controller/whatsapp_helpers.php';
$checks = 0;
function expect($condition, $message) {
    global $checks;
    if (!$condition) throw new RuntimeException($message);
    $checks++;
}
expect(qs_product_prices(['price' => 6, 'discount_price' => 4]) === ['current' => 4.0, 'original' => 6.0], 'Discount price must match display and charge.');
expect(qs_product_prices(['price' => 6, 'discount_price' => 8])['current'] === 6.0, 'Invalid higher sale price must not increase the charge.');
$sample = ['items' => [['name' => "Rice &amp; Beans\n<script>x</script>", 'quantity' => 3, 'price' => 2.5, 'item_type' => 'product']]];
$message = qs_whatsapp_cart_message($sample);
expect(strpos($message, 'Quantity: 3') !== false && strpos($message, 'Line total: £7.50') !== false, 'Line arithmetic.');
expect(strpos($message, 'Items total: £7.50') !== false && strpos($message, 'to be confirmed') !== false, 'Unselected delivery must not invent a final delivery-inclusive price.');
expect(strpos($message, '<script>') === false && strpos($message, 'Rice & Beans') !== false, 'Normalize catalog text.');
try { qs_whatsapp_cart_message(['items' => []]); throw new RuntimeException('Empty cart was accepted.'); } catch (InvalidArgumentException $expected) { $checks++; }
expect(qs_whatsapp_number() === '447767918847', 'Approved destination number.');

if (!in_array('--http', $argv, true)) { echo "$checks unit checks passed. Use --http for local endpoint checks.\n"; exit; }
if (!in_array(env_value('DB_HOST', 'localhost'), ['localhost', '127.0.0.1', '::1'], true)) throw new RuntimeException('HTTP QA is local only.');
$pdo = new PDO('mysql:host=' . env_value('DB_HOST', 'localhost') . ';dbname=' . env_value('DB_NAME', 'mart_a99_queenzy') . ';charset=utf8mb4', env_value('DB_USER', 'root'), env_value('DB_PASSWORD', ''), [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
$cookie = tempnam(sys_get_temp_dir(), 'mart-cart-qa-');
$tag = 'QA' . bin2hex(random_bytes(5));
$created = ['product' => null, 'coupon' => null, 'zone' => null, 'user' => null];
$sessionId = null;
function request($path, $data = null, $json = true) {
    global $cookie;
    $ch = curl_init('http://localhost/mart/' . $path);
    curl_setopt_array($ch, [CURLOPT_RETURNTRANSFER => true, CURLOPT_COOKIEFILE => $cookie, CURLOPT_COOKIEJAR => $cookie, CURLOPT_TIMEOUT => 20]);
    if ($data !== null) curl_setopt_array($ch, [CURLOPT_POST => true, CURLOPT_POSTFIELDS => http_build_query($data)]);
    $body = curl_exec($ch);
    $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    if ($body === false) throw new RuntimeException(curl_error($ch));
    curl_close($ch);
    if (!$json) return [$status, $body];
    $decoded = json_decode($body, true);
    if (!is_array($decoded)) throw new RuntimeException("Invalid JSON from $path (HTTP $status): " . substr(strip_tags($body), 0, 200));
    return [$status, $decoded];
}
function insertFixture($pdo, $table, $fields) {
    $stmt = $pdo->prepare('INSERT INTO ' . $table . ' (`' . implode('`,`', array_keys($fields)) . '`) VALUES (' . implode(',', array_fill(0, count($fields), '?')) . ')');
    $stmt->execute(array_values($fields));
    return (int)$pdo->lastInsertId();
}
try {
    $categoryId = (int)$pdo->query('SELECT categoryTbl_id FROM categories ORDER BY categoryTbl_id LIMIT 1')->fetchColumn();
    $created['product'] = insertFixture($pdo, 'products', ['category_id' => $categoryId, 'product_name' => $tag . ' Rice & Beans', 'product_slug' => strtolower($tag), 'sku' => $tag, 'price' => 6, 'discount_price' => 4, 'stock_quantity' => 20, 'status' => 'Active']);
    $created['coupon'] = insertFixture($pdo, 'coupons', ['code' => $tag, 'discount_type' => 'fixed', 'discount_value' => 1, 'minimum_order_amount' => 0, 'status' => 'active']);
    $created['zone'] = insertFixture($pdo, 'delivery_zones', ['zone_name' => $tag, 'delivery_fee' => 2.5, 'status' => 'active']);
    $ordersBefore = (int)$pdo->query('SELECT COUNT(*) FROM orders_mart')->fetchColumn();
    $usageBefore = (int)$pdo->query('SELECT COUNT(*) FROM coupon_usage')->fetchColumn();
    [$status, $empty] = request('app/ajax/cart_share.php', ['context' => 'cart']);
    expect($status === 422 && $empty['status'] === 'error', 'Empty cart must return a useful error.');
    // Read only this test's cookie, never the user's browser session.
    foreach (file($cookie, FILE_IGNORE_NEW_LINES) as $line) {
        $parts = explode("\t", $line);
        if (count($parts) >= 7 && $parts[5] === 'PHPSESSID') $sessionId = $parts[6];
    }
    expect(!empty($sessionId), 'Test session cookie exists.');
    [$status, $added] = request('app/ajax/cart_action.php', ['action' => 'add', 'product_id' => $created['product'], 'quantity' => 2]);
    expect($status === 200 && $added['status'] === 'success' && $added['count'] === 2, 'Guest cart add.');
    $lineId = $added['item_id'];
    [$status, $shared] = request('app/ajax/cart_share.php', ['context' => 'cart']);
    expect($status === 200 && strpos($shared['message'], 'Unit price: £4.00') !== false && strpos($shared['message'], 'Items total: £8.00') !== false, 'Sharing uses the actual discounted cart price.');
    expect(strpos($shared['url'], 'https://wa.me/447767918847?text=') === 0, 'Destination and encoded message.');
    expect(rawurldecode(substr($shared['url'], strpos($shared['url'], '?text=') + 6)) === $shared['message'], 'Encoded message round trip.');
    [$status, $changed] = request('app/ajax/cart_action.php', ['action' => 'update_quantity', 'cart_item_id' => $lineId, 'quantity' => 3]);
    expect($changed['status'] === 'success', 'Quantity update.');
    [$status, $shared] = request('app/ajax/cart_share.php', ['context' => 'checkout', 'fulfilment_type' => 'pickup', 'coupon_code' => $tag]);
    expect(strpos($shared['message'], 'Quantity: 3') !== false && strpos($shared['message'], 'Total: £11.00') !== false, 'Checkout share uses fresh quantity and coupon totals.');
    [$status, $delivery] = request('app/ajax/cart_share.php', ['context' => 'checkout', 'fulfilment_type' => 'delivery', 'delivery_zone_id' => $created['zone'], 'coupon_code' => $tag]);
    $settings = $pdo->query('SELECT * FROM delivery_settings WHERE id=1')->fetch(PDO::FETCH_ASSOC);
    $expectedFee = !empty($settings['free_delivery_minimum']) && 12 >= (float)$settings['free_delivery_minimum'] ? 0 : 2.5;
    expect($status === 200 && strpos($delivery['message'], 'Total: ' . qs_money(11 + $expectedFee)) !== false, 'Zone, delivery fee, and coupon are included.');
    [$status, $invalidCoupon] = request('app/ajax/cart_share.php', ['context' => 'checkout', 'fulfilment_type' => 'pickup', 'coupon_code' => 'INVALID_' . $tag]);
    expect($status === 422, 'Invalid coupon cannot generate misleading totals.');
    [$status, $invalidZone] = request('app/ajax/cart_share.php', ['context' => 'checkout', 'fulfilment_type' => 'delivery', 'delivery_zone_id' => 2147483647]);
    expect($status === 422, 'Unavailable delivery zone is rejected.');
    [$status, $sidebar] = request('app/ajax/get_cart_items.php');
    expect($status === 200 && $sidebar['count'] === 3 && strpos($sidebar['html'], 'viewproduct.php?id=' . $created['product']) !== false, 'Cart is preserved and product links use IDs.');
    [$status, $page] = request('view/checkout.php', null, false);
    expect($status === 200 && strpos($page, 'offset-lg-2') !== false && strpos($page, 'Send cart on WhatsApp') !== false, 'Guest checkout offers sharing and responsive layout.');
    // Authenticate a disposable local test account, without sending verification mail.
    $password = bin2hex(random_bytes(16));
    $email = strtolower($tag) . '@example.invalid';
    $created['user'] = insertFixture($pdo, 'users_mart', ['email' => $email, 'phone' => '+447700900000', 'password_hash' => password_hash($password, PASSWORD_BCRYPT), 'verified' => 1]);
    [$status] = request('app/user_access_action.php', ['action' => bin2hex(base64_encode('login')), 'email' => $email, 'password' => $password], false);
    expect($status === 302, 'Test account login.');
    [$status, $page] = request('view/checkout.php', null, false);
    expect($status === 200 && strpos($page, 'data-share-context="checkout"') !== false && strpos($page, 'id="checkoutForm"') !== false, 'Authenticated checkout includes final-total sharing.');
    expect(strpos($page, 'Profile</a>') !== false && substr_count($page, 'id="ec-side-cart"') === 1, 'Shared account links and exactly one cart drawer.');
    [$status, $page] = request('view/booking.php', null, false);
    expect($status === 200 && substr_count($page, 'id="ec-side-cart"') === 1, 'Booking includes its missing cart drawer.');
    // Exercise mixed and service-only cart messages through the real booking endpoint.
    $serviceId = (int)$pdo->query("SELECT id FROM services WHERE status='active' ORDER BY id LIMIT 1")->fetchColumn();
    expect($serviceId > 0, 'Active service fixture is available.');
    [$status, $bookingPage] = request('view/booking.php', null, false);
    preg_match('/name="csrf_token" value="([a-f0-9]+)"/', $bookingPage, $bookingToken);
    $bookingDate = (new DateTimeImmutable('tomorrow', new DateTimeZone('Europe/London')))->format('Y-m-d');
    [$status] = request('app/bookingHandler.php', ['service_ids' => [$serviceId], 'csrf_token' => $bookingToken[1] ?? '', 'appointment_date' => $bookingDate, 'appointment_time' => '14:30', 'privacy_consent' => '1'], false);
    expect($status === 303, 'Booking adds a service.');
    [$status, $mixed] = request('app/ajax/cart_share.php', ['context' => 'checkout', 'fulfilment_type' => 'pickup', 'appointment_date' => '2030-01-15', 'appointment_time' => '14:30']);
    expect($status === 200 && strpos($mixed['message'], '(Service)') !== false && strpos($mixed['message'], '2030-01-15 at 14:30') !== false, 'Mixed cart includes service and requested appointment.');
    request('app/ajax/cart_action.php', ['action' => 'remove', 'cart_item_id' => $lineId, 'item_type' => 'product']);
    [$status, $serviceOnly] = request('app/ajax/cart_share.php', ['context' => 'checkout']);
    expect($status === 200 && strpos($serviceOnly['message'], 'Delivery fee:') === false && strpos($serviceOnly['message'], 'Appointment date / time: to be confirmed.') !== false, 'Service-only sharing has no shipping and identifies missing appointment details.');
    [$status, $invalidDate] = request('app/ajax/cart_share.php', ['context' => 'checkout', 'appointment_date' => '2030-02-31', 'appointment_time' => '14:30']);
    expect($status === 422, 'Impossible appointment dates are rejected.');
    $longName = str_repeat('Long product name ', 14);
    $pdo->prepare('UPDATE products SET product_name=? WHERE product_id=?')->execute([$longName, $created['product']]);
    request('app/ajax/cart_action.php', ['action' => 'add', 'product_id' => $created['product'], 'quantity' => 1]);
    // Large service names are limited by schema; many repeated lines are tested at the pure formatting boundary.
    $large = qs_whatsapp_cart_message(['items' => array_fill(0, 60, ['name' => $longName, 'quantity' => 1, 'price' => 4])]);
    expect(strlen('https://wa.me/' . qs_whatsapp_number() . '?text=' . rawurlencode($large)) > 7000 && substr_count($large, 'Unit price:') === 60, 'Long lists remain complete for the copy fallback.');
    expect((int)$pdo->query('SELECT COUNT(*) FROM orders_mart')->fetchColumn() === $ordersBefore, 'Sharing creates no orders.');
    expect((int)$pdo->query('SELECT COUNT(*) FROM coupon_usage')->fetchColumn() === $usageBefore, 'Sharing does not consume coupons.');
    echo "$checks checks passed: guest/authenticated carts, prices, quantities, links, delivery, pickup, coupons, services, message encoding, and unchanged orders.\n";
} finally {
    $stmt = $pdo->prepare('SELECT cart_id FROM cart WHERE session_id = ? OR user_id = ?');
    $stmt->execute([$sessionId, $created['user']]);
    foreach ($stmt->fetchAll(PDO::FETCH_COLUMN) as $cartId) {
        foreach (['cart_items', 'service_cart_items', 'cart'] as $table) $pdo->prepare("DELETE FROM `$table` WHERE cart_id = ?")->execute([$cartId]);
    }
    foreach (['product' => ['products', 'product_id'], 'coupon' => ['coupons', 'coupon_id'], 'zone' => ['delivery_zones', 'zone_id'], 'user' => ['users_mart', 'user_id']] as $key => [$table, $column]) {
        if ($created[$key]) $pdo->prepare("DELETE FROM `$table` WHERE `$column` = ?")->execute([$created[$key]]);
    }
    if (is_file($cookie)) unlink($cookie);
}
