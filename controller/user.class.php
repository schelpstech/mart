<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class User
{
    private $model;
    private $db;

    public function __construct($db_conn)
    {
        $this->db = $db_conn;
        $this->model = new Model($db_conn);
    }

    private function env($key, $default = '')
    {
        return function_exists('env_value') ? env_value($key, $default) : $default;
    }

    private function envBool($key, $default = false)
    {
        return function_exists('env_bool') ? env_bool($key, $default) : $default;
    }

    private function configureMailer(PHPMailer $mail)
    {
        $mail->isSMTP();
        $mail->Host       = $this->env('SMTP_HOST', 'queenzystores.com');
        $mail->SMTPAuth   = $this->envBool('SMTP_AUTH', true);
        $mail->Username   = $this->env('SMTP_USERNAME', 'noreply@queenzystores.com');
        $mail->Password   = $this->env('SMTP_PASSWORD', '');
        $mail->Port       = (int) $this->env('SMTP_PORT', 465);

        $smtpSecure = strtolower((string) $this->env('SMTP_SECURE', 'ssl'));
        $mail->SMTPSecure = $smtpSecure === 'tls'
            ? PHPMailer::ENCRYPTION_STARTTLS
            : ($smtpSecure === 'none' || $smtpSecure === 'false' ? false : PHPMailer::ENCRYPTION_SMTPS);

        $mail->setFrom(
            $this->env('MAIL_FROM_ADDRESS', 'noreply@queenzystores.com'),
            $this->env('MAIL_FROM_NAME', 'Queenzy Stores')
        );
    }

    private function adminOrderEmail()
    {
        return $this->env('ADMIN_ORDER_EMAIL', 'orders@queenzystores.com');
    }

    /**
     * Check if email already exists
     */
    public function emailExists($email)
    {
        try {
            error_log("User::emailExists checking email={$email}");
            $run = $this->db->exists("users_mart", "email = '{$email}'");
            error_log("User::emailExists result=" . var_export($run, true));
            return $run;
        } catch (Exception $e) {
            error_log("User::emailExists ERROR - " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Register user
     */
    public function register($email, $phone, $password)
    {
        try {
            error_log("User::register started for email={$email}");

            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
            $verificationToken = bin2hex(random_bytes(32));

            $fields = [
                "email"              => $email,
                "phone"              => $phone,
                "password_hash"           => $hashedPassword,
                "verification_token" => $verificationToken,
                "verified"        => 0,
                "created_at"         => date("Y-m-d H:i:s"),
                "updated_at"         => date("Y-m-d H:i:s")
            ];

            $run = $this->db->insert("users_mart", $fields);
            error_log("User::register insert result=" . var_export($run, true));

            return $run;
        } catch (Exception $e) {
            error_log("User::register ERROR - " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Send verification email
     */
    public function sendVerificationEmail($email)
    {
        try {
            error_log("User::sendVerificationEmail started for {$email}");

            $user = $this->db->getRows("users_mart", [
                "where" => ["email" => $email],
                "return_type" => "single"
            ]);

            if (!$user) {
                error_log("User::sendVerificationEmail failed: no user found for {$email}");
                return false;
            }

            $verifyLink = "https://queenzystores.com/app/verify.php?token=" . $user["verification_token"];

            $mail = new PHPMailer(true);
            $this->configureMailer($mail);
            $mail->addAddress($email);

            $mail->isHTML(true);
            $mail->CharSet = 'UTF-8';
            $mail->Subject = "Verify Your Email";
            $mail->Body    = "
                <h2>Welcome!</h2>
                <p>Click the link below to verify your email address:</p>
                <a href='{$verifyLink}'>Verify Email</a>
            ";

            $result = $mail->send();
            error_log("User::sendVerificationEmail send result=" . var_export($result, true));

            return $result;
        } catch (Exception $e) {
            error_log("User::sendVerificationEmail ERROR - " . $e->getMessage());
            return false;
        }
    }

    /**
     * Verify account
     */
    public function verifyAccount($token)
    {
        try {
            error_log("User::verifyAccount started for token={$token}");

            $user = $this->db->getRows("users_mart", [
                "where" => ["verification_token" => $token],
                "return_type" => "single"
            ]);

            if ($user) {
                $this->db->update("users_mart", [
                    "verified" => 1,
                    "verification_token" => null
                ], ["user_id" => $user["user_id"]]);

                error_log("User::verifyAccount success for user_id=" . $user["id"]);
                return true;
            }

            error_log("User::verifyAccount failed: no user found for token={$token}");
            return false;
        } catch (Exception $e) {
            error_log("User::verifyAccount ERROR - " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Login user
     */
    public function login($email, $password)
    {
        try {
            error_log("User::login started for {$email}");

            $user = $this->model->getRows("users_mart", [
                "where" => ["email" => $email],
                "return_type" => "single"
            ]);

            switch (true) {
                case !$user:
                    error_log("User::login failed - email not found: {$email}");
                    return ["status" => false, "message" => "Invalid login credentials."];

                case !password_verify($password, $user["password_hash"]):
                    error_log("User::login failed - wrong password for {$email}");
                    return ["status" => false, "message" => "Invalid login credentials."];

                case !$user["verified"]:
                    error_log("User::login failed - email not verified for {$email}");
                    return [
                        "status" => false,
                        "message" => 'Please check your email for verification instructions. 
                  '
                    ];

                default:
                    // Successful login
                    $_SESSION["user_id"] = $user["user_id"];
                    $_SESSION["user_email"] = $user["email"];

                    // Update cart table to assign session items to logged-in user
                    $this->model->update(
                        "cart",
                        ["user_id" => $user["user_id"]],
                        ["session_id" => session_id()]
                    );

                    error_log("User::login success for user_id=" . $user["user_id"]);
                    return ["status" => true, "message" => "Login successful"];
            }
        } catch (Exception $e) {
            error_log("User::login ERROR - " . $e->getMessage());
            throw $e;
        }
    }

    public function getByEmail($email)
    {
        try {
            error_log("User::getByEmail started for {$email}");
            $result = $row = $this->model->getRows("users_mart", [
                "where" => ["email" => $email],
                "return_type" => "single"
            ]);
            error_log("User::getByEmail result=" . var_export($result, true));
            return $result;
        } catch (Exception $e) {
            error_log("User::getByEmail ERROR - " . $e->getMessage());
            throw $e;
        }
    }

    public function resendVerificationEmail($email)
    {
        try {
            error_log("User::resendVerificationEmail started for email={$email}");

            // Fetch user by email
            $row = $this->model->getRows("users_mart", [
                "where" => ["email" => $email],
                "return_type" => "single"
            ]);

            if ($row) {
                if ($row['is_verified'] == 1) {
                    error_log("User::resendVerificationEmail failed: email already verified");
                    return false;
                }

                // Generate new token
                $newToken = bin2hex(random_bytes(16));

                // Update with new token
                $this->model->update(
                    "users_mart",
                    ["verification_token" => $newToken],
                    ["id" => $row['id']]
                );

                // Send the verification email
                $this->sendVerificationEmail($email);

                error_log("User::resendVerificationEmail success for user_id=" . $row['id']);
                return true;
            }

            error_log("User::resendVerificationEmail failed: email not found");
            return false;
        } catch (Exception $e) {
            error_log("User::resendVerificationEmail ERROR - " . $e->getMessage());
            throw $e;
        }
    }

    public function logout()
    {
        // Destroy all session variables
        $_SESSION = array();

        // If using cookies for login, clear them too
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params["path"],
                $params["domain"],
                $params["secure"],
                $params["httponly"]
            );
        }
        // Destroy the session
        session_destroy();
        return true;
    }

    public function saveUserProfile($userId, $data)
    {
        // check if profile exists
        $exists = $this->model->getRows("user_profiles", [
            "where" => ["user_id" => $userId],
            "return_type" => "single"
        ]);

        if ($exists) {
            // update
            return $this->model->update("user_profiles", $data, ["user_id" => $userId]);
        } else {
            // insert
            $data["user_id"] = $userId;
            return $this->model->insert("user_profiles", $data);
        }
    }

    public function getUserProfile($userId)
    {
        // Step 1: Get base user info (email, phone, etc.)
        $user = $this->model->getRows("users_mart", [
            "where" => ["user_id" => $userId],
            "return_type" => "single"
        ]);

        if (!$user) {
            return null; // user not found
        }

        // Step 2: Check if profile exists
        $profile = $this->model->getRows("user_profiles", [
            "where" => ["user_id" => $userId],
            "return_type" => "single"
        ]);

        // Step 3: Merge results
        if ($profile) {
            return array_merge($user, $profile);
        }

        // If no profile, return just users_mart fields
        return $user;
    }


    public function sendOrderConfirmationEmail($orderId)
    {
        try {

            // 1. Fetch Order
            $order = $this->db->getRows("orders_mart", [
                "where" => ["order_tbl_id" => $orderId],
                "return_type" => "single"
            ]);

            if (!$order) {
                error_log("Order not found: {$orderId}");
                return false;
            }
            $orderReference = ($order['order_reference'] ?? "N/A");
            // 3. Fetch Order Items
            $orderItems = $this->db->getRows("order_items_mart", [
                "where" => ["order_item_id" => $orderId]
            ]);

            if (!$orderItems) {
                error_log("No items found for order: {$orderId}");
                return false;
            }

            // 4. Build Items Table
            $itemsHtml = "";
            $grandTotal = 0;

            foreach ($orderItems as $item) {
                $productName = htmlspecialchars($item['product_name'] ?? "Item", ENT_QUOTES, "UTF-8");
                $itemType = ucfirst($item['orderType'] ?? "product");
                $quantity = $item['quantity'];
                $price = $item['price'];
                $total = $quantity * $price;

                $grandTotal += $total;


                $itemsHtml .= "
                <tr>
                    <td>{$productName}</td>
                    <td>{$itemType}</td>
                    <td>{$quantity}</td>
                    <td>£" . number_format($price, 2) . "</td>
                    <td>£" . number_format($total, 2) . "</td>
                </tr>
            ";
            }
            $financials = qs_order_financials($order, $grandTotal);
            $fulfilmentMessage = strtolower($order['fulfilment_type'] ?? 'delivery') === 'pickup'
                ? 'We will notify you when your order is ready for pickup.'
                : 'We will notify you once your order is shipped.';

            // 5. Email HTML Template
            $emailBody = "
        <div style='font-family: Arial, sans-serif;'>
            <h2>Thank you for your order!</h2>
            <p>Hi {$order['firstname']},</p>
            <p>Your order has been successfully placed.</p>

            <h3>Order Details</h3>
            <table width='100%' border='1' cellspacing='0' cellpadding='8' style='border-collapse: collapse;'>
                <thead>
                    <tr style='background:#f2f2f2;'>
                        <th>Item</th>
                        <th>Type</th>
                        <th>Qty</th>
                        <th>Price</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    {$itemsHtml}
                </tbody>
            </table>

            <h3 style='margin-top:20px;'>Order Total: £" . number_format($financials['total'], 2) . "</h3>
            <p><strong>Fulfilment:</strong> " . qs_fulfilment_label($order['fulfilment_type'] ?? 'delivery') . "</p>
            <p><strong>Delivery Fee:</strong> £" . number_format($financials['delivery_fee'], 2) . "</p>
            <p><strong>Discount:</strong> £" . number_format($financials['discount'], 2) . "</p>

            <p>{$fulfilmentMessage}</p>
            <p>Thank you for shopping with us.</p>

            <br>
            <strong>Queenzy Stores</strong>
        </div>
        ";

            // 6. Send Mail
            $mail = new PHPMailer(true);
            $this->configureMailer($mail);
            $mail->addAddress($order['email']);

            $mail->isHTML(true);
            $mail->CharSet = 'UTF-8';
            $mail->Subject = "Order Confirmation - #" . $orderReference;
            $mail->Body    = $emailBody;

            return $mail->send();
        } catch (Exception $e) {
            error_log("Order confirmation email error: " . $e->getMessage());
            return false;
        }
    }

    public function notifyAdminOfNewOrder($orderId)
    {
        try {

            // Fetch Order
            $order = $this->db->getRows("orders_mart", [
                "where" => ["order_tbl_id" => $orderId],
                "return_type" => "single"
            ]);

            if (!$order) {
                error_log("Admin Notify: Order not found {$orderId}");
                return false;
            }
            $orderReference = ($order['order_reference'] ?? "N/A");
            // Fetch Order Items
            $orderItems = $this->db->getRows("order_items_mart", [
                "where" => ["order_item_id" => $orderId]
            ]);

            if (!$orderItems) {
                error_log("Admin Notify: No items for {$orderId}");
                return false;
            }

            $itemsHtml = "";
            $grandTotal = 0;

            foreach ($orderItems as $item) {
                $productName = htmlspecialchars($item['product_name'] ?? "Item", ENT_QUOTES, "UTF-8");
                $itemType = ucfirst($item['orderType'] ?? "product");
                $quantity = $item['quantity'];
                $price = $item['price'];
                $total = $quantity * $price;

                $grandTotal += $total;

                $itemsHtml .= "
                <tr>
                    <td>{$productName}</td>
                    <td>{$itemType}</td>
                    <td>{$quantity}</td>
                    <td>£" . number_format($price, 2) . "</td>
                    <td>£" . number_format($total, 2) . "</td>
                </tr>
            ";
            }
            $financials = qs_order_financials($order, $grandTotal);
            $address = trim(($order['address1'] ?? '') . ' ' . ($order['address2'] ?? '') . ' ' . ($order['city'] ?? '') . ' ' . ($order['postcode'] ?? ''));

            $emailBody = "
        <div style='font-family: Arial, sans-serif;'>
            <h2>New Order Received</h2>

            <p><strong>Order ID:</strong> {$orderReference}</p>
            <p><strong>Customer:</strong> {$order['firstname']} {$order['lastname']}</p>
            <p><strong>Email:</strong> {$order['email']}</p>
            <p><strong>Phone:</strong> {$order['phone']}</p>
            <p><strong>Fulfilment:</strong> " . qs_fulfilment_label($order['fulfilment_type'] ?? 'delivery') . "</p>
            <p><strong>Address:</strong> {$address}</p>

            <h3>Order Items</h3>

            <table width='100%' border='1' cellspacing='0' cellpadding='8' style='border-collapse: collapse;'>
                <thead>
                    <tr style='background:#f2f2f2;'>
                        <th>Item</th>
                        <th>Type</th>
                        <th>Qty</th>
                        <th>Price</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    {$itemsHtml}
                </tbody>
            </table>

            <h3>Total: £" . number_format($financials['total'], 2) . "</h3>
            <p><strong>Delivery Fee:</strong> £" . number_format($financials['delivery_fee'], 2) . "</p>
            <p><strong>Discount:</strong> £" . number_format($financials['discount'], 2) . "</p>

            <p>Please process this order immediately.</p>
        </div>
        ";

            $mail = new PHPMailer(true);
            $this->configureMailer($mail);
            $mail->addAddress($this->adminOrderEmail());

            $mail->isHTML(true);
            $mail->CharSet = 'UTF-8';
            $mail->Subject = "NEW ORDER - #" . $orderReference;
            $mail->Body    = $emailBody;

            return $mail->send();
        } catch (Exception $e) {
            error_log("Admin order notify error: " . $e->getMessage());
            return false;
        }
    }

    public function sendPaymentConfirmationEmail($orderId)
    {
        try {

            $order = $this->db->getRows("orders_mart", [
                "where" => ["order_tbl_id" => $orderId],
                "return_type" => "single"
            ]);

            if (!$order) {
                error_log("Payment Email: Order not found {$orderId}");
                return false;
            }

            $status = strtolower(trim($order['payment_status']));
            $orderReference = ($order['order_reference'] ?? "N/A");
            $fulfilmentLabel = qs_fulfilment_label($order['fulfilment_type'] ?? 'delivery');
            $fulfilmentMessage = strtolower($order['fulfilment_type'] ?? 'delivery') === 'pickup'
                ? 'We will notify you when your order is ready for pickup.'
                : 'Your order is now being processed and will be shipped soon.';
            // Default subject and message
            $subject = "";
            $messageContent = "";

            if ($status === "paid") {

                $subject = "Payment Successful - Order #" . $orderReference;

                $messageContent = "
                <h2 style='color:green;'>Payment Successful</h2>
                <p>Hi {$order['firstname']},</p>
                <p>We have successfully received your payment for Order #{$orderReference}.</p>
                <p><strong>Order Reference:</strong> " . ($orderReference ?? 'N/A') . "</p>
                <p><strong>Order Amount:</strong> £" . number_format($order['total_amount'], 2) . "</p>
                <p><strong>Fulfilment:</strong> {$fulfilmentLabel}</p>
                <p>{$fulfilmentMessage}</p>
                <p>Thank you for shopping with Queenzy Stores.</p>
            ";
            } elseif ($status === "failed") {

                $subject = "Payment Failed - Order #" . $orderReference;

                $messageContent = "
                <h2 style='color:red;'>Payment Failed</h2>
                <p>Hi {$order['firstname']},</p>
                <p>Unfortunately, your payment for Order #{$orderReference} was not successful.</p>
                <p>Please log in to your account and retry the payment.</p>
                <p>If you believe this is an error, kindly contact us at 
                <strong>orders@queenzystores.com</strong>.</p>
            ";
            } elseif ($status === "pending") {

                $subject = "Payment Pending - Order #" . $orderReference;

                $messageContent = "
                <h2 style='color:orange;'>Payment Pending</h2>
                <p>Hi {$order['firstname']},</p>
                <p>Your payment for Order #{$orderReference} is currently pending.</p>
                <p>Please log in to your account to complete the payment.</p>
                <p>If you have already made the payment, kindly wait a few minutes and check again.</p>
                <p>If you notice any issue, contact 
                <strong>orders@queenzystores.com</strong>.</p>
            ";
            } else {

                $subject = "Order Payment Update - Order #" . $orderReference;

                $messageContent = "
                <h2>Payment Status Update</h2>
                <p>Hi {$order['firstname']},</p>
                <p>Your payment status for Order #{$orderReference} is currently: <strong>{$status}</strong>.</p>
                <p>If this seems incorrect, please contact 
                <strong>orders@queenzystores.com</strong>.</p>
            ";
            }

            $emailBody = "
        <div style='font-family: Arial, sans-serif;'>
            {$messageContent}
            <hr>
            <p>You can log in to your account here:</p>
            <p><a href='https://queenzystores.com'>
            Login to Your Account</a></p>
            <br>
            <strong>Queenzy Stores Team</strong>
        </div>
        ";

            $mail = new PHPMailer(true);
            $this->configureMailer($mail);
            $mail->addAddress($order['email']);

            $mail->isHTML(true);
            $mail->CharSet = 'UTF-8';
            $mail->Subject = $subject;
            $mail->Body    = $emailBody;

            return $mail->send();
        } catch (Exception $e) {
            error_log("Payment confirmation email error: " . $e->getMessage());
            return false;
        }
    }
}
