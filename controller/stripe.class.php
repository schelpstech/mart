<?php

class StripePayment
{
    private $secretKey;

    public function __construct($secretKey)
    {
        $this->secretKey = $secretKey;
    }

    /**
     * Create a Checkout Session
     */

    public function createCheckoutSession($successUrl, $cancelUrl, $lineItems, $customerEmail = null)
    {
        $data = [
            "success_url" => $successUrl . "?session_id={CHECKOUT_SESSION_ID}",
            "cancel_url"  => $cancelUrl,
            "mode"        => "payment",
        ];

        foreach ($lineItems as $i => $item) {
            $data["line_items[$i][price_data][currency]"] = $item["currency"];
            $data["line_items[$i][price_data][product_data][name]"] = $item["name"];
            $data["line_items[$i][price_data][unit_amount]"] = $item["amount"];
            $data["line_items[$i][quantity]"] = $item["quantity"];
        }

        if ($customerEmail) {
            $data["customer_email"] = $customerEmail;
        }

        return $this->sendRequest("https://api.stripe.com/v1/checkout/sessions", $data);
    }

    /**
     * Retrieve a Checkout Session
     */
    public function retrieveCheckoutSession($sessionId)
    {
        return $this->sendRequest("https://api.stripe.com/v1/checkout/sessions/$sessionId", [], "GET");
    }

    /**
     * Refund a payment using PaymentIntent ID
     */
    public function refund($paymentIntentId, $amount = null)
    {
        $data = ["payment_intent" => $paymentIntentId];
        if ($amount) {
            $data["amount"] = $amount;
        }

        return $this->sendRequest("https://api.stripe.com/v1/refunds", $data);
    }

    /**
     * Handle Stripe Webhook
     *
     * @param string $payload      Raw POST body
     * @param string $sigHeader    Stripe-Signature header
     * @param string $endpointSecret  Your webhook secret from Stripe Dashboard
     * @return array|null
     */
    public function handleWebhook($payload, $sigHeader, $endpointSecret)
    {
        $tolerance = 300; // 5 minutes
        $parts = [];

        foreach (explode(",", $sigHeader) as $part) {
            list($k, $v) = explode("=", $part, 2);
            $parts[$k] = $v;
        }

        if (!isset($parts['t']) || !isset($parts['v1'])) {
            return null; // Invalid header
        }

        $signedPayload = $parts['t'] . "." . $payload;
        $expectedSig = hash_hmac("sha256", $signedPayload, $endpointSecret);

        if (!hash_equals($expectedSig, $parts['v1'])) {
            return null; // Signature verification failed
        }

        // Optional: check timestamp tolerance
        if (abs(time() - (int)$parts['t']) > $tolerance) {
            return null; // Too old
        }

        return json_decode($payload, true);
    }

    /**
     * Helper: send request with cURL
     */
    private function sendRequest($url, $data = [], $method = "POST")
    {
        $ch = curl_init();

        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_USERPWD => $this->secretKey . ":",
            CURLOPT_HTTPAUTH => CURLAUTH_BASIC,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => 0
        ]);

        if ($method === "POST") {
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        }

        $response = curl_exec($ch);
        $error = curl_error($ch);

        curl_close($ch);

        if ($error) {
            return ["success" => false, "error" => $error];
        }

        return json_decode($response, true);
    }
}
