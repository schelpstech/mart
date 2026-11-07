<?php
// Repair interrupted local SQL imports without changing existing record IDs.
if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}
require_once __DIR__ . '/../controller/env.php';
$host = env_value('DB_HOST', 'localhost');
if (!in_array($host, ['localhost', '127.0.0.1', '::1'], true)) {
    throw new RuntimeException('This repair is restricted to a local database.');
}
$pdo = new PDO('mysql:host=' . $host . ';dbname=' . env_value('DB_NAME', 'mart_a99_queenzy') . ';charset=utf8mb4', env_value('DB_USER', 'root'), env_value('DB_PASSWORD', ''), [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
// Preserve legacy zero dates during table rebuilds; this affects this CLI connection only.
$mode = explode(',', (string)$pdo->query('SELECT @@SESSION.sql_mode')->fetchColumn());
$pdo->exec('SET SESSION sql_mode = ' . $pdo->quote(implode(',', array_diff($mode, ['NO_ZERO_DATE', 'NO_ZERO_IN_DATE']))));
$identities = ['admins' => 'admin_id', 'cart' => 'cart_id', 'cart_items' => 'cart_item_id', 'categories' => 'categoryTbl_id', 'coupon_usage' => 'usage_id', 'coupons' => 'coupon_id', 'delivery_settings' => 'id', 'delivery_zones' => 'zone_id', 'order_items_mart' => 'id', 'orders_mart' => 'order_tbl_id', 'password_resets' => 'id'];
$plan = [];
foreach ($identities as $table => $column) {
    $stats = $pdo->query("SELECT COUNT(*) AS n, COUNT(DISTINCT `$column`) AS ids, COALESCE(MIN(`$column`), 1) AS smallest FROM `$table`")->fetch(PDO::FETCH_ASSOC);
    if ((int)$stats['n'] !== (int)$stats['ids'] || (int)$stats['smallest'] < 1) {
        throw new RuntimeException("Refusing to repair $table: IDs must be unique, positive, and non-null.");
    }
    $columns = $pdo->query("SHOW COLUMNS FROM `$table`")->fetchAll(PDO::FETCH_ASSOC);
    $identity = null;
    $primary = [];
    foreach ($columns as $definition) {
        if ($definition['Field'] === $column) $identity = $definition;
        if ($definition['Key'] === 'PRI') $primary[] = $definition['Field'];
    }
    if ($primary && $primary !== [$column]) throw new RuntimeException("Unexpected primary key on $table.");
    if (!$identity || !preg_match('/^int(?:\(\d+\))?(?: unsigned)?$/i', $identity['Type'])) throw new RuntimeException("Unexpected ID type on $table.");
    $parts = [];
    if (!$primary) $parts[] = "ADD PRIMARY KEY (`$column`)";
    if (stripos($identity['Extra'], 'auto_increment') === false) $parts[] = "MODIFY `$column` {$identity['Type']} NOT NULL AUTO_INCREMENT";
    if ($parts) $plan[] = ['sql' => "ALTER TABLE `$table` " . implode(', ', $parts), 'table' => $table, 'column' => $column, 'count' => (int)$stats['n']];
}
// All tables are checked before any DDL. MySQL DDL is not transactionally reversible.
foreach ($plan as $step) {
    echo $step['sql'] . ";\n";
    if (in_array('--apply', $argv, true)) {
        $pdo->exec($step['sql']);
        $count = (int)$pdo->query("SELECT COUNT(*) FROM `{$step['table']}`")->fetchColumn();
        if ($count !== $step['count']) throw new RuntimeException('Unexpected record-count change.');
    }
}
echo count($plan) . (in_array('--apply', $argv, true) ? " table identities repaired.\n" : " tables need repair. Back up the database, then run with --apply.\n");
