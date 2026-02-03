<?php
/**
 * Health check endpoint for Render.com
 */
header('Content-Type: application/json');

echo json_encode([
    'status' => 'ok',
    'service' => 'sp-global-api',
    'timestamp' => date('c')
]);
