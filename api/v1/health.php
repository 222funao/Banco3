<?php

require_once __DIR__ . '/common.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    api_response(['error' => 'Metodo no permitido', 'code' => 'METHOD_NOT_ALLOWED'], 405);
}

$databaseTime = $conn->query('SELECT CURRENT_TIMESTAMP')->fetchColumn();
api_response([
    'status' => 'ok',
    'bank' => ['id' => 'bank-3', 'name' => env_value('BANK_NAME', 'Banco 3')],
    'database' => 'connected',
    'database_time' => $databaseTime,
]);

?>
