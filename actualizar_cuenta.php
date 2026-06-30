<?php

include("conexion.php");

if($_SERVER['REQUEST_METHOD'] == 'POST')
{
    $id_cuenta      = $_POST['id_cuenta'];
    $id_cliente     = $_POST['id_cliente'];
    $numero_cuenta  = db_real_escape_string($conn,$_POST['numero_cuenta']);
    $tipo_cuenta    = db_real_escape_string($conn,$_POST['tipo_cuenta']);
    $fecha_apertura = $_POST['fecha_apertura'];
    $saldo          = $_POST['saldo'];
    $estado         = db_real_escape_string($conn,$_POST['estado']);

    $sql = "
    UPDATE cuentas
    SET
        id_cliente = '$id_cliente',
        numero_cuenta = '$numero_cuenta',
        tipo_cuenta = '$tipo_cuenta',
        fecha_apertura = '$fecha_apertura',
        saldo = '$saldo',
        estado = '$estado'
    WHERE id_cuenta = '$id_cuenta'
    ";

    if(db_query($conn,$sql))
    {
        header("Location: cuentas.php");
        exit();
    }
    else
    {
        echo "Error al actualizar la cuenta: " . db_error($conn);
    }
}
else
{
    header("Location: cuentas.php");
    exit();
}

?>