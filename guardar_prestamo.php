<?php

include("conexion.php");

if($_SERVER['REQUEST_METHOD'] == 'POST')
{
    $id_cliente = $_POST['id_cliente'];
    $monto      = $_POST['monto'];
    $interes    = $_POST['interes'];
    $plazo      = $_POST['plazo'];

    if($monto <= 0 || $interes < 0 || $plazo <= 0)
    {
        header("Location: prestamos.php?msg=error");
        exit();
    }

    $sql = "
    INSERT INTO prestamos (id_cliente, monto, interes, plazo, estado)
    VALUES ('$id_cliente', '$monto', '$interes', '$plazo', 'Pendiente')
    ";

    if(db_query($conn,$sql))
    {
        header("Location: prestamos.php?msg=ok");
        exit();
    }
    else
    {
        header("Location: prestamos.php?msg=error");
        exit();
    }
}
else
{
    header("Location: prestamos.php");
    exit();
}

?>