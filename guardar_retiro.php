<?php

require_once __DIR__ . '/conexion.php';

function redirect_retiro(string $message): never
{
    header('Location: transacciones.php?msg=' . urlencode($message));
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect_retiro('error');
}

$accountId = filter_var($_POST['id_cuenta'] ?? null, FILTER_VALIDATE_INT);
$amount = filter_var($_POST['monto'] ?? null, FILTER_VALIDATE_FLOAT);
$date = trim((string) ($_POST['fecha'] ?? ''));
$parsedDate = DateTimeImmutable::createFromFormat('!Y-m-d', $date);

if (
    $accountId === false ||
    $accountId < 1 ||
    $amount === false ||
    $amount <= 0 ||
    !$parsedDate ||
    $parsedDate->format('Y-m-d') !== $date
) {
    redirect_retiro('error');
}

try {
    $statement = $conn->prepare(
        'WITH updated_account AS (
            UPDATE cuentas
            SET saldo = saldo - :balance_amount
            WHERE id_cuenta = :account_id
              AND estado = :status
              AND saldo >= :minimum_balance
            RETURNING id_cuenta
         )
         INSERT INTO transacciones (id_cuenta, tipo, monto, fecha)
         SELECT id_cuenta, :type, :transaction_amount, :transaction_date
         FROM updated_account
         RETURNING id_transaccion'
    );
    $statement->execute([
        'balance_amount' => $amount,
        'account_id' => $accountId,
        'status' => 'Activa',
        'minimum_balance' => $amount,
        'type' => 'Retiro',
        'transaction_amount' => $amount,
        'transaction_date' => $date,
    ]);

    if (!$statement->fetch()) {
        $accountCheck = $conn->prepare(
            'SELECT saldo FROM cuentas
             WHERE id_cuenta = :account_id AND estado = :status'
        );
        $accountCheck->execute([
            'account_id' => $accountId,
            'status' => 'Activa',
        ]);
        throw new DomainException(
            $accountCheck->fetch() ? 'saldo_insuficiente' : 'cuenta_invalida'
        );
    }
    redirect_retiro('ok');
} catch (Throwable $exception) {
    error_log(
        'Banco 3 retiro failed [' .
        get_class($exception) .
        ']: ' .
        $exception->getMessage()
    );
    redirect_retiro(
        $exception instanceof DomainException ? $exception->getMessage() : 'error'
    );
}
