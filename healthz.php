<?php

header('Content-Type: application/json; charset=utf-8');

$postgresDriverLoaded = extension_loaded('pdo_pgsql');
http_response_code($postgresDriverLoaded ? 200 : 503);

echo json_encode([
    'status' => $postgresDriverLoaded ? 'ok' : 'error',
    'service' => 'Banco 3',
    'php_version' => PHP_VERSION,
    'pdo_pgsql' => $postgresDriverLoaded,
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

?>
