<?php
session_start();
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'abogado') {
    header('Location: ../vistas/login.php'); exit;
}
require_once __DIR__ . '/../inc/conexion.php';

$idAbogado = isset($_SESSION['Id_abgd']) ? (int)$_SESSION['Id_abgd'] : 0;
if ($idAbogado <= 0) { header('Location: ../vistas/login.php'); exit; }

$sql = "SELECT Da_ct, Hra_ct, Nom_cl_ct, App_cl_ct, Apm_cl_ct
        FROM cita
        WHERE abgd_id_ct = ?
        ORDER BY Da_ct, Hra_ct";
$stmt = $conexion->prepare($sql);
if (!$stmt) { die("Error SQL (citas->prepare): " . $conexion->error); }

$stmt->bind_param('i', $idAbogado);
$stmt->execute();
$citas = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Mis Citas - Abogado</title>
<link rel="stylesheet" href="../css/estilo_panel.css">
<style>
.table-box{background:#fff;padding:20px;border:1px solid #dcd6c8;border-radius:8px;max-width:800px;margin:auto;}
table{width:100%;border-collapse:collapse;margin-top:10px;}
th,td{padding:10px;border-bottom:1px solid #e5e0d8;text-align:center;}
</style>
</head>
<body>
<div class="header">
  <span>⚖️ García &amp; Asociados</span>
  <span>Abogado: <?php echo htmlspecialchars($_SESSION['Nom_abgd'].' '.$_SESSION['App_abgd']); ?></span>
</div>
<div class="sidebar">
  <a href="panel_abogado.php">Inicio</a>
  <a href="citas_abogado.php">Mis Citas</a>
  <a href="agendar_cita.php">Agendar Cita</a>
  <a href="../php/logout.php">Cerrar Sesión</a>
</div>
<div class="content">
  <h1 class="title">Mis Citas</h1>
  <div class="table-box">
    <table>
      <tr><th>Fecha</th><th>Hora</th><th>Cliente</th></tr>
      <?php if ($citas->num_rows === 0): ?>
        <tr><td colspan="3">No hay citas registradas.</td></tr>
      <?php else: while($c = $citas->fetch_assoc()): ?>
        <tr>
          <td><?php echo htmlspecialchars($c['Da_ct']); ?></td>
          <td><?php echo htmlspecialchars($c['Hra_ct']); ?></td>
          <td><?php echo htmlspecialchars($c['Nom_cl_ct'].' '.$c['App_cl_ct'].' '.$c['Apm_cl_ct']); ?></td>
        </tr>
      <?php endwhile; endif; ?>
    </table>
  </div>
</div>
</body>
</html>
