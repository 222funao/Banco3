<?php
session_start();
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'Administrador') {
    header("Location: login.php");
    exit();
}

include("conexion.php");

if(isset($_GET['id'])) {
    $id = db_real_escape_string($conn, $_GET['id']);
    
    // Control de seguridad para no eliminarse a sí mismo
    if($id == $_SESSION['id_usuario']){
        die("Error: No puedes eliminar tu propio usuario mientras estás conectado.");
    }

    $sql = "DELETE FROM usuarios WHERE id_usuario = '$id'";
    if(db_query($conn, $sql)) {
        header("Location: usuarios.php?msg=Usuario eliminado correctamente del sistema");
        exit();
    } else {
        echo "Error al eliminar usuario: " . db_error($conn);
    }
}
?>