<?php

include("conexion.php");

$total = db_query($conn,"SELECT COUNT(*) as total FROM clientes");
$datos = db_fetch_assoc($total);
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Gestión de Clientes</title>

<style>

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:'Segoe UI',sans-serif;
}

body{
    min-height:100vh;
    background:linear-gradient(135deg,#fbcfe8,#f9a8d4,#ec4899);
}

header{
    background:rgba(131,24,67,.95);
    padding:25px;
    text-align:center;
    color:white;
    box-shadow:0 5px 20px rgba(0,0,0,.3);
}

header h1{
    font-size:2.3rem;
}

nav{
    display:flex;
    justify-content:center;
    flex-wrap:wrap;
    gap:15px;
    padding:18px;
    background:rgba(255,255,255,.08);
}

nav a{
    color:white;
    text-decoration:none;
    padding:12px 20px;
    border-radius:12px;
    transition:.3s;
    font-weight:600;
}

nav a:hover{
    background:rgba(255,255,255,.15);
}

.container{
    width:90%;
    max-width:1300px;
    margin:40px auto;
}

.stat{
    background:white;
    border-radius:20px;
    padding:25px;
    text-align:center;
    margin-bottom:25px;
    box-shadow:0 10px 25px rgba(0,0,0,.2);
}

.stat h3{
    color:#9d174d;
}

.stat p{
    font-size:40px;
    color:#ec4899;
    font-weight:bold;
}

.card{
    background:white;
    border-radius:20px;
    padding:30px;
    margin-bottom:25px;
    box-shadow:0 10px 30px rgba(0,0,0,.2);
}

.card h2{
    color:#be185d;
    margin-bottom:20px;
}

.form-grid{
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(250px,1fr));
    gap:15px;
}

input{
    width:100%;
    padding:14px;
    border:1px solid #f9a8d4;
    border-radius:12px;
}

input:focus{
    outline:none;
    border-color:#ec4899;
}

button{
    background:#ec4899;
    color:white;
    border:none;
    padding:14px;
    border-radius:12px;
    cursor:pointer;
    font-weight:bold;
    transition:.3s;
}

button:hover{
    background:#db2777;
}

.tabla{
    overflow-x:auto;
}

table{
    width:100%;
    border-collapse:collapse;
}

thead{
    background:#ec4899;
    color:white;
}

th,td{
    padding:15px;
    text-align:center;
}

tbody tr:nth-child(even){
    background:#fdf2f8;
}

tbody tr:hover{
    background:#fbcfe8;
}

.btn-editar{
    background:#ec4899;
    color:white;
    text-decoration:none;
    padding:8px 14px;
    border-radius:10px;
    font-size:14px;
    font-weight:600;
    display:inline-block;
    margin-right:5px;
    transition:.3s;
}

.btn-editar:hover{
    background:#db2777;
    transform:translateY(-2px);
}

.btn-eliminar{
    background:#dc2626;
    color:white;
    text-decoration:none;
    padding:8px 14px;
    border-radius:10px;
    font-size:14px;
    font-weight:600;
    display:inline-block;
    transition:.3s;
}

.btn-eliminar:hover{
    background:#b91c1c;
    transform:translateY(-2px);
}

.modal{
    display:none;
    position:fixed;
    top:0;
    left:0;
    width:100%;
    height:100%;
    background:rgba(0,0,0,.6);
    justify-content:center;
    align-items:center;
    z-index:9999;
}

.modal-content{
    background:white;
    width:90%;
    max-width:700px;
    padding:30px;
    border-radius:20px;
    box-shadow:0 15px 40px rgba(0,0,0,.3);
}

.cerrar{
    float:right;
    cursor:pointer;
    font-size:28px;
    font-weight:bold;
    color:#dc2626;
}

.barra-busqueda{
    display:flex;
    gap:12px;
    align-items:center;
    margin-bottom:20px;
    flex-wrap:wrap;
}

.barra-busqueda input{
    flex:1;
    min-width:250px;
}

.btn-mostrar{
    background:#ec4899;
    color:white;
    text-decoration:none;
    padding:14px 18px;
    border-radius:12px;
    font-weight:bold;
    transition:.3s;
}

.btn-mostrar:hover{
    background:#db2777;
    transform:translateY(-2px);
}

</style>

</head>
<body>

<header>
    <h1>👤 Gestión de Clientes</h1>
</header>

<nav>
    <a href="index.php">🏠 Inicio</a>
    <a href="clientes.php">👤 Clientes</a>
    <a href="cuentas.php">💳 Cuentas</a>
    <a href="transacciones.php">💸 Transacciones</a>
    <a href="prestamos.php">📄 Préstamos</a>
    <a href="reportes.php">📊 Reportes</a>
</nav>

<div class="container">

<div class="stat">
    <h3>Total de Clientes</h3>
    <p><?php echo $datos['total']; ?></p>
</div>

<div class="card">

<h2>➕ Registrar Cliente</h2>

<form action="guardar_cliente.php" method="POST" class="form-grid">

    <input
        type="text"
        name="cedula"
        placeholder="Cédula (10 dígitos)"
        maxlength="10"
        oninput="this.value=this.value.replace(/[^0-9]/g,'')"
        required>

    <input
        type="text"
        name="nombres"
        placeholder="Nombres"
        required>

    <input
        type="text"
        name="apellidos"
        placeholder="Apellidos"
        required>

    <input
        type="date"
        name="fecha_nacimiento"
        required>

    <input
        type="text"
        name="direccion"
        placeholder="Dirección">

    <input
        type="text"
        name="telefono"
        placeholder="Teléfono"
        maxlength="10"
        oninput="this.value=this.value.replace(/[^0-9]/g,'')">

    <input
        type="email"
        name="correo"
        placeholder="Correo Electrónico"
        required>

    <button type="submit">
        Guardar Cliente
    </button>

</form>

</div>

<div class="card">

<h2>📋 Lista de Clientes</h2>
<div class="barra-busqueda">

    <form method="GET" style="display:flex; gap:12px; flex:1;">

        <input
            type="text"
            name="buscar"
            placeholder="Buscar por nombre, apellido o cédula">

        <button type="submit">
            🔍 Buscar
        </button>

    </form>

    <a href="clientes.php" class="btn-mostrar">
        🔄 Mostrar Todos
    </a>

</div>

<br>
<div class="tabla">

<table>

<thead>
<tr>
    <th>ID</th>
    <th>Cédula</th>
    <th>Nombres</th>
    <th>Apellidos</th>
    <th>Teléfono</th>
    <th>Correo</th>
	<th>Acciones</th>
</tr>
</thead>

<tbody>

<?php

if(isset($_GET['buscar']))
{
    $buscar = $_GET['buscar'];

    $sql = "
    SELECT *
    FROM clientes
    WHERE nombres LIKE '%$buscar%'
    OR apellidos LIKE '%$buscar%'
    OR cedula LIKE '%$buscar%'
    ORDER BY id_cliente DESC
    ";
}
else
{
    $sql = "
    SELECT *
    FROM clientes
    ORDER BY id_cliente DESC
    ";
}$resultado = db_query($conn,$sql);

while($fila = db_fetch_assoc($resultado))
{
    echo "
    <tr>
        <td>{$fila['id_cliente']}</td>
        <td>{$fila['cedula']}</td>
        <td>{$fila['nombres']}</td>
        <td>{$fila['apellidos']}</td>
        <td>{$fila['telefono']}</td>
        <td>{$fila['correo']}</td>

        <td>

<a class='btn-editar'
onclick=\"abrirModal(
'{$fila['id_cliente']}',
'{$fila['cedula']}',
'{$fila['nombres']}',
'{$fila['apellidos']}',
'{$fila['direccion']}',
'{$fila['telefono']}',
'{$fila['correo']}'
)\">
✏️ Editar
</a>

<a class='btn-eliminar'
   href='eliminar_cliente.php?id={$fila['id_cliente']}'
   onclick='return confirm(\"¿Eliminar cliente?\")'>
   🗑️ Eliminar
</a>

</td>

    </tr>";
}

?>

</tbody>

</table>

</div>

</div>

</div>

</body>
<div class="modal" id="modalEditar">

    <div class="modal-content">

        <span class="cerrar" onclick="cerrarModal()">&times;</span>

        <h2>✏️ Editar Cliente</h2>

        <form action="actualizar_cliente.php" method="POST" class="form-grid">

            <input type="hidden" name="id_cliente" id="edit_id">

            <input type="text" name="cedula" id="edit_cedula" required>

            <input type="text" name="nombres" id="edit_nombres" required>

            <input type="text" name="apellidos" id="edit_apellidos" required>

            <input type="text" name="direccion" id="edit_direccion">

            <input type="text" name="telefono" id="edit_telefono">

            <input type="email" name="correo" id="edit_correo">

            <button type="submit">
                💾 Guardar Cambios
            </button>

        </form>

    </div>

</div>

<script>

function abrirModal(id,cedula,nombres,apellidos,direccion,telefono,correo)
{
    document.getElementById("edit_id").value=id;
    document.getElementById("edit_cedula").value=cedula;
    document.getElementById("edit_nombres").value=nombres;
    document.getElementById("edit_apellidos").value=apellidos;
    document.getElementById("edit_direccion").value=direccion;
    document.getElementById("edit_telefono").value=telefono;
    document.getElementById("edit_correo").value=correo;

    document.getElementById("modalEditar").style.display="flex";
}

function cerrarModal()
{
    document.getElementById("modalEditar").style.display="none";
}

</script>    
</html>