<?php
include("conexion.php");

$total = db_query($conn,"SELECT COUNT(*) as total FROM transacciones");
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
<title>Gestión de Transacciones</title>

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

.btn-retiro{
    background:#dc2626;
}

.btn-retiro:hover{
    background:#b91c1c;
}

.btn-transferencia{
    background:#c026d3;
}

.btn-transferencia:hover{
    background:#a21caf;
}

.tabs{
    display:flex;
    gap:10px;
    margin-bottom:25px;
    flex-wrap:wrap;
}

.tab-btn{
    background:#fce7f3;
    color:#9d174d;
    border:none;
    padding:12px 20px;
    border-radius:12px;
    cursor:pointer;
    font-weight:600;
    transition:.3s;
}

.tab-btn.activo{
    background:#ec4899;
    color:white;
}

.tab-contenido{
    display:none;
}

.tab-contenido.activo{
    display:block;
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

.badge{
    padding:6px 12px;
    border-radius:20px;
    font-size:13px;
    font-weight:600;
    color:white;
    display:inline-block;
}

.badge-deposito{
    background:#ec4899;
}

.badge-retiro{
    background:#dc2626;
}

.badge-trans-env{
    background:#f472b6;
}

.badge-trans-rec{
    background:#c026d3;
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

</style>

</head>
<body>

<header>
    <h1>💸 Gestión de Transacciones</h1>
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
    <h3>Total de Transacciones</h3>
    <p><?php echo $datos['total']; ?></p>
</div>

<?php
if($mensaje == "ok")
{
    echo "<div class='alerta alerta-ok'>✅ Transacción registrada correctamente.</div>";
}
elseif($mensaje == "saldo_insuficiente")
{
    echo "<div class='alerta alerta-error'>❌ Saldo insuficiente para realizar esta operación.</div>";
}
elseif($mensaje == "cuenta_invalida")
{
    echo "<div class='alerta alerta-error'>❌ La cuenta indicada no existe.</div>";
}
elseif($mensaje == "misma_cuenta")
{
    echo "<div class='alerta alerta-error'>❌ La cuenta origen y destino no pueden ser la misma.</div>";
}
elseif($mensaje == "error")
{
    echo "<div class='alerta alerta-error'>❌ Ocurrió un error al procesar la transacción.</div>";
}
?>

<div class="card">

<h2>➕ Registrar Transacción</h2>

<div class="tabs">
    <button type="button" class="tab-btn activo" onclick="mostrarTab('deposito')">⬆️ Depósito</button>
    <button type="button" class="tab-btn" onclick="mostrarTab('retiro')">⬇️ Retiro</button>
    <button type="button" class="tab-btn" onclick="mostrarTab('transferencia')">🔁 Transferencia</button>
</div>

<div class="tab-contenido activo" id="tab-deposito">

    <form action="guardar_deposito.php" method="POST" class="form-grid">

        <select name="id_cuenta" required>
            <option value="" disabled selected>Seleccione cuenta</option>
            <?php
            $cuentasQuery = db_query($conn,"SELECT id_cuenta, numero_cuenta FROM cuentas ORDER BY numero_cuenta ASC");
            while($c = db_fetch_assoc($cuentasQuery))
            {
                echo "<option value='{$c['id_cuenta']}'>{$c['numero_cuenta']}</option>";
            }
            ?>
        </select>

        <input
            type="number"
            name="monto"
            placeholder="Valor a depositar"
            step="0.01"
            min="0.01"
            required>

        <input
            type="date"
            name="fecha"
            required>

        <button type="submit">
            Registrar Depósito
        </button>

    </form>

</div>

<div class="tab-contenido" id="tab-retiro">

    <form action="guardar_retiro.php" method="POST" class="form-grid">

        <select name="id_cuenta" required>
            <option value="" disabled selected>Seleccione cuenta</option>
            <?php
            $cuentasQuery2 = db_query($conn,"SELECT id_cuenta, numero_cuenta, saldo FROM cuentas ORDER BY numero_cuenta ASC");
            while($c2 = db_fetch_assoc($cuentasQuery2))
            {
                echo "<option value='{$c2['id_cuenta']}'>{$c2['numero_cuenta']} (Saldo: \${$c2['saldo']})</option>";
            }
            ?>
        </select>

        <input
            type="number"
            name="monto"
            placeholder="Valor a retirar"
            step="0.01"
            min="0.01"
            required>

        <input
            type="date"
            name="fecha"
            required>

        <button type="submit" class="btn-retiro">
            Registrar Retiro
        </button>

    </form>

</div>

<div class="tab-contenido" id="tab-transferencia">

    <form action="guardar_transferencia.php" method="POST" class="form-grid">

        <select name="id_cuenta_origen" required>
            <option value="" disabled selected>Cuenta Origen</option>
            <?php
            $cuentasQuery3 = db_query($conn,"SELECT id_cuenta, numero_cuenta, saldo FROM cuentas ORDER BY numero_cuenta ASC");
            while($c3 = db_fetch_assoc($cuentasQuery3))
            {
                echo "<option value='{$c3['id_cuenta']}'>{$c3['numero_cuenta']} (Saldo: \${$c3['saldo']})</option>";
            }
            ?>
        </select>

        <select name="id_cuenta_destino" required>
            <option value="" disabled selected>Cuenta Destino</option>
            <?php
            $cuentasQuery4 = db_query($conn,"SELECT id_cuenta, numero_cuenta FROM cuentas ORDER BY numero_cuenta ASC");
            while($c4 = db_fetch_assoc($cuentasQuery4))
            {
                echo "<option value='{$c4['id_cuenta']}'>{$c4['numero_cuenta']}</option>";
            }
            ?>
        </select>

        <input
            type="number"
            name="monto"
            placeholder="Valor a transferir"
            step="0.01"
            min="0.01"
            required>

        <input
            type="date"
            name="fecha"
            required>

        <button type="submit" class="btn-transferencia">
            Registrar Transferencia
        </button>

    </form>

</div>

</div>

<div class="card">

<h2>📋 Historial de Transacciones</h2>

<div class="barra-busqueda">

    <form method="GET" style="display:flex; gap:12px; flex:1;">

        <input
            type="text"
            name="buscar"
            placeholder="Buscar por número de cuenta o tipo">

        <button type="submit">
            🔍 Buscar
        </button>

    </form>

    <a href="transacciones.php" class="btn-mostrar">
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
    <th>Monto</th>
    <th>Fecha</th>
</tr>
</thead>

<tbody>

<?php

if(isset($_GET['buscar']))
{
    $buscar = db_real_escape_string($conn,$_GET['buscar']);

    $sql = "
    SELECT t.*, cu.numero_cuenta
    FROM transacciones t
    INNER JOIN cuentas cu ON t.id_cuenta = cu.id_cuenta
    WHERE cu.numero_cuenta LIKE '%$buscar%'
    OR t.tipo LIKE '%$buscar%'
    ORDER BY t.fecha DESC, t.id_transaccion DESC
    ";
}
else
{
    $sql = "
    SELECT t.*, cu.numero_cuenta
    FROM transacciones t
    INNER JOIN cuentas cu ON t.id_cuenta = cu.id_cuenta
    ORDER BY t.fecha DESC, t.id_transaccion DESC
    ";
}

$resultado = db_query($conn,$sql);

while($fila = db_fetch_assoc($resultado))
{
    $claseBadge = "badge-deposito";
    if($fila['tipo'] == "Retiro") $claseBadge = "badge-retiro";
    if($fila['tipo'] == "Transferencia Enviada") $claseBadge = "badge-trans-env";
    if($fila['tipo'] == "Transferencia Recibida") $claseBadge = "badge-trans-rec";

    $montoFormateado = number_format($fila['monto'],2);

    echo "
    <tr>
        <td>{$fila['id_transaccion']}</td>
        <td>{$fila['numero_cuenta']}</td>
        <td><span class='badge {$claseBadge}'>{$fila['tipo']}</span></td>
        <td>\${$montoFormateado}</td>
        <td>{$fila['fecha']}</td>
    </tr>";
}

?>

</tbody>

</table>

</div>

</div>

</div>

<script>

function mostrarTab(nombre)
{
    document.querySelectorAll(".tab-contenido").forEach(function(el){
        el.classList.remove("activo");
    });

    document.querySelectorAll(".tab-btn").forEach(function(el){
        el.classList.remove("activo");
    });

    document.getElementById("tab-" + nombre).classList.add("activo");
    event.target.classList.add("activo");
}

</script>

</body>
</html>