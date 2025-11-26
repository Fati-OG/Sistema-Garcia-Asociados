<?php
if (!isset($_SESSION)) { session_start(); }

$esAdmin = $_SESSION['es_admin'] ?? 0;
?>

<div class="sidebar">
    <a href="../vistas/panel_abogado.php">Inicio</a>
    <a href="casos_abogado.php">Casos</a>
    <a href="../vistas/citas_abogado.php">Mis Citas</a>
    <a href="../vistas/agendar_cita.php">Agendar Cita</a>
    <a href="../vistas/registro_cliente.php">Registrar Cliente</a>
    <?php if ($esAdmin == 1): ?>
        <a href="../vistas/clientes_registrados.php">Clientes Registrados</a>
        <a href="../vistas/registro_abogado.php">Registrar Abogado</a>
        <a href="../vistas/abogados_registrados.php">Abogados Registrados</a>
        <a href="../vistas/detalleae.php">Detalle AE</a>
    <?php endif; ?>
    <a href="../PHP/logout.php">Cerrar Sesión</a>
</div>
