<?php
require_once __DIR__ . '/esewa_config.php';

/**
 * Build the HMAC-SHA256 signature eSewa requires, base64-encoded.
 * $fields must be an associative array in the exact order eSewa expects,
 * e.g. ['total_amount' => ..., 'transaction_uuid' => ..., 'product_code' => ...]
 */
function esewa_generate_signature(array $fields, string $secretKey): string
{
    $message = [];
    foreach ($fields as $key => $value) {
        $message[] = $key . '=' . $value;
    }
    $message = implode(',', $message);

    $hash = hash_hmac('sha256', $message, $secretKey, true);
    return base64_encode($hash);
}

/**
 * Generate a unique transaction UUID for a new order/payment attempt.
 * eSewa requires this to be unique per payment attempt.
 */
function esewa_generate_transaction_uuid(): string
{
    return date('Ymd-His') . '-' . bin2hex(random_bytes(4));
}

/**
 * Verify the base64 "data" query param eSewa sends back to the success URL.
 * Returns the decoded array on success, or false if the signature is invalid.
 */
function esewa_verify_response(string $base64Data)
{
    $json = base64_decode($base64Data);
    if ($json === false) {
        return false;
    }
    $data = json_decode($json, true);
    if (!$data || !isset($data['signature'], $data['signed_field_names'])) {
        return false;
    }

    $signedFieldNames = explode(',', $data['signed_field_names']);
    $fieldsToSign = [];
    foreach ($signedFieldNames as $field) {
        if (!isset($data[$field])) {
            return false;
        }
        $fieldsToSign[$field] = $data[$field];
    }

    $expectedSignature = esewa_generate_signature($fieldsToSign, ESEWA_SECRET_KEY);

    if (!hash_equals($expectedSignature, $data['signature'])) {
        return false;
    }

    return $data;
}

/**
 * Double-check the transaction status directly with eSewa's servers
 * (belt-and-braces, in addition to the signature check above).
 */
function esewa_check_transaction_status(string $transactionUuid, string $totalAmount)
{
    $url = ESEWA_STATUS_CHECK_URL . '?product_code=' . urlencode(ESEWA_MERCHANT_CODE)
         . '&total_amount=' . urlencode($totalAmount)
         . '&transaction_uuid=' . urlencode($transactionUuid);

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 15);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($response === false || $httpCode !== 200) {
        return null;
    }

    $result = json_decode($response, true);
    return $result; // contains ['status' => 'COMPLETE'|'PENDING'|'FULL_REFUND'|... , ...]
}
