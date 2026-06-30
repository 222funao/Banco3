<?php
include("conexion.php");

$q_clientes = db_query($conn, "SELECT COUNT(*) as total FROM clientes");
$d_clientes = db_fetch_assoc($q_clientes);

$q_cuentas = db_query($conn, "SELECT COUNT(*) as total FROM cuentas WHERE estado = 'Activa'");
$d_cuentas = db_fetch_assoc($q_cuentas);

$q_depositos = db_query($conn, "SELECT SUM(monto) as total FROM transacciones WHERE tipo LIKE 'deposito%' OR tipo LIKE 'depósito%'");
$d_depositos = db_fetch_assoc($q_depositos);
$total_depositos = $d_depositos['total'] ?? 0;

$q_retiros = db_query($conn, "SELECT SUM(monto) as total FROM transacciones WHERE tipo LIKE 'retiro%'");
$d_retiros = db_fetch_assoc($q_retiros);
$total_retiros = $d_retiros['total'] ?? 0;

// 5. Total de préstamos otorgados
$q_prestamos = db_query($conn, "SELECT SUM(monto) as total FROM prestamos");
$d_prestamos = db_fetch_assoc($q_prestamos);
$total_prestamos = $d_prestamos['total'] ?? 0;
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Reportes Financieros</title>

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

.form-grid{
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(220px,1fr));
    gap:20px;
    margin-bottom:25px;
}

.stat{
    background:white;
    border-radius:20px;
    padding:20px;
    text-align:center;
    box-shadow:0 10px 25px rgba(0,0,0,.2);
}

.stat h3{
    color:#9d174d;
    font-size:14px;
    text-transform:uppercase;
    margin-bottom:5px;
}

.stat p{
    font-size:28px;
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
}

.badge-deposito{
    background:#ec4899;
}

.badge-retiro{
    background:#db2777;
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
    padding:14px;
    border:1px solid #f9a8d4;
    border-radius:12px;
}

.barra-busqueda input:focus{
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
    <h1>📊 Panel de Reportes Financieros</h1>
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

    <div class="form-grid">
        <div class="stat">
            <h3>Clientes Registrados</h3>
            <p><?php echo $d_clientes['total']; ?></p>
        </div>
        <div class="stat">
            <h3>Cuentas Activas</h3>
            <p><?php echo $d_cuentas['total']; ?></p>
        </div>
        <div class="stat">
            <h3>Total Depósitos</h3>
            <p>$<?php echo number_format($total_depositos, 2); ?></p>
        </div>
        <div class="stat">
            <h3>Total Retiros</h3>
            <p>$<?php echo number_format($total_retiros, 2); ?></p>
        </div>
        <div class="stat">
            <h3>Total en Préstamos</h3>
            <p>$<?php echo number_format($total_prestamos, 2); ?></p>
        </div>
    </div>

    <div class="card">
        <h2>👑 Clientes con Mayor Saldo (Top 5)</h2>
        <div class="tabla">
            <table>
                <thead>
                    <tr>
                        <th>Puesto</th>
                        <th>Cédula</th>
                        <th>Cliente</th>
                        <th>Saldo Total Acumulado</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sql_mayores = "
                        SELECT cl.cedula, cl.nombres, cl.apellidos, SUM(cu.saldo) as saldo_total
                        FROM clientes cl
                        INNER JOIN cuentas cu ON cl.id_cliente = cu.id_cliente
                        GROUP BY cl.id_cliente, cl.cedula, cl.nombres, cl.apellidos
                        ORDER BY saldo_total DESC
                        LIMIT 5
                    ";
                    $res_mayores = db_query($conn, $sql_mayores);
                    $puesto = 1;
                    
                    if(db_num_rows($res_mayores) > 0) {
                        while($m = db_fetch_assoc($res_mayores)) {
                            $saldo_f = number_format($m['saldo_total'], 2);
                            echo "
                            <tr>
                                <td><b>#{$puesto}</b></td>
                                <td>{$m['cedula']}</td>
                                <td>{$m['nombres']} {$m['apellidos']}</td>
                                <td style='color: #16a34a; font-weight: bold;'>\${$saldo_f}</td>
                            </tr>";
                            $puesto++;
                        }
                    } else {
                        echo "<tr><td colspan='4'>No hay cuentas registradas con saldo comercial.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="card">
        <h2>📜 Historial General de Movimientos</h2>
        
        <div class="barra-busqueda">
            <form method="GET" style="display:flex; gap:12px; flex:1;">
                <input
                    type="text"
                    name="buscar"
                    placeholder="Filtrar por N° Cuenta, Cliente, Cédula o Tipo (Deposito/Retiro)"
                    value="<?php echo isset($_GET['buscar']) ? htmlspecialchars($_GET['buscar']) : ''; ?>">
                <button type="submit">🔍 Filtrar</button>
            </form>
            <a href="reportes.php" class="btn-mostrar">🔄 Restablecer</a>
        </div>

        <div class="tabla">
            <table>
                <thead>
                    <tr>
                        <th>ID Trans.</th>
                        <th>N° Cuenta</th>
                        <th>Cliente</th>
                        <th>Cédula</th>
                        <th>Tipo</th>
                        <th>Monto</th>
                        <th>Fecha</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if(isset($_GET['buscar']) && !empty($_GET['buscar'])) {
                        $buscar = db_real_escape_string($conn, $_GET['buscar']);
                        $sql_movimientos = "
                            SELECT t.*, cu.numero_cuenta, cl.nombres, cl.apellidos, cl.cedula
                            FROM transacciones t
                            INNER JOIN cuentas cu ON t.id_cuenta = cu.id_cuenta
                            INNER JOIN clientes cl ON cu.id_cliente = cl.id_cliente
                            WHERE cu.numero_cuenta LIKE '%$buscar%'
                            OR cl.nombres LIKE '%$buscar%'
                            OR cl.apellidos LIKE '%$buscar%'
                            OR cl.cedula LIKE '%$buscar%'
                            OR t.tipo LIKE '%$buscar%'
                            ORDER BY t.id_transaccion DESC
                        ";
                    } else {
                        $sql_movimientos = "
                            SELECT t.*, cu.numero_cuenta, cl.nombres, cl.apellidos, cl.cedula
                            FROM transacciones t
                            INNER JOIN cuentas cu ON t.id_cuenta = cu.id_cuenta
                            INNER JOIN clientes cl ON cu.id_cliente = cl.id_cliente
                            ORDER BY t.id_transaccion DESC
                            LIMIT 30
                        ";
                    }

                    $res_movimientos = db_query($conn, $sql_movimientos);

                    if(db_num_rows($res_movimientos) > 0) {
                        while($mov = db_fetch_assoc($res_movimientos)) {
                            // Detecta si es deposito o retiro de manera insensible a mayúsculas/minúsculas
                            $tipo_str = strtolower($mov['tipo']);
                            $clase_badge = (strpos($tipo_str, 'depo') !== false) ? 'badge-deposito' : 'badge-retiro';
                            $monto_mov_f = number_format($mov['monto'], 2);
                            
                            echo "
                            <tr>
                                <td>{$mov['id_transaccion']}</td>
                                <td><b>{$mov['numero_cuenta']}</b></td>
                                <td>{$mov['nombres']} {$mov['apellidos']}</td>
                                <td>{$mov['cedula']}</td>
                                <td><span class='badge {$clase_badge}'>{$mov['tipo']}</span></td>
                                <td style='font-weight: 600;'>\${$monto_mov_f}</td>
                                <td>{$mov['fecha']}</td>
                            </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='7'>No se encontraron movimientos registrados.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

</body>
</html>