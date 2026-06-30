<?php
include("conexion.php");

$total = db_query($conn,"SELECT COUNT(*) as total FROM cuentas");
$datos = db_fetch_assoc($total);
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Gestión de Cuentas</title>

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

input,select{
    width:100%;
    padding:14px;
    border:1px solid #f9a8d4;
    border-radius:12px;
    font-family:'Segoe UI',sans-serif;
    font-size:15px;
    background:white;
}

input:focus,select:focus{
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
    border:none;
    cursor:pointer;
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

.badge{
    padding:6px 12px;
    border-radius:20px;
    font-size:13px;
    font-weight:600;
    color:white;
}

.badge-activa{
    background:#ec4899;
}

.badge-inactiva{
    background:#dc2626;
}

.badge-suspendida{
    background:#db2777;
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
    <h1>💳 Gestión de Cuentas</h1>
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
    <h3>Total de Cuentas</h3>
    <p><?php echo $datos['total']; ?></p>
</div>

<div class="card">

<h2>➕ Registrar Cuenta</h2>

<form action="guardar_cuenta.php" method="POST" class="form-grid">

    <select name="id_cliente" required>
        <option value="" disabled selected>Seleccione un cliente</option>
        <?php
        $clientesQuery = db_query($conn,"SELECT id_cliente, cedula, nombres, apellidos FROM clientes ORDER BY nombres ASC");
        while($cli = db_fetch_assoc($clientesQuery))
        {
            echo "<option value='{$cli['id_cliente']}'>{$cli['nombres']} {$cli['apellidos']} - {$cli['cedula']}</option>";
        }
        ?>
    </select>

    <input
        type="text"
        name="numero_cuenta"
        placeholder="Número de Cuenta"
        required>

    <select name="tipo_cuenta" required>
        <option value="" disabled selected>Tipo de Cuenta</option>
        <option value="Ahorros">Ahorros</option>
        <option value="Corriente">Corriente</option>
    </select>

    <input
        type="date"
        name="fecha_apertura"
        required>

    <input
        type="number"
        name="saldo"
        placeholder="Saldo Inicial"
        step="0.01"
        min="0"
        required>

    <select name="estado" required>
        <option value="" disabled selected>Estado</option>
        <option value="Activa">Activa</option>
        <option value="Inactiva">Inactiva</option>
        <option value="Suspendida">Suspendida</option>
    </select>

    <button type="submit">
        Guardar Cuenta
    </button>

</form>

</div>

<div class="card">

<h2>📋 Lista de Cuentas</h2>
<div class="barra-busqueda">

    <form method="GET" style="display:flex; gap:12px; flex:1;">

        <input
            type="text"
            name="buscar"
            placeholder="Buscar por número de cuenta, cliente o cédula">

        <button type="submit">
            🔍 Buscar
        </button>

    </form>

    <a href="cuentas.php" class="btn-mostrar">
        🔄 Mostrar Todas
    </a>

</div>

<br>
<div class="tabla">

<table>

<thead>
<tr>
    <th>ID</th>
    <th>N° Cuenta</th>
    <th>Tipo</th>
    <th>Cliente</th>
    <th>Cédula</th>
    <th>Fecha Apertura</th>
    <th>Saldo</th>
    <th>Estado</th>
    <th>Acciones</th>
</tr>
</thead>

<tbody>

<?php

if(isset($_GET['buscar']))
{
    $buscar = db_real_escape_string($conn,$_GET['buscar']);

    $sql = "
    SELECT cu.*, cl.nombres, cl.apellidos, cl.cedula
    FROM cuentas cu
    INNER JOIN clientes cl ON cu.id_cliente = cl.id_cliente
    WHERE cu.numero_cuenta LIKE '%$buscar%'
    OR cl.nombres LIKE '%$buscar%'
    OR cl.apellidos LIKE '%$buscar%'
    OR cl.cedula LIKE '%$buscar%'
    ORDER BY cu.id_cuenta DESC
    ";
}
else
{
    $sql = "
    SELECT cu.*, cl.nombres, cl.apellidos, cl.cedula
    FROM cuentas cu
    INNER JOIN clientes cl ON cu.id_cliente = cl.id_cliente
    ORDER BY cu.id_cuenta DESC
    ";
}

$resultado = db_query($conn,$sql);

while($fila = db_fetch_assoc($resultado))
{
    $estadoClass = "badge-activa";
    if($fila['estado'] == "Inactiva") $estadoClass = "badge-inactiva";
    if($fila['estado'] == "Suspendida") $estadoClass = "badge-suspendida";

    $saldoFormateado = number_format($fila['saldo'],2);

    echo "
    <tr>
        <td>{$fila['id_cuenta']}</td>
        <td>{$fila['numero_cuenta']}</td>
        <td>{$fila['tipo_cuenta']}</td>
        <td>{$fila['nombres']} {$fila['apellidos']}</td>
        <td>{$fila['cedula']}</td>
        <td>{$fila['fecha_apertura']}</td>
        <td>\${$saldoFormateado}</td>
        <td><span class='badge {$estadoClass}'>{$fila['estado']}</span></td>

        <td>

<a class='btn-editar'
onclick=\"abrirModal(
'{$fila['id_cuenta']}',
'{$fila['id_cliente']}',
'{$fila['numero_cuenta']}',
'{$fila['tipo_cuenta']}',
'{$fila['fecha_apertura']}',
'{$fila['saldo']}',
'{$fila['estado']}'
)\">
✏️ Editar
</a>

<a class='btn-eliminar'
   href='eliminar_cuenta.php?id={$fila['id_cuenta']}'
   onclick='return confirm(\"¿Eliminar esta cuenta?\")'>
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

        <h2>✏️ Editar Cuenta</h2>

        <form action="actualizar_cuenta.php" method="POST" class="form-grid">

            <input type="hidden" name="id_cuenta" id="edit_id">

            <select name="id_cliente" id="edit_id_cliente" required>
                <?php
                $clientesQuery2 = db_query($conn,"SELECT id_cliente, cedula, nombres, apellidos FROM clientes ORDER BY nombres ASC");
                while($cli2 = db_fetch_assoc($clientesQuery2))
                {
                    echo "<option value='{$cli2['id_cliente']}'>{$cli2['nombres']} {$cli2['apellidos']} - {$cli2['cedula']}</option>";
                }
                ?>
            </select>

            <input type="text" name="numero_cuenta" id="edit_numero_cuenta" required>

            <select name="tipo_cuenta" id="edit_tipo_cuenta" required>
                <option value="Ahorros">Ahorros</option>
                <option value="Corriente">Corriente</option>
            </select>

            <input type="date" name="fecha_apertura" id="edit_fecha_apertura" required>

            <input type="number" name="saldo" id="edit_saldo" step="0.01" min="0" required>

            <select name="estado" id="edit_estado" required>
                <option value="Activa">Activa</option>
                <option value="Inactiva">Inactiva</option>
                <option value="Suspendida">Suspendida</option>
            </select>

            <button type="submit">
                💾 Guardar Cambios
            </button>

        </form>

    </div>

</div>

<script>

function abrirModal(id,id_cliente,numero_cuenta,tipo_cuenta,fecha_apertura,saldo,estado)
{
    document.getElementById("edit_id").value=id;
    document.getElementById("edit_id_cliente").value=id_cliente;
    document.getElementById("edit_numero_cuenta").value=numero_cuenta;
    document.getElementById("edit_tipo_cuenta").value=tipo_cuenta;
    document.getElementById("edit_fecha_apertura").value=fecha_apertura;
    document.getElementById("edit_saldo").value=saldo;
    document.getElementById("edit_estado").value=estado;

    document.getElementById("modalEditar").style.display="flex";
}

function cerrarModal()
{
    document.getElementById("modalEditar").style.display="none";
}

</script>
</html>