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
        header('Access-Control-Allow-Headers: Content-Type, X-API-Key, Authorization');
        header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
    }
}

function api_request_header(string $name): string
{
    $serverKey = 'HTTP_' . strtoupper(str_replace('-', '_', $name));
    $value = $_SERVER[$serverKey] ?? '';
    if (is_string($value) && $value !== '') {
        return trim($value);
    }

    if (function_exists('getallheaders')) {
        foreach (getallheaders() as $headerName => $headerValue) {
            if (strcasecmp($headerName, $name) === 0 && is_string($headerValue)) {
                return trim($headerValue);
            }
        }
    }

    return '';
}

function api_authorize(): void
{
    $expected = trim((string) env_value('DASHBOARD_API_KEY', ''));
    $received = api_request_header('X-API-Key');

    if ($received === '') {
        $authorization = api_request_header('Authorization');
        if (preg_match('/^Bearer\s+(.+)$/i', $authorization, $matches)) {
            $received = trim($matches[1]);
        }
    }

    if ($expected === '') {
        api_response(['error' => 'API no configurada', 'code' => 'API_KEY_NOT_CONFIGURED'], 503);
    }
    if (!hash_equals($expected, $received)) {
        error_log(sprintf(
            'Banco 3 API key rejected: expected_len=%d expected_sha=%s received_len=%d received_sha=%s',
            strlen($expected),
            substr(hash('sha256', $expected), 0, 12),
            strlen($received),
            $received === '' ? 'empty' : substr(hash('sha256', $received), 0, 12)
        ));
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
