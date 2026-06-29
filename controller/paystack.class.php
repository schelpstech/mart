<?php
class PaystackPayment
{
    private $secretKey;

    public function __construct($secretKey = null)
    {
        if ($secretKey === null && function_exists('env_value')) {
            $secretKey = env_value('PAYSTACK_SECRET_KEY', '');
        }

        $this->secretKey = (string) $secretKey;
    }

    public function initializePayment($email, $amount, $callbackUrl, $reference = null)
    {
        if (trim($this->secretKey) === '') {
            throw new Exception("Paystack secret key is not configured.");
        }

        $url = "https://api.paystack.co/transaction/initialize";
    
        // Generate a unique reference if none is provided
        if (!$reference) {
            $reference = "TXN_" . uniqid() . time(); // Custom format
        }
    
        $data = [
            'email' => $email,
            'amount' => $amount,
            'callback_url' => $callbackUrl,
            'reference' => $reference
        ];
    
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Authorization: Bearer {$this->secretKey}",
            "Content-Type: application/json"
        ]);
    
        $response = curl_exec($ch);
        $error = curl_error($ch);
        curl_close($ch);
    
        if ($error) {
            throw new Exception("cURL Error: $error");
        }
    
        $decodedResponse = json_decode($response, true);
    
        if (!isset($decodedResponse['status']) || !$decodedResponse['status']) {
            throw new Exception("Paystack Initialization Failed: " . ($decodedResponse['message'] ?? 'Unknown error'));
        }
    
        return $decodedResponse;
    }
    
    public function verifyTransaction($reference)
{
    if (trim($this->secretKey) === '') {
        throw new Exception("Paystack secret key is not configured.");
    }

    $url = "https://api.paystack.co/transaction/verify/$reference";

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Authorization: Bearer {$this->secretKey}"
    ]);

    $response = curl_exec($ch);
    $error = curl_error($ch);
    curl_close($ch);

    if ($error) {
        throw new Exception("cURL Error: $error");
    }

    $decodedResponse = json_decode($response, true);

    if (!isset($decodedResponse['status']) || !$decodedResponse['status']) {
        throw new Exception("Paystack Verification Failed: " . ($decodedResponse['message'] ?? 'Unknown error'));
    }

    return $decodedResponse;
}

}
