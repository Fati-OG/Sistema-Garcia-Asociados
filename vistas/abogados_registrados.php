<?php
session_start();

// Validar sesión y rol
if (!isset($_SESSION['rol']) || $_SESSION['rol'] != "abogado") {
    header("Location: ../vistas/login.php");
    exit();
}

// Solo el admin puede acceder
if ((int)$_SESSION['es_admin'] !== 1) {
    echo "<script>alert('⚠️ Solo el administrador puede acceder a esta sección.'); window.location='panel_abogado.php';</script>";
    exit();
}

include "../inc/conexion.php";
$abogados = $conexion->query("SELECT Id_abgd, Nom_abgd, App_abgd, Apm_abgd, Cor_abgd, Tel_abgd, Dir_abgd, es_admin FROM abogado ORDER BY Nom_abgd ASC");
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Abogados Registrados</title>
<link rel="stylesheet" href="../css/estilo_panel.css">
<style>
/* ----- ESTRUCTURA GENERAL ----- */
* {
  font-family: 'Montserrat', sans-serif;
  box-sizing: border-box;
}

.content {
  margin-left: 220px; /* evita empalme con sidebar */
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
  overflow-x: auto; /* RESPONSIVO */
}

table {
  width: 100%;
  border-collapse: collapse;
  min-width: 800px;
}

th, td {
  padding: 12px;
  border-bottom: 1px solid #e5e0d8;
  text-align: center;
  font-size: 14px;
}

th {
  background: #f5f1eb;
  font-weight: bold;
}

/* ----- BOTONES / ACCIONES ----- */
.acciones {
  display: flex;
  gap: 8px;
  justify-content: center;
}

/* BOTÓN EDITAR */
.btn-editar {
  background: #004aad;
  color: white;
  padding: 7px 12px;
  border-radius: 5px;
  font-size: 13px;
  text-decoration: none;
  transition: 0.2s ease;
}

.btn-editar:hover {
  background: #00337a;
}

/* BOTÓN ELIMINAR */
.btn-eliminar {
  background: #c0392b;
  color: white;
  padding: 7px 12px;
  border: none;
  border-radius: 5px;
  font-size: 13px;
  cursor: pointer;
  transition: 0.2s ease;
}

.btn-eliminar:hover {
  background: #96281b;
}

/* ETIQUETA ADMIN */
.admin-label {
  background: #ffc107;
  color: #212529;
  font-size: 12px;
  padding: 3px 6px;
  border-radius: 4px;
  font-weight: bold;
}

/* RESPONSIVO */
@media (max-width: 768px) {
  .content {
    margin-left: 0;
    padding: 10px;
  }
  .sidebar {
    position: relative;
    width: 100%;
  }
}
</style>

</head>
<body>

<div class="header">
  <span>⚖️ García & Asociados</span>
  <span>Administrador: <?php echo $_SESSION['Nom_abgd']." ".$_SESSION['App_abgd']; ?></span>
</div>

<div class="sidebar">
    <a href="panel_abogado.php">Inicio</a>
    <a href="citas_abogado.php">Mis Citas</a>
    <a href="agendar_cita.php">Agendar Cita</a>
    <a href="registro_cliente.php">Registrar Cliente</a>

    <?php if (isset($_SESSION['es_admin']) && (int)$_SESSION['es_admin'] === 1): ?>
        <a href="clientes_registrados.php">Clientes Registrados</a>
        <a href="registro_abogado.php">Registrar Abogado</a>
    <a href="abogados_registrados.php">Abogados Registrados</a>
    <a href="detalleae.php">Detalle AE</a>
    <?php endif; ?>

    <a href="../PHP/logout.php">Cerrar Sesión</a>
</div>

<div class="content">
<h1 class="title">Abogados Registrados</h1>

<?php if (isset($_GET['msg'])): ?>
  <?php
    $mensaje = "";
    $tipo = "info";

switch ($_GET['msg']) {
  // ELIMINAR
  case 'ok':
    $mensaje = "✅ Abogado eliminado correctamente.";
    $tipo = "success";
    break;
  case 'error_id':
    $mensaje = "⚠️ ID de abogado no recibido.";
    $tipo = "warning";
    break;
  case 'error_sql':
    $mensaje = "❌ Error interno al preparar la eliminación.";
    $tipo = "error";
    break;
  case 'error_exec':
    $mensaje = "❌ No se pudo eliminar el abogado.";
    $tipo = "error";
    break;
  case 'no_permiso':
    $mensaje = "⚠️ No puedes eliminar a otro administrador ni a ti mismo.";
    $tipo = "warning";
    break;

  // EDITAR
  case 'edit_ok':
    $mensaje = "✅ Abogado actualizado correctamente.";
    $tipo = "success";
    break;
  case 'edit_err':
    $mensaje = "❌ Error al actualizar el abogado.";
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
  font-family: 'Montserrat', sans-serif;
  font-weight: 500;
  font-size: 15px;
  color: white;
  animation: fadeIn 0.5s ease, fadeOut 0.5s ease 3s forwards;
  z-index: 9999;
  box-shadow: 0 2px 8px rgba(0,0,0,0.15);
}
.mensaje.success { background-color: #28a745; }  
.mensaje.warning { background-color: #ffc107; color: #212529; }
.mensaje.error   { background-color: #dc3545; }  
@keyframes fadeIn {
  from { opacity: 0; transform: translate(-50%, -20px); }
  to { opacity: 1; transform: translate(-50%, 0); }
}
@keyframes fadeOut {
  from { opacity: 1; transform: translate(-50%, 0); }
  to { opacity: 0; transform: translate(-50%, -20px); }
}
</style>

<script>
  setTimeout(() => {
    const msg = document.getElementById('mensaje-notificacion');
    if (msg) msg.remove();
  }, 3500);
</script>

<div class="table-box">
<table>
<tr>
  <th>Nombre</th>
  <th>Correo</th>
  <th>Teléfono</th>
  <th>Dirección</th>
  <th>Rol</th>
  <th>Acciones</th>
</tr>

<?php if ($abogados->num_rows > 0): ?>
  <?php while($abg = $abogados->fetch_assoc()): ?>
    <tr>
      <td><?= htmlspecialchars($abg['Nom_abgd']." ".$abg['App_abgd']." ".$abg['Apm_abgd']); ?></td>
      <td><?= htmlspecialchars($abg['Cor_abgd']); ?></td>
      <td><?= htmlspecialchars($abg['Tel_abgd']); ?></td>
      <td><?= htmlspecialchars($abg['Dir_abgd']); ?></td>
      <td>
        <?php if ((int)$abg['es_admin'] === 1): ?>
          <span class="admin-label">Administrador</span>
        <?php else: ?>
          Abogado
        <?php endif; ?>
      </td>
      <td>
  <div class="acciones">

    <a href="editar_abogado.php?id=<?= $abg['Id_abgd']; ?>" 
       class="btn-editar">
        Editar
    </a>

    <?php if ((int)$abg['es_admin'] === 0 && (int)$abg['Id_abgd'] !== (int)$_SESSION['Id_abgd']): ?>
      <form action="../php/eliminar_abogado.php" method="POST"
            onsubmit="return confirm('¿Eliminar a <?= $abg['Nom_abgd']; ?>?');">
        <input type="hidden" name="id_abgd" value="<?= $abg['Id_abgd']; ?>">
        <button type="submit" class="btn-eliminar">
          Eliminar
        </button>
      </form>
    <?php else: ?>
      <em style="color:gray;">No permitido</em>
    <?php endif; ?>

  </div>
</td>


    </tr>
  <?php endwhile; ?>
<?php else: ?>
  <tr><td colspan="6">No hay abogados registrados.</td></tr>
<?php endif; ?>
</table>
</div>
</div>
</body>
</html>
