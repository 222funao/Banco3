<?php

include("conexion.php");

if(isset($_GET['id']))
{
    $id = $_GET['id'];

    $consulta = db_query($conn,"
    SELECT estado
    FROM prestamos
    WHERE id_prestamo = '$id'
    ");

    if(db_num_rows($consulta) == 0)
    {
        header("Location: prestamos.php?msg=error");
        exit();
    }

    $prestamo = db_fetch_assoc($consulta);

    if(
        $prestamo['estado'] == 'Pendiente' ||
        $prestamo['estado'] == 'Rechazado'
    )
    {
        db_query($conn,"
        DELETE FROM prestamos
        WHERE id_prestamo = '$id'
        ");

        header("Location: prestamos.php?msg=eliminado");
        exit();
    }

    header("Location: prestamos.php?msg=no_se_puede_eliminar");
    exit();
}

header("Location: prestamos.php");
exit();

?>