<?php

include("conexion.php");

if($_SERVER['REQUEST_METHOD'] == 'POST')
{
    $id_cliente     = $_POST['id_cliente'];
    $numero_cuenta  = db_real_escape_string($conn,$_POST['numero_cuenta']);
    $tipo_cuenta    = db_real_escape_string($conn,$_POST['tipo_cuenta']);
    $fecha_apertura = $_POST['fecha_apertura'];
    $saldo          = $_POST['saldo'];
    $estado         = db_real_escape_string($conn,$_POST['estado']);

    $sql = "
    INSERT INTO cuentas (id_cliente, numero_cuenta, tipo_cuenta, fecha_apertura, saldo, estado)
    VALUES ('$id_cliente', '$numero_cuenta', '$tipo_cuenta', '$fecha_apertura', '$saldo', '$estado')
    ";

    if(db_query($conn,$sql))
    {
        header("Location: cuentas.php");
        exit();
    }
    else
    {
        echo "Error al guardar la cuenta: " . db_error($conn);
    }
}
else
{
    header("Location: cuentas.php");
    exit();
}

?>