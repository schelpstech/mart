<?php
include '../app/query.php'; // your init file (db + session + model)

use Dompdf\Dompdf;

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$userId = $_SESSION['user_id'];
$orderRef = $_GET['ref'] ?? null;

if (!$orderRef) {
    header("Location: order.php");
    exit;
}

// Fetch order
$orders = $model->getRows("orders_mart", [
    "where" => [
        "order_reference" => $orderRef,
        "user_id" => $userId
    ],
    "return_type" => "single"
]);

if (empty($orders)) {
    die("Invalid order reference.");
}

// Fetch order items

if (!empty($orders)) {
    $orderedItems = $model->getRows('order_items_mart', [
        'where' => [
            'order_reference' => $orderRef,
            'user_id' => $userId
        ],
        'left_join' => [
            'orders_mart' => ' on orders_mart.order_tbl_id = order_items_mart.order_item_id'
        ]
    ]);
} else {
    header("Location: login.php");
}

$currency = "£";
$subtotal = 0;

// Generate HTML
ob_start();
?>
<html>

<head>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
        }

        h2 {
            color: #333;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th,
        td {
            border: 1px solid #ccc;
            padding: 8px;
            text-align: left;
        }

        th {
            background: #f2f2f2;
        }

        .total-row td {
            font-weight: bold;
        }
    </style>
</head>

<body>
    <div class="page-header text-blue-d2" style="text-align: center; margin-bottom: 20px;">
        <img src="assets/images/logo/nlogo.png" alt="Queenzy Logo" style="max-height:80px;">
    </div>

    <h2>Invoice - <?= htmlspecialchars($orders["order_reference"]) ?></h2>
    <p><strong>Date:</strong> <?= htmlspecialchars($orders["created_at"]) ?></p>
    <p><strong>Name:</strong> <?= htmlspecialchars($orders["firstname"] . " " . $orders["lastname"]) ?><br>
        <strong>Address:</strong> <?= htmlspecialchars(($orders["address1"] ?? "") . " " . ($orders["address2"] ?? "")) ?><br>
        <strong>City:</strong> <?= htmlspecialchars($orders["city"] ?? "") ?><br>
        <strong>Country:</strong> <?= htmlspecialchars($orders["country"] ?? "") ?><br>
        <strong>Phone:</strong> <?= htmlspecialchars($orders["phone"] ?? "") ?>
    </p>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Product</th>
                <th>Qty</th>
                <th>Price</th>
                <th>Amount</th>
            </tr>
        </thead>
        <tbody>
            <?php $count = 1; ?>
            <?php foreach ($orderedItems as $item):
                $subtotal += (float)$item['subtotal']; ?>
                <tr>
                    <td><?= $count++ ?></td>
                    <td><?= htmlspecialchars($item['product_name'] ?? "N/A") ?></td>
                    <td><?= htmlspecialchars($item['quantity'] ?? "0") ?></td>
                    <td><?= $currency . number_format($item['price'], 2) ?></td>
                    <td><?= $currency . number_format($item['subtotal'], 2) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="4">Subtotal</td>
                <td><?= $currency . number_format($subtotal, 2) ?></td>
            </tr>
            <tr class="total-row">
                <td colspan="4">Delivery Fee</td>
                <td><?= $currency . number_format(($orders['total_amount'] - $subtotal), 2) ?></td>
            </tr>
            <tr class="total-row">
                <td colspan="4">Total</td>
                <td><?= $currency . number_format($orders['total_amount'], 2) ?></td>
            </tr>
        </tfoot>
    </table>
</body>

</html>
<?php
$html = ob_get_clean();

// Create PDF
$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();

// Output to browser
$dompdf->stream("Invoice-{$orders["order_reference"]}.pdf", ["Attachment" => true]);
exit;
