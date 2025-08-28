<?php
require_once "./query.php"; // DB connect
$endpoint_secret = "whsec_your_webhook_secret"; // from Stripe dashboard

$payload = @file_get_contents("php://input");
$sig_header = $_SERVER["HTTP_STRIPE_SIGNATURE"] ?? '';
$event = null;

// Verify signature
if (!hash_hmac('sha256', $payload, $endpoint_secret)) {
    http_response_code(400);
    exit("Invalid signature");
}

$event = json_decode($payload, true);

if ($event["type"] === "checkout.session.completed") {
    $session = $event["data"]["object"];
    $orderId = $_GET["order_id"] ?? null; // or store orderId in metadata when creating session

    if ($orderId) {
        $model->update("orders", ["payment_status" => "paid"], "order_tbl_id = ?", [$orderId]);
    }
}

http_response_code(200);
