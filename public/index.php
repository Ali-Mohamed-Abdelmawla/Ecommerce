<?php

// Required headers
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle OPTIONS request for CORS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header('HTTP/1.1 200 OK');
    exit;
}

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('HTTP/1.1 405 Method Not Allowed');
    echo json_encode(['errors' => [['message' => 'Only POST requests are allowed']]]);
    exit;
}

// Load dependencies
require_once __DIR__ . '/../vendor/autoload.php';

// Execute GraphQL
use App\Controller\GraphQLController;
GraphQLController::handle();