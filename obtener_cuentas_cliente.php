<?php

include("conexion.php");

header('Content-Type: application/json');

if(isset($_GET['id_cliente']))
{
    $id_cliente = db_real_escape_string($conn,$_GET['id_cliente']);

    $sql = "
    SELECT id_cuenta, numero_cuenta, saldo
    FROM cuentas
    WHERE id_cliente = '$id_cliente'
    ORDER BY numero_cuenta ASC
    ";

    $resultado = db_query($conn,$sql);

    $cuentas = array();

    while($fila = db_fetch_assoc($resultado))
    {
        $cuentas[] = array(
            "id_cuenta" => $fila['id_cuenta'],
            "numero_cuenta" => $fila['numero_cuenta'],
            "saldo" => number_format($fila['saldo'],2)
        );
    }

    echo json_encode($cuentas);
}
else
{
    echo json_encode(array());
}

?>