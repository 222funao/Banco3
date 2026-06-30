<?php

include("conexion.php");

$clientes = db_fetch_assoc(
    db_query($conn,"SELECT COUNT(*) AS total FROM clientes")
);

$cuentas = db_fetch_assoc(
    db_query($conn,"SELECT COUNT(*) AS total FROM cuentas")
);

$transacciones = db_fetch_assoc(
    db_query($conn,"SELECT COUNT(*) AS total FROM transacciones")
);

$prestamos = db_fetch_assoc(
    db_query($conn,"SELECT COUNT(*) AS total FROM prestamos")
);

?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Banco Digital</title>

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
    font-size:2.5rem;
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
    text-decoration:none;
    color:white;
    padding:12px 20px;
    border-radius:12px;
    transition:.3s;
    font-weight:600;
}

nav a:hover{
    background:rgba(255,255,255,.15);
    transform:translateY(-3px);
}

.hero{
    width:90%;
    max-width:1300px;
    margin:30px auto;
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:40px;
    background:white;
    padding:40px;
    border-radius:25px;
    box-shadow:0 15px 35px rgba(0,0,0,.2);
}

.hero-texto{
    flex:1;
}

.hero-texto h2{
    font-size:3rem;
    color:#be185d;
    margin-bottom:15px;
}

.hero-texto p{
    color:#6b7280;
    font-size:18px;
    line-height:1.8;
    margin-bottom:25px;
}

.btn-hero{
    display:inline-block;
    text-decoration:none;
    background:#ec4899;
    color:white;
    padding:14px 25px;
    border-radius:12px;
    font-weight:bold;
}

.hero-imagen{
    flex:1;
    text-align:center;
}

.hero-imagen img{
    width:100%;
    max-width:450px;
    border-radius:20px;
}

main{
    width:90%;
    max-width:1300px;
    margin:20px auto 40px;
}

.stats{
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(230px,1fr));
    gap:20px;
    margin-bottom:30px;
}

.stat{
    background:white;
    border-radius:20px;
    padding:25px;
    text-align:center;
    box-shadow:0 10px 30px rgba(0,0,0,.2);
    border-top:5px solid #ec4899;
}

.stat h3{
    color:#9d174d;
}

.stat p{
    font-size:40px;
    font-weight:bold;
    color:#ec4899;
}

.card{
    background:white;
    border-radius:20px;
    padding:35px;
    margin-bottom:25px;
    box-shadow:0 10px 30px rgba(0,0,0,.2);
}

.card h2{
    color:#be185d;
    margin-bottom:15px;
}

.card p{
    color:#475569;
    line-height:1.8;
}

.funciones{
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(250px,1fr));
    gap:15px;
    margin-top:20px;
}

.item{
    background:#fdf2f8;
    padding:18px;
    border-radius:15px;
    border-left:5px solid #ec4899;
}

@media(max-width:900px){
    .hero{
        flex-direction:column;
        text-align:center;
    }

    .hero-texto h2{
        font-size:2.2rem;
    }
}
</style>
</head>
<body>

<header>
    <h1>🏦 BANCO DIGITAL</h1>
</header>

<nav>
    <a href="index.php">🏠 Inicio</a>
    <a href="clientes.php">👤 Clientes</a>
    <a href="cuentas.php">💳 Cuentas</a>
    <a href="transacciones.php">💸 Transacciones</a>
    <a href="prestamos.php">📄 Préstamos</a>
    <a href="reportes.php">📊 Reportes</a>
</nav>

<section class="hero">
    <div class="hero-texto">
        <h2>Bienvenido al Banco Digital</h2>
        <p>
            Administra clientes, cuentas bancarias, transacciones y préstamos
            desde una plataforma moderna, rápida y segura.
        </p>
        <a href="clientes.php" class="btn-hero">Comenzar</a>
    </div>

    <div class="hero-imagen">
        <img src="banco2.png" alt="Banco Digital">
    </div>
</section>

<main>

<div class="stats">

    <div class="stat">
        <h3>👥 Clientes</h3>
        <p><?php echo $clientes['total']; ?></p>
    </div>

    <div class="stat">
        <h3>💳 Cuentas</h3>
        <p><?php echo $cuentas['total']; ?></p>
    </div>

    <div class="stat">
        <h3>💸 Transacciones</h3>
        <p><?php echo $transacciones['total']; ?></p>
    </div>

    <div class="stat">
        <h3>📄 Préstamos</h3>
        <p><?php echo $prestamos['total']; ?></p>
    </div>

</div>

<div class="card">
    <h2>Sistema de Gestión Bancaria</h2>
    <p>
        Controla toda la información de tu banco desde una sola interfaz:
        clientes, cuentas, depósitos, retiros, transferencias y préstamos.
    </p>
</div>

<div class="card">
    <h2>Funciones Principales</h2>

    <div class="funciones">
        <div class="item">✔ Registro de clientes</div>
        <div class="item">✔ Creación de cuentas bancarias</div>
        <div class="item">✔ Depósitos y retiros</div>
        <div class="item">✔ Transferencias</div>
        <div class="item">✔ Gestión de préstamos</div>
        <div class="item">✔ Reportes financieros</div>
    </div>
</div>

</main>

</body>
</html>
