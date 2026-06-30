<?php

include("conexion.php");

if(isset($_POST['id_cliente']))
{
    $id = $_POST['id_cliente'];

    $cedula = $_POST['cedula'];
    $nombres = $_POST['nombres'];
    $apellidos = $_POST['apellidos'];
    $direccion = $_POST['direccion'];
    $telefono = $_POST['telefono'];
    $correo = $_POST['correo'];

    $sql = "UPDATE clientes SET
            cedula='$cedula',
            nombres='$nombres',
            apellidos='$apellidos',
            direccion='$direccion',
            telefono='$telefono',
            correo='$correo'
            WHERE id_cliente='$id'";

    db_query($conn, $sql);
}

header("Location: clientes.php");
exit();

?>