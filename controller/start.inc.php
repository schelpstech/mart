<?php

// begin or resume session
session_start();

// Include necessary file
include_once 'user.class.php';
include_once 'model.class.php';
include_once 'utility.class.php';
include_once 'qrcode.class.php';
include_once 'paystack.class.php';
include_once 'product.class.php';
include_once 'cart.class.php';
include_once 'stripe.class.php';

// database access parameters
$db_host = 'localhost';
$db_user = 'mart@dmin';
$db_pass = 'P@$$word';
$db_name = 'mart_a99_queenzy';

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
$str = "sk_test_51S12wOJbQ1CMImofOotcElF2LPgtFP5Xq2z1ipAiYhqTR1LJEPOrKf5xVO5wtRT4DuP0CD6gD5EOyt3vxRRazsal00gWnuumBa";
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
    $stripe = new StripePayment($str);
} else {
    // Handle the case when the connection fails (e.g., show an error message or stop further processing)
    echo "Database connection failed.";
}
