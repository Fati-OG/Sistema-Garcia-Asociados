<?php
session_start();
if(!isset($_SESSION['rol']) || $_SESSION['rol'] != "abogado"){
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Panel del Abogado</title>
<link rel="stylesheet" href="../CSS/estilo_panel.css">
</head>

<body>

<div class="header">
    <span>⚖️ García & Asociados</span>
    <span>Bienvenido, <?php echo htmlspecialchars($_SESSION['Nom_abgd'].' '.$_SESSION['App_abgd']); ?></span>
</div>
<div class="header-slogan">Compromiso, ética y ley.</div>


<div class="sidebar">
    <a href="panel_abogado.php">Inicio</a>
    <a href="citas_abogado.php">Mis Citas</a>
    <a href="registro_cliente.php">Registrar Cliente</a>
    <a href="registro_abogado.php">Registrar Abogado</a>
    
    <a href="../PHP/logout.php">Cerrar Sesión</a>
</div>

<div class="content">
    <h1 class="title">Panel Principal</h1>
    <p>Seleccione una opción del menú para continuar.</p>
    

</div>

</body>
</html>
