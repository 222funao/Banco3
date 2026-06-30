<?php

require_once __DIR__ . '/conexion.php';

function redirect_transferencia(string $message): never
{
    header('Location: transacciones.php?msg=' . urlencode($message));
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect_transferencia('error');
}

$originId = filter_var($_POST['id_cuenta_origen'] ?? null, FILTER_VALIDATE_INT);
$destinationId = filter_var($_POST['id_cuenta_destino'] ?? null, FILTER_VALIDATE_INT);
$amount = filter_var($_POST['monto'] ?? null, FILTER_VALIDATE_FLOAT);
$date = trim((string) ($_POST['fecha'] ?? ''));
$parsedDate = DateTimeImmutable::createFromFormat('!Y-m-d', $date);

if (
    $originId === false ||
    $destinationId === false ||
    $originId < 1 ||
    $destinationId < 1 ||
    $amount === false ||
    $amount <= 0 ||
    !$parsedDate ||
    $parsedDate->format('Y-m-d') !== $date
) {
    redirect_transferencia('error');
}
if ($originId === $destinationId) {
    redirect_transferencia('misma_cuenta');
}

try {
    $conn->beginTransaction();

    $statement = $conn->prepare(
        'SELECT id_cuenta, saldo FROM cuentas
         WHERE id_cuenta IN (:origin_id, :destination_id)
           AND estado = :status
         ORDER BY id_cuenta
         FOR UPDATE'
    );
    $statement->execute([
        'origin_id' => $originId,
        'destination_id' => $destinationId,
        'status' => 'Activa',
    ]);
    $accounts = $statement->fetchAll();

    if (count($accounts) !== 2) {
        throw new DomainException('cuenta_invalida');
    }

    $originBalance = null;
    foreach ($accounts as $account) {
        if ((int) $account['id_cuenta'] === $originId) {
            $originBalance = (float) $account['saldo'];
            break;
        }
    }
    if ($originBalance === null || $originBalance < $amount) {
        throw new DomainException('saldo_insuficiente');
    }

    $insert = $conn->prepare(
        'INSERT INTO transacciones (id_cuenta, tipo, monto, fecha)
         VALUES (:account_id, :type, :amount, :transaction_date)'
    );
    $insert->execute([
        'account_id' => $originId,
        'type' => 'Transferencia Enviada',
        'amount' => $amount,
        'transaction_date' => $date,
    ]);
    $insert->execute([
        'account_id' => $destinationId,
        'type' => 'Transferencia Recibida',
        'amount' => $amount,
        'transaction_date' => $date,
    ]);

    $debit = $conn->prepare(
        'UPDATE cuentas SET saldo = saldo - :amount
         WHERE id_cuenta = :account_id'
    );
    $debit->execute([
        'amount' => $amount,
        'account_id' => $originId,
    ]);

    $credit = $conn->prepare(
        'UPDATE cuentas SET saldo = saldo + :amount
         WHERE id_cuenta = :account_id'
    );
    $credit->execute([
        'amount' => $amount,
        'account_id' => $destinationId,
    ]);

    $conn->commit();
    redirect_transferencia('ok');
} catch (Throwable $exception) {
    if ($conn->inTransaction()) {
        $conn->rollBack();
    }
    error_log(
        'Banco 3 transferencia failed [' .
        get_class($exception) .
        ']: ' .
        $exception->getMessage()
    );
    redirect_transferencia(
        $exception instanceof DomainException ? $exception->getMessage() : 'error'
    );
}
