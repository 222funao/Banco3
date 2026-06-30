<?php

include("conexion.php");

if($_SERVER['REQUEST_METHOD'] == 'POST')
{
    $id_prestamo = $_POST['id_prestamo'];
    $estado = $_POST['estado'];

    db_begin_transaction($conn);

    try
    {
        $consulta = db_query($conn,"
        SELECT *
        FROM prestamos
        WHERE id_prestamo = '$id_prestamo'
        ");

        if(db_num_rows($consulta) == 0)
        {
            throw new Exception();
        }

        $prestamo = db_fetch_assoc($consulta);

        if($prestamo['estado'] != 'Pendiente')
        {
            throw new Exception();
        }

        if($estado == "Aprobado")
        {
            if(
                !isset($_POST['id_cuenta_destino']) ||
                empty($_POST['id_cuenta_destino'])
            )
            {
                header("Location: prestamos.php?msg=cuenta_requerida");
                exit();
            }

            $id_cuenta = $_POST['id_cuenta_destino'];
            $monto = $prestamo['monto'];

            $verificar = db_query($conn,"
            SELECT *
            FROM cuentas
            WHERE id_cuenta = '$id_cuenta'
            ");

            if(db_num_rows($verificar) == 0)
            {
                throw new Exception();
            }

            db_query($conn,"
            UPDATE cuentas
            SET saldo = saldo + $monto
            WHERE id_cuenta = '$id_cuenta'
            ");

            db_query($conn,"
            INSERT INTO transacciones
            (id_cuenta,tipo,monto)
            VALUES
            ('$id_cuenta','Desembolso Préstamo','$monto')
            ");

            db_query($conn,"
            UPDATE prestamos
            SET estado='Aprobado'
            WHERE id_prestamo='$id_prestamo'
            ");

            db_commit($conn);

            header("Location: prestamos.php?msg=aprobado");
            exit();
        }

        elseif($estado == "Rechazado")
        {
            db_query($conn,"
            UPDATE prestamos
            SET estado='Rechazado'
            WHERE id_prestamo='$id_prestamo'
            ");

            db_commit($conn);

            header("Location: prestamos.php?msg=actualizado");
            exit();
        }

        throw new Exception();
    }
    catch(Exception $e)
    {
        db_rollback($conn);
        header("Location: prestamos.php?msg=error");
        exit();
    }
}

header("Location: prestamos.php");
exit();

?>