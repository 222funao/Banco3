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
    $conn->beginTransaction();

    $statement = $conn->prepare(
        'SELECT id_cuenta, saldo FROM cuentas
         WHERE id_cuenta = :account_id AND estado = :status
         FOR UPDATE'
    );
    $statement->execute([
        'account_id' => $accountId,
        'status' => 'Activa',
    ]);
    $account = $statement->fetch();

    if (!$account) {
        throw new DomainException('cuenta_invalida');
    }
    if ((float) $account['saldo'] < $amount) {
        throw new DomainException('saldo_insuficiente');
    }

    $statement = $conn->prepare(
        'INSERT INTO transacciones (id_cuenta, tipo, monto, fecha)
         VALUES (:account_id, :type, :amount, :transaction_date)'
    );
    $statement->execute([
        'account_id' => $accountId,
        'type' => 'Retiro',
        'amount' => $amount,
        'transaction_date' => $date,
    ]);

    $statement = $conn->prepare(
        'UPDATE cuentas SET saldo = saldo - :amount
         WHERE id_cuenta = :account_id'
    );
    $statement->execute([
        'amount' => $amount,
        'account_id' => $accountId,
    ]);

    $conn->commit();
    redirect_retiro('ok');
} catch (Throwable $exception) {
    if ($conn->inTransaction()) {
        $conn->rollBack();
    }
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
