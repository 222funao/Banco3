<?php

include("conexion.php");

if($_SERVER['REQUEST_METHOD'] == 'POST')
{
    $id_cuenta = $_POST['id_cuenta'];
    $monto     = $_POST['monto'];
    $fecha     = $_POST['fecha'];

    if($monto <= 0)
    {
        header("Location: transacciones.php?msg=error");
        exit();
    }

    db_begin_transaction($conn);

    try
    {
        $checkSql = "SELECT id_cuenta, saldo FROM cuentas WHERE id_cuenta = '$id_cuenta' FOR UPDATE";
        $checkResult = db_query($conn,$checkSql);

        if(db_num_rows($checkResult) == 0)
        {
            throw new Exception("cuenta_invalida");
        }

        $sqlInsert = "
        INSERT INTO transacciones (id_cuenta, tipo, monto, fecha)
        VALUES ('$id_cuenta', 'Deposito', '$monto', '$fecha')
        ";

        if(!db_query($conn,$sqlInsert))
        {
            throw new Exception("error");
        }

        $sqlUpdate = "
        UPDATE cuentas
        SET saldo = saldo + '$monto'
        WHERE id_cuenta = '$id_cuenta'
        ";

        if(!db_query($conn,$sqlUpdate))
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