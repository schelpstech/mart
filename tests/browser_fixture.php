<?php
// Disposable account for manual/browser QA. No registration email is sent.
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
require_once __DIR__ . '/../controller/env.php';
if (!in_array(env_value('DB_HOST', 'localhost'), ['localhost', '127.0.0.1', '::1'], true)) throw new RuntimeException('Local QA only.');
$pdo = new PDO('mysql:host=' . env_value('DB_HOST', 'localhost') . ';dbname=' . env_value('DB_NAME', 'mart_a99_queenzy'), env_value('DB_USER', 'root'), env_value('DB_PASSWORD', ''), [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
$email = 'qa-whatsapp-browser@example.invalid';
$find = $pdo->prepare('SELECT user_id FROM users_mart WHERE email = ?');
$find->execute([$email]);
$id = $find->fetchColumn();
if (in_array('--cleanup', $argv, true)) {
    if ($id) {
        $stmt = $pdo->prepare('SELECT cart_id FROM cart WHERE user_id = ?');
        $stmt->execute([$id]);
        foreach ($stmt->fetchAll(PDO::FETCH_COLUMN) as $cartId) {
            foreach (['cart_items', 'service_cart_items', 'cart'] as $table) $pdo->prepare("DELETE FROM `$table` WHERE cart_id = ?")->execute([$cartId]);
        }
        $pdo->prepare('DELETE FROM users_mart WHERE user_id = ? AND email = ?')->execute([$id, $email]);
    }
    echo "Browser test account and its carts removed.\n";
    exit;
}
if ($id) throw new RuntimeException('A browser fixture already exists; clean it up first.');
$password = bin2hex(random_bytes(12)) . '!aA1';
$pdo->prepare('INSERT INTO users_mart (email,phone,password_hash,verified) VALUES (?,?,?,1)')->execute([$email, '+447700900000', password_hash($password, PASSWORD_BCRYPT)]);
echo "Email: $email\nPassword: $password\n";
