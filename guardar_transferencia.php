<?php

include("conexion.php");

if($_SERVER['REQUEST_METHOD'] == 'POST')
{
    $id_origen  = $_POST['id_cuenta_origen'];
    $id_destino = $_POST['id_cuenta_destino'];
    $monto      = $_POST['monto'];
    $fecha      = $_POST['fecha'];

    if($monto <= 0)
    {
        header("Location: transacciones.php?msg=error");
        exit();
    }

    if($id_origen == $id_destino)
    {
        header("Location: transacciones.php?msg=misma_cuenta");
        exit();
    }

    db_begin_transaction($conn);

    try
    {
        $checkSql = "
        SELECT id_cuenta, saldo
        FROM cuentas
        WHERE id_cuenta IN ('$id_origen','$id_destino')
        FOR UPDATE
        ";
        $checkResult = db_query($conn,$checkSql);

        if(db_num_rows($checkResult) < 2)
        {
            throw new Exception("cuenta_invalida");
        }

        $saldoOrigen = null;
        while($row = db_fetch_assoc($checkResult))
        {
            if($row['id_cuenta'] == $id_origen)
            {
                $saldoOrigen = $row['saldo'];
            }
        }

        if($saldoOrigen === null || $saldoOrigen < $monto)
        {
            throw new Exception("saldo_insuficiente");
        }

        // Registrar salida en cuenta origen
        $sqlInsertSalida = "
        INSERT INTO transacciones (id_cuenta, tipo, monto, fecha)
        VALUES ('$id_origen', 'Transferencia Enviada', '$monto', '$fecha')
        ";

        if(!db_query($conn,$sqlInsertSalida))
        {
            throw new Exception("error");
        }

        // Registrar entrada en cuenta destino
        $sqlInsertEntrada = "
        INSERT INTO transacciones (id_cuenta, tipo, monto, fecha)
        VALUES ('$id_destino', 'Transferencia Recibida', '$monto', '$fecha')
        ";

        if(!db_query($conn,$sqlInsertEntrada))
        {
            throw new Exception("error");
        }

        // Actualizar saldo cuenta origen
        $sqlUpdateOrigen = "
        UPDATE cuentas
        SET saldo = saldo - '$monto'
        WHERE id_cuenta = '$id_origen'
        ";

        if(!db_query($conn,$sqlUpdateOrigen))
        {
            throw new Exception("error");
        }

        // Actualizar saldo cuenta destino
        $sqlUpdateDestino = "
        UPDATE cuentas
        SET saldo = saldo + '$monto'
        WHERE id_cuenta = '$id_destino'
        ";

        if(!db_query($conn,$sqlUpdateDestino))
        {
            throw new Exception("error");
        }

        db_commit($conn);

        header("Location: transacciones.php?msg=ok");
        exit();
    }
    catch(Exception $e)
    {
        db_rollback($conn);
        header("Location: transacciones.php?msg=" . $e->getMessage());
        exit();
    }
}
else
{
    header("Location: transacciones.php");
    exit();
}

?>