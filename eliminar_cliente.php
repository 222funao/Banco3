<?php

include("conexion.php");

$id = $_GET['id'];

db_query(
    $conn,
    "DELETE FROM clientes WHERE id_cliente='$id'"
);

header("Location: clientes.php");

?>