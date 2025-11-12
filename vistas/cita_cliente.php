<?php
session_start();
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'cliente') {
    header('Location: ../vistas/login.php'); exit;
}
require_once __DIR__ . '/../inc/conexion.php';

$idCliente = isset($_SESSION['id_cl']) ? (int)$_SESSION['id_cl'] : 0;
if ($idCliente <= 0) { header('Location: ../vistas/login.php'); exit; }

$sql = "SELECT Da_ct, Hra_ct, Nom_abgd_ct, App_abgd_ct, Apm_abgd_ct
        FROM cita
        WHERE Id_cl_ct = ?
        ORDER BY Da_ct, Hra_ct";
$stmt = $conexion->prepare($sql);
if (!$stmt) { die("Error SQL (cita_cliente->prepare): " . $conexion->error); }

$stmt->bind_param('i', $idCliente);
$stmt->execute();
$citas = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Mis Citas</title>
<link rel="stylesheet" href="../css/estilo_panel.css">
<style>
.table-box{background:#fff;padding:20px;border:1px solid #dcd6c8;border-radius:8px;max-width:720px;margin:auto;}
table{width:100%;border-collapse:collapse;margin-top:10px;}
th,td{padding:10px;border-bottom:1px solid #e5e0d8;text-align:center;}
</style>
</head>
<body>
<div class="header">
  <span>⚖️ García &amp; Asociados</span>
  <span>Cliente: <?php echo htmlspecialchars($_SESSION['Nom_cl'].' '.$_SESSION['App_cl']); ?></span>
</div>
<div class="sidebar">
  <a href="panel_cliente.php">Inicio</a>
  <a href="cita_cliente.php">Mis Citas</a>
  <a href="../php/logout.php">Cerrar Sesión</a>
</div>
<div class="content">
  <h1 class="title">Mis Citas</h1>
  <div class="table-box">
    <table>
      <tr><th>Fecha</th><th>Hora</th><th>Abogado</th></tr>
      <?php if ($citas->num_rows === 0): ?>
        <tr><td colspan="3">No tienes citas registradas aún.</td></tr>
      <?php else: while($c = $citas->fetch_assoc()): ?>
        <tr>
          <td><?php echo htmlspecialchars($c['Da_ct']); ?></td>
          <td><?php echo htmlspecialchars($c['Hra_ct']); ?></td>
          <td><?php echo htmlspecialchars($c['Nom_abgd_ct'].' '.$c['App_abgd_ct'].' '.$c['Apm_abgd_ct']); ?></td>
        </tr>
      <?php endwhile; endif; ?>
    </table>
  </div>
</div>
</body>
</html>
