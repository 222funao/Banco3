<?php

include("conexion.php");

$cedula = trim($_POST['cedula']);
$nombres = trim($_POST['nombres']);
$apellidos = trim($_POST['apellidos']);
$fecha_nacimiento = $_POST['fecha_nacimiento'];
$direccion = trim($_POST['direccion']);
$telefono = trim($_POST['telefono']);
$correo = trim($_POST['correo']);

if(strlen($cedula) != 10){
    die("La cédula debe tener 10 dígitos.");
}

$verificar = db_query(
    $conn,
    "SELECT id_cliente FROM clientes WHERE cedula='$cedula'"
);

if(db_num_rows($verificar) > 0){
    die("Ya existe un cliente con esa cédula.");
}

// 1. Insertar primero en la tabla clientes
$sql = "INSERT INTO clientes
(
    cedula,
    nombres,
    apellidos,
    fecha_nacimiento,
    direccion,
    telefono,
    correo
)
VALUES
(
    '$cedula',
    '$nombres',
    '$apellidos',
    '$fecha_nacimiento',
    '$direccion',
    '$telefono',
    '$correo'
)";

if(db_query($conn, $sql)){

    $sql_usuario = "INSERT INTO usuarios (usuario, contraseña, rol) 
                    VALUES ('$cedula', '$cedula', 'Cliente')";
    
    if(db_query($conn, $sql_usuario)){
        header("Location: clientes.php?msg=Cliente y usuario creados con éxito");
        exit();
    } else {
        echo "El cliente se registró, pero hubo un error al crear su cuenta de usuario: " . db_error($conn);
    }

}else{

    echo "Error al guardar cliente: " . db_error($conn);

}

?>