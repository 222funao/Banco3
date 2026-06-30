<?php

include("conexion.php");

if(isset($_GET['id']))
{
    $id_cuenta = $_GET['id'];

    $sql = "DELETE FROM cuentas WHERE id_cuenta = '$id_cuenta'";

    if(db_query($conn,$sql))
    {
        header("Location: cuentas.php");
        exit();
    }
    else
    {
        echo "Error al eliminar la cuenta: " . db_error($conn);
    }
}
else
{
    header("Location: cuentas.php");
    exit();
}

?>