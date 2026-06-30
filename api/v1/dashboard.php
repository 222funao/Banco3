<?php

require_once __DIR__ . '/common.php';
api_authorize();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    api_response(['error' => 'Metodo no permitido', 'code' => 'METHOD_NOT_ALLOWED'], 405);
}

$payload = api_body();
$cedula = trim((string) ($payload['cedula'] ?? ''));
if (!preg_match('/^[0-9]{6,20}$/', $cedula)) {
    api_response([
        'error' => 'La cedula debe contener entre 6 y 20 digitos',
        'code' => 'INVALID_CEDULA',
    ], 400);
}

$limit = filter_var($payload['transaction_limit'] ?? 20, FILTER_VALIDATE_INT);
if ($limit === false) {
    api_response(['error' => 'transaction_limit no es valido', 'code' => 'INVALID_LIMIT'], 400);
}
$limit = max(1, min($limit, 100));

$statement = $conn->prepare(
    'SELECT id_cliente, cedula, nombres, apellidos, correo, telefono
     FROM clientes WHERE cedula = :cedula'
);
$statement->execute(['cedula' => $cedula]);
$customer = $statement->fetch();
if (!$customer) {
    api_response([
        'error' => 'No existe un cliente de Banco 3 con esa cedula',
        'code' => 'CUSTOMER_NOT_FOUND',
    ], 404);
}

$customerId = $customer['id_cliente'];
$incomeTypes = "'Deposito', 'Depósito', 'Transferencia Recibida', 'Desembolso Préstamo'";
$expenseTypes = "'Retiro', 'Transferencia Enviada'";

$statement = $conn->prepare(
    "SELECT c.id_cuenta, c.numero_cuenta, c.tipo_cuenta, c.fecha_apertura,
            c.saldo AS balance, c.estado,
            COALESCE(SUM(CASE
                WHEN t.tipo IN ($incomeTypes) THEN t.monto
                WHEN t.tipo IN ($expenseTypes) THEN -t.monto
                ELSE 0 END
            ) FILTER (WHERE t.fecha >= DATE_TRUNC('month', CURRENT_DATE)), 0) AS month_change
     FROM cuentas c
     LEFT JOIN transacciones t ON t.id_cuenta = c.id_cuenta
     WHERE c.id_cliente = :customer_id
     GROUP BY c.id_cuenta
     ORDER BY c.fecha_apertura DESC, c.id_cuenta DESC"
);
$statement->execute(['customer_id' => $customerId]);
$accounts = $statement->fetchAll();

$statement = $conn->prepare(
    "SELECT
        COALESCE((SELECT SUM(saldo) FROM cuentas
                  WHERE id_cliente = :balance_id AND estado = 'Activa'), 0) AS balance,
        COALESCE((SELECT SUM(t.monto) FROM transacciones t
                  JOIN cuentas c ON c.id_cuenta = t.id_cuenta
                  WHERE c.id_cliente = :income_id AND t.tipo IN ($incomeTypes)
                    AND t.fecha >= DATE_TRUNC('month', CURRENT_DATE)), 0) AS income,
        COALESCE((SELECT SUM(t.monto) FROM transacciones t
                  JOIN cuentas c ON c.id_cuenta = t.id_cuenta
                  WHERE c.id_cliente = :expense_id AND t.tipo IN ($expenseTypes)
                    AND t.fecha >= DATE_TRUNC('month', CURRENT_DATE)), 0) AS expenses"
);
$statement->execute([
    'balance_id' => $customerId,
    'income_id' => $customerId,
    'expense_id' => $customerId,
]);
$summary = $statement->fetch();

$statement = $conn->prepare(
    "WITH days AS (
        SELECT GENERATE_SERIES(
            CURRENT_DATE - INTERVAL '6 days', CURRENT_DATE, INTERVAL '1 day'
        )::date AS activity_date
     )
     SELECT d.activity_date,
            COALESCE(SUM(CASE
                WHEN t.tipo IN ($incomeTypes) THEN t.monto
                WHEN t.tipo IN ($expenseTypes) THEN -t.monto
                ELSE 0 END), 0) AS net_amount,
            COUNT(t.id_transaccion) AS transaction_count
     FROM days d
     LEFT JOIN transacciones t ON t.fecha::date = d.activity_date
       AND t.id_cuenta IN (SELECT id_cuenta FROM cuentas WHERE id_cliente = :customer_id)
     GROUP BY d.activity_date
     ORDER BY d.activity_date"
);
$statement->execute(['customer_id' => $customerId]);
$activity = $statement->fetchAll();

$statement = $conn->prepare(
    'SELECT t.id_transaccion, t.tipo, t.monto, t.fecha,
            c.id_cuenta, c.numero_cuenta
     FROM transacciones t
     JOIN cuentas c ON c.id_cuenta = t.id_cuenta
     WHERE c.id_cliente = :customer_id
     ORDER BY t.fecha DESC, t.id_transaccion DESC
     LIMIT :transaction_limit'
);
$statement->bindValue('customer_id', $customerId, PDO::PARAM_INT);
$statement->bindValue('transaction_limit', $limit, PDO::PARAM_INT);
$statement->execute();
$transactions = $statement->fetchAll();

foreach ($accounts as &$account) {
    $account['id_cuenta'] = (int) $account['id_cuenta'];
    $account['balance'] = (float) $account['balance'];
    $account['month_change'] = (float) $account['month_change'];
    $account['masked_number'] = mask_account($account['numero_cuenta']);
}
unset($account);

foreach ($activity as &$day) {
    $day['net_amount'] = (float) $day['net_amount'];
    $day['transaction_count'] = (int) $day['transaction_count'];
}
unset($day);

foreach ($transactions as &$transaction) {
    $isIncome = in_array($transaction['tipo'], [
        'Deposito', 'Depósito', 'Transferencia Recibida', 'Desembolso Préstamo'
    ], true);
    $transaction['id_transaccion'] = (int) $transaction['id_transaccion'];
    $transaction['id_cuenta'] = (int) $transaction['id_cuenta'];
    $transaction['monto'] = (float) $transaction['monto'];
    $transaction['direction'] = $isIncome ? 'income' : 'expense';
    $transaction['signed_amount'] = $isIncome
        ? $transaction['monto']
        : -$transaction['monto'];
    $transaction['masked_account'] = mask_account($transaction['numero_cuenta']);
}
unset($transaction);

$customer['id_cliente'] = (int) $customer['id_cliente'];
$balance = (float) $summary['balance'];
$income = (float) $summary['income'];
$expenses = (float) $summary['expenses'];

api_response([
    'bank' => [
        'id' => 'bank-3',
        'name' => env_value('BANK_NAME', 'Banco 3'),
        'status' => 'connected',
    ],
    'customer' => $customer,
    'summary' => [
        'currency' => env_value('BANK_CURRENCY', 'USD'),
        'balance' => $balance,
        'income_this_month' => $income,
        'expenses_this_month' => $expenses,
        'net_savings_this_month' => $income - $expenses,
        'active_accounts' => count(array_filter(
            $accounts,
            fn($account) => $account['estado'] === 'Activa'
        )),
    ],
    'accounts' => $accounts,
    'daily_activity' => $activity,
    'recent_transactions' => $transactions,
    'generated_at' => date(DATE_ATOM),
]);

?>
