<?php

include("conexion.php");

$total = db_query($conn,"SELECT COUNT(*) as total FROM prestamos");
$datos = db_fetch_assoc($total);

$mensaje = "";
if(isset($_GET['msg']))
{
    $mensaje = $_GET['msg'];
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Gestión de Préstamos</title>

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
    display:inline-block;
}

.badge-pendiente{
    background:#f472b6;
}

.badge-aprobado{
    background:#ec4899;
}

.badge-rechazado{
    background:#dc2626;
}

.badge-pagado{
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
    max-width:600px;
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

.alerta{
    padding:16px 20px;
    border-radius:12px;
    margin-bottom:20px;
    font-weight:600;
}

.alerta-ok{
    background:#fce7f3;
    color:#9d174d;
    border:1px solid #ec4899;
}

.alerta-error{
    background:#fee2e2;
    color:#991b1b;
    border:1px solid #dc2626;
}

.aviso-cuenta{
    background:#fdf2f8;
    color:#9d174d;
    border:1px solid #ec4899;
    padding:12px 16px;
    border-radius:10px;
    font-size:14px;
    margin-top:6px;
}

</style>

</head>
<body>

<header>
    <h1>📄 Gestión de Préstamos</h1>
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
    <h3>Total de Préstamos</h3>
    <p><?php echo $datos['total']; ?></p>
</div>

<?php
if($mensaje == "ok")
{
    echo "<div class='alerta alerta-ok'>✅ Préstamo registrado correctamente.</div>";
}
elseif($mensaje == "aprobado")
{
    echo "<div class='alerta alerta-ok'>✅ Préstamo aprobado y desembolsado correctamente.</div>";
}
elseif($mensaje == "actualizado")
{
    echo "<div class='alerta alerta-ok'>✅ Estado del préstamo actualizado.</div>";
}
elseif($mensaje == "eliminado")
{
    echo "<div class='alerta alerta-ok'>✅ Préstamo eliminado correctamente.</div>";
}
elseif($mensaje == "no_se_puede_eliminar")
{
    echo "<div class='alerta alerta-error'>❌ Solo se pueden eliminar préstamos Pendientes o Rechazados.</div>";
}
elseif($mensaje == "cuenta_requerida")
{
    echo "<div class='alerta alerta-error'>❌ Debe seleccionar una cuenta destino para aprobar el préstamo.</div>";
}
elseif($mensaje == "sin_cuentas")
{
    echo "<div class='alerta alerta-error'>❌ El cliente no tiene cuentas registradas, no se puede desembolsar.</div>";
}
elseif($mensaje == "error")
{
    echo "<div class='alerta alerta-error'>❌ Ocurrió un error al procesar la solicitud.</div>";
}
?>

<div class="card">

<h2>➕ Registrar Préstamo</h2>

<form action="guardar_prestamo.php" method="POST" class="form-grid">

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
        type="number"
        name="monto"
        placeholder="Monto del préstamo"
        step="0.01"
        min="0.01"
        required>

    <input
        type="number"
        name="interes"
        placeholder="Interés (% anual)"
        step="0.01"
        min="0"
        required>

    <input
        type="number"
        name="plazo"
        placeholder="Plazo (meses)"
        min="1"
        required>

    <button type="submit">
        Solicitar Préstamo
    </button>

</form>

</div>

<div class="card">

<h2>📋 Lista de Préstamos</h2>

<div class="barra-busqueda">

    <form method="GET" style="display:flex; gap:12px; flex:1;">

        <input
            type="text"
            name="buscar"
            placeholder="Buscar por nombre, apellido o cédula del cliente">

        <button type="submit">
            🔍 Buscar
        </button>

    </form>

    <a href="prestamos.php" class="btn-mostrar">
        🔄 Mostrar Todos
    </a>

</div>

<br>
<div class="tabla">

<table>

<thead>
<tr>
    <th>ID</th>
    <th>Cliente</th>
    <th>Cédula</th>
    <th>Monto</th>
    <th>Interés</th>
    <th>Plazo</th>
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
    SELECT p.*, cl.nombres, cl.apellidos, cl.cedula
    FROM prestamos p
    INNER JOIN clientes cl ON p.id_cliente = cl.id_cliente
    WHERE cl.nombres LIKE '%$buscar%'
    OR cl.apellidos LIKE '%$buscar%'
    OR cl.cedula LIKE '%$buscar%'
    ORDER BY p.id_prestamo DESC
    ";
}
else
{
    $sql = "
    SELECT p.*, cl.nombres, cl.apellidos, cl.cedula
    FROM prestamos p
    INNER JOIN clientes cl ON p.id_cliente = cl.id_cliente
    ORDER BY p.id_prestamo DESC
    ";
}

$resultado = db_query($conn,$sql);

while($fila = db_fetch_assoc($resultado))
{
    $claseBadge = "badge-pendiente";
    if($fila['estado'] == "Aprobado") $claseBadge = "badge-aprobado";
    if($fila['estado'] == "Rechazado") $claseBadge = "badge-rechazado";
    if($fila['estado'] == "Pagado") $claseBadge = "badge-pagado";

    $montoFormateado = number_format($fila['monto'],2);

    echo "
    <tr>
        <td>{$fila['id_prestamo']}</td>
        <td>{$fila['nombres']} {$fila['apellidos']}</td>
        <td>{$fila['cedula']}</td>
        <td>\${$montoFormateado}</td>
        <td>{$fila['interes']}%</td>
        <td>{$fila['plazo']} meses</td>
        <td><span class='badge {$claseBadge}'>{$fila['estado']}</span></td>

        <td>";

    if($fila['estado'] == "Pendiente")
    {
        echo "
        <a class='btn-editar'
        onclick=\"abrirModal('{$fila['id_prestamo']}','{$fila['id_cliente']}')\">
        ⚙️ Gestionar
        </a>";
    }

    if($fila['estado'] == "Pendiente" || $fila['estado'] == "Rechazado")
    {
        echo "
        <a class='btn-eliminar'
        href='eliminar_prestamo.php?id={$fila['id_prestamo']}'
        onclick='return confirm(\"¿Eliminar este préstamo?\")'>
        🗑️ Eliminar
        </a>";
    }

    echo "
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

        <h2>⚙️ Gestionar Préstamo</h2>

        <form action="actualizar_estado_prestamo.php" method="POST" class="form-grid">

            <input type="hidden" name="id_prestamo" id="edit_id">
            <input type="hidden" name="id_cliente" id="edit_id_cliente">

            <select name="estado" id="edit_estado" required onchange="toggleCuenta()">
                <option value="" disabled selected>Seleccione una acción</option>
                <option value="Aprobado">Aprobar y Desembolsar</option>
                <option value="Rechazado">Rechazar</option>
            </select>

            <div id="contenedor_cuenta" style="display:none;">
                <select name="id_cuenta_destino" id="edit_id_cuenta">
                    <option value="" disabled selected>Cargando cuentas...</option>
                </select>
                <div class="aviso-cuenta">
                    Al aprobar, el monto se acreditará automáticamente a la cuenta seleccionada.
                </div>
            </div>

            <button type="submit">
                💾 Confirmar
            </button>

        </form>

    </div>

</div>

<script>

function abrirModal(id, idCliente)
{
    document.getElementById("edit_id").value = id;
    document.getElementById("edit_id_cliente").value = idCliente;
    document.getElementById("edit_estado").value = "";
    document.getElementById("contenedor_cuenta").style.display = "none";

    document.getElementById("modalEditar").style.display = "flex";
}

function cerrarModal()
{
    document.getElementById("modalEditar").style.display = "none";
}

function toggleCuenta()
{
    const estado = document.getElementById("edit_estado").value;
    const contenedor = document.getElementById("contenedor_cuenta");

    if(estado == "Aprobado")
    {
        contenedor.style.display = "block";
        cargarCuentasCliente();
    }
    else
    {
        contenedor.style.display = "none";
    }
}

function cargarCuentasCliente()
{
    const idCliente = document.getElementById("edit_id_cliente").value;
    const select = document.getElementById("edit_id_cuenta");

    select.innerHTML = "<option value='' disabled selected>Cargando cuentas...</option>";

    fetch("obtener_cuentas_cliente.php?id_cliente=" + idCliente)
        .then(function(response){ return response.json(); })
        .then(function(data){

            select.innerHTML = "";

            if(data.length == 0)
            {
                select.innerHTML = "<option value='' disabled selected>Este cliente no tiene cuentas</option>";
                return;
            }

            const opcionDefault = document.createElement("option");
            opcionDefault.value = "";
            opcionDefault.disabled = true;
            opcionDefault.selected = true;
            opcionDefault.textContent = "Seleccione cuenta destino";
            select.appendChild(opcionDefault);

            data.forEach(function(cuenta){
                const opcion = document.createElement("option");
                opcion.value = cuenta.id_cuenta;
                opcion.textContent = cuenta.numero_cuenta + " (Saldo: $" + cuenta.saldo + ")";
                select.appendChild(opcion);
            });
        })
        .catch(function(){
            select.innerHTML = "<option value='' disabled selected>Error al cargar cuentas</option>";
        });
}

</script>
</html>