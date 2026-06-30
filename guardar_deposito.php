<?php

require_once __DIR__ . '/conexion.php';

header('X-Banco3-Transaction-Version: 2');

function redirect_deposito(string $message): never
{
    header('Location: transacciones.php?msg=' . urlencode($message));
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect_deposito('error');
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
    redirect_deposito('error');
}

$stage = 'begin';

try {
    $conn->beginTransaction();

    $stage = 'lock_account';
    $statement = $conn->prepare(
        'SELECT id_cuenta FROM cuentas
         WHERE id_cuenta = :account_id AND estado = :status
         FOR UPDATE'
    );
    $statement->execute([
        'account_id' => $accountId,
        'status' => 'Activa',
    ]);
    if (!$statement->fetch()) {
        throw new DomainException('cuenta_invalida');
    }

    $stage = 'insert_transaction';
    $statement = $conn->prepare(
        'INSERT INTO transacciones (id_cuenta, tipo, monto, fecha)
         VALUES (:account_id, :type, :amount, :transaction_date)'
    );
    $statement->execute([
        'account_id' => $accountId,
        'type' => 'Deposito',
        'amount' => $amount,
        'transaction_date' => $date,
    ]);

    $stage = 'update_balance';
    $statement = $conn->prepare(
        'UPDATE cuentas SET saldo = saldo + :amount
         WHERE id_cuenta = :account_id'
    );
    $statement->execute([
        'amount' => $amount,
        'account_id' => $accountId,
    ]);

    $stage = 'commit';
    $conn->commit();
    redirect_deposito('ok');
} catch (Throwable $exception) {
    if ($conn->inTransaction()) {
        $conn->rollBack();
    }
    error_log(
        'Banco 3 deposito failed [' .
        get_class($exception) .
        ']: ' .
        $exception->getMessage()
    );
    header('X-Banco3-Transaction-Stage: ' . $stage);
    header('X-Banco3-Transaction-Error: ' . get_class($exception));
    header('X-Banco3-Transaction-Code: ' . $exception->getCode());
    redirect_deposito(
        $exception instanceof DomainException ? $exception->getMessage() : 'error'
    );
}
