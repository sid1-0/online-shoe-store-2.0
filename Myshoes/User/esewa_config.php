<?php
/**
 * eSewa Payment Gateway Configuration
 * ------------------------------------
 * ESEWA_MODE:
 *   'test' -> uses eSewa's public UAT/sandbox environment. No real money moves.
 *             Test eSewa ID: 9806014442
 *             Test password: password   Test MPIN: 1122   Test Token: 123456
 *   'live' -> uses your real eSewa merchant account. Requires an approved
 *             merchant agreement with eSewa (see https://esewa.com.np for onboarding).
 *
 * To go live, change ESEWA_MODE to 'live' and fill in your real
 * ESEWA_MERCHANT_CODE (Product Code) and ESEWA_SECRET_KEY given to you by eSewa.
 */

define('ESEWA_MODE', 'test'); // 'test' or 'live'

if (ESEWA_MODE === 'live') {
    define('ESEWA_MERCHANT_CODE', 'YOUR_LIVE_PRODUCT_CODE');   // from eSewa merchant dashboard
    define('ESEWA_SECRET_KEY', 'YOUR_LIVE_SECRET_KEY');        // from eSewa merchant dashboard
    define('ESEWA_PAYMENT_URL', 'https://epay.esewa.com.np/api/epay/main/v2/form');
    define('ESEWA_STATUS_CHECK_URL', 'https://epay.esewa.com.np/api/epay/transaction/status/');
} else {
    // Official eSewa test/sandbox merchant credentials (publicly documented by eSewa)
    define('ESEWA_MERCHANT_CODE', 'EPAYTEST');
    define('ESEWA_SECRET_KEY', '8gBm/:&EnhH.1/q');
    define('ESEWA_PAYMENT_URL', 'https://rc-epay.esewa.com.np/api/epay/main/v2/form');
    define('ESEWA_STATUS_CHECK_URL', 'https://rc.esewa.com.np/api/epay/transaction/status/');
}

// Dynamically generate the success and failure URLs to prevent 404 Not Found errors
if (php_sapi_name() !== 'cli' && isset($_SERVER['HTTP_HOST'])) {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
    $host = $_SERVER['HTTP_HOST'];
    $baseUrl = $protocol . $host . rtrim(dirname($_SERVER['PHP_SELF']), '/\\') . '/';

    define('ESEWA_SUCCESS_URL', $baseUrl . 'esewa_success.php');
    define('ESEWA_FAILURE_URL', $baseUrl . 'esewa_failure.php');
} else {
    // Fallback for CLI context — these won't be used in CLI, but prevents fatal errors
    define('ESEWA_SUCCESS_URL', '');
    define('ESEWA_FAILURE_URL', '');
}
