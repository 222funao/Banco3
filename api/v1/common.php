<?php

require_once __DIR__ . '/../../conexion.php';

function api_headers(): void
{
    header('Content-Type: application/json; charset=utf-8');
    $origin = $_SERVER['HTTP_ORIGIN'] ?? '';
    $allowedOrigin = env_value('DASHBOARD_ORIGIN', 'http://localhost:5173');
    if ($origin === $allowedOrigin) {
        header("Access-Control-Allow-Origin: $origin");
        header('Vary: Origin');
        header('Access-Control-Allow-Headers: Content-Type, X-API-Key');
        header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
    }
}

function api_authorize(): void
{
    $expected = env_value('DASHBOARD_API_KEY');
    $received = $_SERVER['HTTP_X_API_KEY'] ?? '';
    if (!$expected) {
        api_response(['error' => 'API no configurada', 'code' => 'API_KEY_NOT_CONFIGURED'], 503);
    }
    if (!hash_equals($expected, $received)) {
        api_response(['error' => 'No autorizado', 'code' => 'UNAUTHORIZED'], 401);
    }
}

function api_response(array $payload, int $status = 200): never
{
    http_response_code($status);
    echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

function api_body(): array
{
    $body = json_decode(file_get_contents('php://input'), true);
    return is_array($body) ? $body : [];
}

function mask_account(string $number): string
{
    return strlen($number) > 4 ? '**** ' . substr($number, -4) : $number;
}

api_headers();
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}
?>
