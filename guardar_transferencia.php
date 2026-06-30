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
    $statement = $conn->prepare(
        'WITH debited AS (
            UPDATE cuentas
            SET saldo = saldo - :debit_amount
            WHERE id_cuenta = :origin_id
              AND estado = :origin_status
              AND saldo >= :minimum_balance
              AND EXISTS (
                  SELECT 1 FROM cuentas destination
                  WHERE destination.id_cuenta = :destination_check_id
                    AND destination.estado = :destination_check_status
              )
            RETURNING id_cuenta
         ),
         credited AS (
            UPDATE cuentas
            SET saldo = saldo + :credit_amount
            WHERE id_cuenta = :destination_id
              AND estado = :destination_status
              AND EXISTS (SELECT 1 FROM debited)
            RETURNING id_cuenta
         ),
         outgoing AS (
            INSERT INTO transacciones (id_cuenta, tipo, monto, fecha)
            SELECT id_cuenta, :outgoing_type, :outgoing_amount, :outgoing_date
            FROM debited
            WHERE EXISTS (SELECT 1 FROM credited)
            RETURNING id_transaccion
         ),
         incoming AS (
            INSERT INTO transacciones (id_cuenta, tipo, monto, fecha)
            SELECT id_cuenta, :incoming_type, :incoming_amount, :incoming_date
            FROM credited
            WHERE EXISTS (SELECT 1 FROM outgoing)
            RETURNING id_transaccion
         )
         SELECT
            (SELECT COUNT(*) FROM debited) AS debited_count,
            (SELECT COUNT(*) FROM credited) AS credited_count,
            (SELECT COUNT(*) FROM outgoing) AS outgoing_count,
            (SELECT COUNT(*) FROM incoming) AS incoming_count'
    );
    $statement->execute([
        'debit_amount' => $amount,
        'origin_id' => $originId,
        'origin_status' => 'Activa',
        'minimum_balance' => $amount,
        'destination_check_id' => $destinationId,
        'destination_check_status' => 'Activa',
        'credit_amount' => $amount,
        'destination_id' => $destinationId,
        'destination_status' => 'Activa',
        'outgoing_type' => 'Transferencia Enviada',
        'outgoing_amount' => $amount,
        'outgoing_date' => $date,
        'incoming_type' => 'Transferencia Recibida',
        'incoming_amount' => $amount,
        'incoming_date' => $date,
    ]);
    $result = $statement->fetch();
    if (
        !$result ||
        (int) $result['debited_count'] !== 1 ||
        (int) $result['credited_count'] !== 1 ||
        (int) $result['outgoing_count'] !== 1 ||
        (int) $result['incoming_count'] !== 1
    ) {
        $accountCheck = $conn->prepare(
            'SELECT id_cuenta, saldo FROM cuentas
             WHERE id_cuenta IN (:origin_id, :destination_id)
               AND estado = :status'
        );
        $accountCheck->execute([
            'origin_id' => $originId,
            'destination_id' => $destinationId,
            'status' => 'Activa',
        ]);
        $accounts = $accountCheck->fetchAll();
        if (count($accounts) !== 2) {
            throw new DomainException('cuenta_invalida');
        }
        throw new DomainException('saldo_insuficiente');
    }
    redirect_transferencia('ok');
} catch (Throwable $exception) {
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
