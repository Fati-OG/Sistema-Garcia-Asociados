<?php
session_start();
if(!isset($_SESSION['rol']) || $_SESSION['rol'] != "cliente"){
    header("Location: ../vistas/login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Panel del Cliente</title>
<link rel="stylesheet" href="../CSS/estilo_panel.css">
<style>
.card {
    background: white;
    border: 1px solid #dcd6c8;
    max-width: 600px;
    padding: 25px;
    margin: 40px auto;
    border-radius: 8px;
}
.card h2{
    font-family: 'Lora', serif;
    font-size: 26px;
    text-align: center;
    margin-bottom: 20px;
}
.card p{
    font-size: 18px;
    margin-bottom: 10px;
}
</style>
</head>

<body>

<div class="header">
    <span>⚖️ García & Asociados</span>
    <span>Cliente: <?php echo $_SESSION['Nom_cl']." ".$_SESSION['App_cl']; ?></span>
</div>

<!-- Menú lateral -->
<div class="sidebar">
    <a href="panel_cliente.php">Inicio</a>
    <a href="cita_cliente.php">Mis Citas</a>
    <a href="../vistas/casos_cliente.php">Mis Casos</a>
    <a href="facturas_cliente.php">Mis Facturas</a>

    <a href="../php/logout.php">Cerrar Sesión</a>
    
</div>

<div class="content">
    <h1 class="title">Bienvenido/a</h1>

    <div class="card">
        <h2>Mis Datos</h2>

        <?php
        include "../inc/conexion.php";
        $idCliente = $_SESSION['id_cl'];
        $consulta = $conexion->query("SELECT * FROM cliente WHERE Id_cl = $idCliente");
        $cliente = $consulta->fetch_assoc();
        ?>

        <p><strong>Nombre completo:</strong> <?php echo $cliente['Nom_cl'] . " " . $cliente['App_cl'] . " " . $cliente['Apm_cl']; ?></p>
        <p><strong>RFC:</strong> <?php echo $cliente['Rfc_cl']; ?></p>
        <p><strong>Código Postal:</strong> <?php echo $cliente['cp_cl']; ?></p>
        <p><strong>Correo:</strong> <?php echo $cliente['Cor_cl']; ?></p>
        <p><strong>Teléfono:</strong> <?php echo $cliente['tel_cl']; ?></p>
        <p><strong>Dirección:</strong> <?php echo $cliente['Dir_cl']; ?></p>
    </div>
</div>

</body>
</html>
