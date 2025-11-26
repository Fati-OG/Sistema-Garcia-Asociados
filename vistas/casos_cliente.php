<?php 
session_start();
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'cliente') {
    header('Location: ../vistas/login.php'); exit;
}

require_once __DIR__ . '/../inc/conexion.php';

$idCliente = isset($_SESSION['id_cl']) ? (int)$_SESSION['id_cl'] : 0;
if ($idCliente <= 0) { header('Location: ../vistas/login.php'); exit; }

$sql = "SELECT Id_cs, No_cs, Tipo_cs, Estado_cs, Fecha_ini, Fecha_act,
               Nom_abgd_ct, App_abgd_ct, Apm_abgd_ct
        FROM casos
        WHERE Id_cl_ct = ?
        ORDER BY Fecha_ini DESC";

$stmt = $conexion->prepare($sql);
if (!$stmt) { die("Error SQL (casos_cliente->prepare): " . $conexion->error); }

$stmt->bind_param('i', $idCliente);
$stmt->execute();
$casos = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Mis Casos</title>
<link rel="stylesheet" href="../css/estilo_panel.css">
<style>
.table-box{
  background:#fff;
  padding:20px;
  border:1px solid #dcd6c8;
  border-radius:8px;
  max-width:900px;
  margin:auto;
}
table{
  width:100%;
  border-collapse:collapse;
  margin-top:10px;
}
th,td{
  padding:10px;
  border-bottom:1px solid #e5e0d8;
  text-align:center;
}
.btn-ver{
  background:#004aad;
  color:#fff;
  padding:6px 12px;
  border-radius:5px;
  text-decoration:none;
}
.btn-ver:hover{
  background:#00337a;
}
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
  <a href="casos_cliente.php">Mis Casos</a>
  <a href="facturas_cliente.php">Mis Facturas</a>

  <a href="../php/logout.php">Cerrar Sesión</a>
</div>

<div class="content">
  <h1 class="title">Mis Casos</h1>

  <div class="table-box">
    <table>
      <tr>
        <th>No. Caso</th>
        <th>Tipo</th>
        <th>Estado</th>
        <th>Abogado</th>
        <th>Fecha Inicio</th>
        <th>Última Act.</th>
        <th>Acciones</th>
      </tr>

      <?php if ($casos->num_rows === 0): ?>
        <tr><td colspan="7">No tienes casos registrados aún.</td></tr>
      <?php else: while($c = $casos->fetch_assoc()): ?>
        <tr>
          <td><?= $c['No_cs'] ?></td>
          <td><?= $c['Tipo_cs'] ?></td>
          <td><?= $c['Estado_cs'] ?></td>
          <td><?= $c['Nom_abgd_ct'].' '.$c['App_abgd_ct'] ?></td>
          <td><?= $c['Fecha_ini'] ?></td>
          <td><?= $c['Fecha_act'] ?: '—' ?></td>
          <td>
            <a class="btn-ver" href="ver_casos_cliente.php?id=<?= $c['Id_cs'] ?>">Ver más</a>
          </td>
        </tr>
      <?php endwhile; endif; ?>
    </table>
  </div>

</div>
</body>
</html>
