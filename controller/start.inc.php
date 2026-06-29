<?php

// begin or resume session
session_start();


include_once __DIR__ . '/env.php';

// Define global application name constant
if (!defined("APP_NAME")) {
    define("APP_NAME", env_value("APP_NAME", "Queenzy Mart"));
}


// Include necessary file
include_once 'user.class.php';
include_once 'model.class.php';
include_once 'utility.class.php';
include_once 'qrcode.class.php';
include_once 'paystack.class.php';
include_once 'product.class.php';
include_once 'cart.class.php';
include_once 'stripe.class.php';
include_once 'store_helpers.php';

// database access parameters
$db_host = env_value('DB_HOST', 'localhost');
$db_user = env_value('DB_USER', 'root');
$db_pass = env_value('DB_PASSWORD', '');
$db_name = env_value('DB_NAME', 'mart_a99_queenzy');

// Initialize $db_conn to avoid undefined variable warning if connection fails
$db_conn = null;

// connect to database
try {
    $db_conn = new PDO("mysql:host={$db_host};dbname={$db_name}", $db_user, $db_pass);
    $db_conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // Handle the error
    $errors = [];
    array_push($errors, $e->getMessage());
    // Optionally, you can log the error or display it to the user
    echo "Error: " . $e->getMessage();
}
// Only proceed if connection was successful
if ($db_conn !== null) {
    // make use of database with users
    $model = new Model($db_conn);
    $utility = new Utility();
    $generator = new QRCodeGenerator();
    $paystack = new PaystackPayment();
    $product = new Product($db_conn);
    $cart = new Cart($model);
    $user = new User($model);
    $stripe = new StripePayment(env_value('STRIPE_SECRET_KEY', ''));
} else {
    // Handle the case when the connection fails (e.g., show an error message or stop further processing)
    echo "Database connection failed.";
}
