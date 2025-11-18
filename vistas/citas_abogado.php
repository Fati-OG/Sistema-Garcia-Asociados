<?php
session_start();
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'abogado') {
    header('Location: ../vistas/login.php'); 
    exit;
}

require_once __DIR__ . '/../inc/conexion.php';

// ID del abogado
$idAbogado = isset($_SESSION['Id_abgd']) ? (int)$_SESSION['Id_abgd'] : 0;
if ($idAbogado <= 0) { 
    die("⚠️ Error: el ID del abogado no está definido en la sesión."); 
}

// Consulta de citas
$sql = "SELECT Id_ct, Da_ct, Hra_ct, Nom_cl_ct, App_cl_ct, Apm_cl_ct
        FROM cita
        WHERE abgd_id_ct = ?
        ORDER BY Da_ct, Hra_ct";

$stmt = $conexion->prepare($sql);
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

/* ----- ESTRUCTURA ----- */
* {
  font-family: 'Montserrat', sans-serif;
  box-sizing: border-box;
}

.content {
  margin-left: 220px;
  padding: 20px;
}

/* ----- TABLA ----- */
.table-box {
  background: white;
  padding: 20px;
  border: 1px solid #dcd6c8;
  border-radius: 8px;
  max-width: 1000px;
  margin: auto;
  overflow-x: auto;
}
table {
  width: 100%;
  border-collapse: collapse;
  min-width: 700px;
}
th, td {
  padding: 12px;
  border-bottom: 1px solid #e5e0d8;
  text-align: center;
}
th {
  background: #f5f1eb;
  font-weight: bold;
}

/* ----- BOTONES ----- */
.acciones {
  display: flex;
  gap: 8px;
  justify-content: center;
}

.btn-eliminar {
  background: #c0392b;
  color: white;
  padding: 7px 12px;
  border-radius: 5px;
  font-size: 13px;
  border: none;
  cursor: pointer;
  transition: 0.2s;
}
.btn-eliminar:hover {
  background: #96281b;
}

/* RESPONSIVE */
@media (max-width: 768px) {
  .content { margin-left: 0; padding: 10px; }
  .sidebar { position: relative; width: 100%; }
}
</style>

</head>
<body>

<div class="header">
  <span>⚖️ García & Asociados</span>
  <span>Abogado: <?php echo htmlspecialchars($_SESSION['Nom_abgd'].' '.$_SESSION['App_abgd']); ?></span>
</div>

<div class="sidebar">
    <a href="panel_abogado.php">Inicio</a>
    <a href="citas_abogado.php">Mis Citas</a>
    <a href="agendar_cita.php">Agendar Cita</a>
    <a href="registro_cliente.php">Registrar Cliente</a>

    <?php if ((int)$_SESSION['es_admin'] === 1): ?>
        <a href="clientes_registrados.php">Clientes Registrados</a>
        <a href="registro_abogado.php">Registrar Abogado</a>
        <a href="abogados_registrados.php">Abogados Registrados</a>
        <a href="detalleae.php">Detalle AE</a>
    <?php endif; ?>

    <a href="../PHP/logout.php">Cerrar Sesión</a>
</div>

<div class="content">
<h1 class="title">Mis Citas</h1>

<?php if (isset($_GET['msg'])): ?>
<?php
$mensaje = "";
$tipo = "info";

switch ($_GET['msg']) {

  // Eliminación
  case 'ok':
    $mensaje = "✅ Cita eliminada correctamente.";
    $tipo = "success";
    break;
  case 'notfound':
    $mensaje = "⚠️ No se encontró la cita o no pertenece a este abogado.";
    $tipo = "warning";
    break;
  case 'error_sql':
    $mensaje = "❌ Error interno al eliminar la cita.";
    $tipo = "error";
    break;
  case 'error_id':
    $mensaje = "⚠️ ID de cita no recibido.";
    $tipo = "warning";
    break;

  // Creación
  case 'cita_ok':
    $mensaje = "✅ Cita guardada exitosamente.";
    $tipo = "success";
    break;
  case 'cita_err':
    $mensaje = "❌ No se pudo guardar la cita.";
    $tipo = "error";
    break;
  case 'cita_faltan':
    $mensaje = "⚠️ Faltan datos: cliente, fecha u hora.";
    $tipo = "warning";
    break;
  case 'cita_no_cli':
    $mensaje = "⚠️ Cliente no encontrado.";
    $tipo = "warning";
    break;
  case 'cita_sql_prep_cli':
    $mensaje = "❌ Error preparando consulta del cliente.";
    $tipo = "error";
    break;
  case 'cita_sql_prep_ins':
    $mensaje = "❌ Error preparando el guardado de la cita.";
    $tipo = "error";
    break;
    case 'edit_ok':
    $mensaje = "✅ Cita actualizada correctamente.";
    $tipo = "success";
    break;

case 'edit_err':
    $mensaje = "❌ No se pudo actualizar la cita.";
    $tipo = "error";
    break;

}
?>
<div class="mensaje <?= $tipo ?>" id="mensaje-notificacion">
  <?= $mensaje ?>
</div>
<?php endif; ?>

<style>
.mensaje {
  position: fixed;
  top: 20px;
  left: 50%;
  transform: translateX(-50%);
  padding: 12px 18px;
  border-radius: 8px;
  font-weight: 500;
  font-size: 15px;
  color: white;
  animation: fadeIn .5s, fadeOut .5s 3s forwards;
  z-index: 9999;
}
.mensaje.success { background:#28a745; }
.mensaje.warning { background:#ffc107; color:#212529; }
.mensaje.error { background:#dc3545; }
@keyframes fadeIn { from{opacity:0; transform:translate(-50%,-20px);} to{opacity:1;} }
@keyframes fadeOut { from{opacity:1;} to{opacity:0; transform:translate(-50%,-20px);} }
</style>

<script>
setTimeout(()=>{ 
  const m=document.getElementById('mensaje-notificacion');
  if(m) m.remove();
},3500);
</script>

<div class="table-box">
<table>
<tr>
  <th>Fecha</th>
  <th>Hora</th>
  <th>Cliente</th>
  <th>Acciones</th>
</tr>

<?php if ($citas->num_rows === 0): ?>
<tr><td colspan="4">No hay citas registradas.</td></tr>
<?php else: ?>
<?php while($c = $citas->fetch_assoc()): ?>
<tr>
  <td><?= htmlspecialchars($c['Da_ct']); ?></td>
  <td><?= htmlspecialchars($c['Hra_ct']); ?></td>
  <td><?= htmlspecialchars($c['Nom_cl_ct'].' '.$c['App_cl_ct'].' '.$c['Apm_cl_ct']); ?></td>
<td>
    <div style="display:flex; gap:8px; justify-content:center;">

        <!-- BOTÓN EDITAR -->
        <a href="editar_cita.php?id=<?= $c['Id_ct']; ?>" 
           style="background:#004aad;color:white;padding:6px 10px;border-radius:5px;text-decoration:none;font-size:13px;">
           Editar
        </a>

        <!-- BOTÓN ELIMINAR -->
        <form action="../php/eliminar_cita.php" method="POST"
              onsubmit="return confirm('¿Seguro que deseas eliminar esta cita?');">
            <input type="hidden" name="id_cita" value="<?= $c['Id_ct']; ?>">
            <button type="submit" 
                    style="background:#c0392b;color:white;border:none;padding:6px 10px;border-radius:5px;cursor:pointer;font-size:13px;">
                Eliminar
            </button>
        </form>

    </div>
</td>

</tr>
<?php endwhile; ?>
<?php endif; ?>
</table>
</div>

</div>
</body>
</html>
