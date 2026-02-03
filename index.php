<?php
/**
 * SP Global Tournament Proxy
 *
 * Прокси-сервер для глобального доступа к турнирам.
 * Все запросы перенаправляются на мастер-сервер (spanalytic.ru).
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Master server URL
$RF_SERVER = getenv('RF_SERVER_URL') ?: 'https://spanalytic.ru/api/tournament';

// Get request path
$requestUri = $_SERVER['REQUEST_URI'];
$path = parse_url($requestUri, PHP_URL_PATH);

// Remove leading slash and index.php if present
$path = preg_replace('#^/?(index\.php)?#', '', $path);
$path = ltrim($path, '/');

// Build target URL
$targetUrl = $RF_SERVER . '/' . $path;

// Forward query string
if (!empty($_SERVER['QUERY_STRING'])) {
    $targetUrl .= '?' . $_SERVER['QUERY_STRING'];
}

// Initialize cURL
$ch = curl_init();

// Set cURL options
curl_setopt($ch, CURLOPT_URL, $targetUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);

// Forward POST data
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    curl_setopt($ch, CURLOPT_POST, true);

    // Get raw POST data
    $postData = file_get_contents('php://input');
    if (!empty($postData)) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Content-Length: ' . strlen($postData)
        ]);
    } else {
        // Form data
        curl_setopt($ch, CURLOPT_POSTFIELDS, $_POST);
    }
}

// Execute request
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);

curl_close($ch);

// Handle errors
if ($error) {
    http_response_code(502);
    echo json_encode([
        'success' => false,
        'error' => 'Proxy error: ' . $error
    ]);
    exit;
}

// Return response
http_response_code($httpCode);
echo $response;
