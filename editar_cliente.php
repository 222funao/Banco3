<?php

include("conexion.php");

$id = $_GET['id'];

$sql = db_query(
    $conn,
    "SELECT * FROM clientes WHERE id_cliente='$id'"
);

$cliente = db_fetch_assoc($sql);

?>

<!DOCTYPE html>
<html>
<head>
<title>Editar Cliente</title>
</head>
<body>

<h2>Editar Cliente</h2>

<form action="actualizar_cliente.php" method="POST">

<input type="hidden"
       name="id_cliente"
       value="<?php echo $cliente['id_cliente']; ?>">

<input type="text"
       name="cedula"
       value="<?php echo $cliente['cedula']; ?>">

<input type="text"
       name="nombres"
       value="<?php echo $cliente['nombres']; ?>">

<input type="text"
       name="apellidos"
       value="<?php echo $cliente['apellidos']; ?>">

<input type="text"
       name="direccion"
       value="<?php echo $cliente['direccion']; ?>">

<input type="text"
       name="telefono"
       value="<?php echo $cliente['telefono']; ?>">

<input type="email"
       name="correo"
       value="<?php echo $cliente['correo']; ?>">

<button type="submit">
Actualizar Cliente
</button>

</form>

</body>
</html>