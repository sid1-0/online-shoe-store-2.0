<?php
header('Content-Type: application/json');

$secretKey = "6Lc8G1UrAAAAAHnQkbFIfN-YPyMNcAaTuAbr4_XU";

$recaptchaResponse = $_POST['g-recaptcha-response'] ?? '';

if (empty($recaptchaResponse)) {
    echo json_encode(array("success" => false, "error-codes" => ["missing-input-response"]));
    exit;
}

$url = 'https://www.google.com/recaptcha/api/siteverify';
$data = array(
    'secret' => $secretKey,
    'response' => $recaptchaResponse
);

$options = array(
    'http' => array(
        'header' => "Content-type: application/x-www-form-urlencoded\r\n",
        'method' => 'POST',
        'content' => http_build_query($data)
    )
);

$context = stream_context_create($options);
$verify = file_get_contents($url, false, $context);
$captchaSuccess = json_decode($verify);

if ($captchaSuccess->success) {
    echo json_encode(array("success" => true));
} else {
    echo json_encode(array("success" => false, "error-codes" => $captchaSuccess->{'error-codes'}));
}
?> 