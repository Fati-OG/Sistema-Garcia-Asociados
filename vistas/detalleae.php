<?php
session_start();
if (!isset($_SESSION['rol']) || $_SESSION['rol'] != "abogado") {
    header("Location: login.php");
    exit();
}
if ((int)$_SESSION['es_admin'] !== 1) {
    echo "<script>alert('⚠️ Solo el administrador puede acceder a esta sección.'); window.location='panel_abogado.php';</script>";
    exit();
}

include "../inc/conexion.php";

$abogados = $conexion->query("SELECT Id_abgd, Nom_abgd, App_abgd, Apm_abgd FROM abogado ORDER BY Nom_abgd ASC");
$especialidades = $conexion->query("SELECT Id_esp, Nom_esp FROM especialidad ORDER BY Nom_esp ASC");
$detalles = $conexion->query("
    SELECT da.Id_da, a.Nom_abgd, a.App_abgd, e.Nom_esp
    FROM detalleae da
    JOIN abogado a ON da.Id_abgd_da = a.Id_abgd
    JOIN especialidad e ON da.Id_esp_da = e.Id_esp
    ORDER BY a.Nom_abgd
");
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Detalle Abogado–Especialidad</title>
<link rel="stylesheet" href="../css/estilo_panel.css">
<style>
.form-box, .table-box {
  background: #fff;
  padding: 20px;
  border: 1px solid #dcd6c8;
  border-radius: 8px;
  margin: 20px auto;
  max-width: 800px;
}
table {
  width: 100%;
  border-collapse: collapse;
  margin-top: 10px;
}
th, td {
  padding: 8px;
  border-bottom: 1px solid #e5e0d8;
  text-align: center;
}
button {
  background:#004aad;
  color: white;
  border: none;
  padding: 8px 14px;
  border-radius: 5px;
  cursor: pointer;
}
button:hover { background:#00337a;} 
.eliminar {
  background: #c0392b;
}
.eliminar:hover {
  background: #922b21;
}

/* Estilo para mensajes */
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
</head>
<body>

<?php include "../inc/header.php"; ?>
<?php include "../inc/sidebar.php"; ?>

<div class="content">
  <h1 class="title">Relación Abogado–Especialidad</h1>

  <?php if (isset($_GET['msg'])): ?>
    <?php
      $mensaje = "";
      $tipo = "info";
      switch ($_GET['msg']) {
        case 'ok':
          $mensaje = "✅ Relación registrada correctamente.";
          $tipo = "success";
          break;
        case 'error':
          $mensaje = "❌ Error al guardar la relación.";
          $tipo = "error";
          break;
        case 'error_id':
          $mensaje = "⚠️ ID de relación no recibido.";
          $tipo = "warning";
          break;
        case 'error_exec':
          $mensaje = "❌ No se pudo eliminar la relación.";
          $tipo = "error";
          break;
        case 'faltan':
          $mensaje = "⚠️ Debes seleccionar un abogado y una especialidad.";
          $tipo = "warning";
          break;
      }
    ?>
    <div class="mensaje <?= $tipo ?>" id="mensaje-notificacion">
      <?= $mensaje ?>
    </div>
  <?php endif; ?>

  <script>
    setTimeout(() => {
      const msg = document.getElementById('mensaje-notificacion');
      if (msg) msg.remove();
    }, 3500);
  </script>

  <div class="form-box">
    <form action="../php/detalleae_guardar.php" method="POST">
      <label>Abogado:</label>
      <select name="id_abogado" required>
        <option value="">--Selecciona--</option>
        <?php while ($a = $abogados->fetch_assoc()): ?>
          <option value="<?= $a['Id_abgd'] ?>"><?= $a['Nom_abgd']." ".$a['App_abgd'] ?></option>
        <?php endwhile; ?>
      </select>

      <label>Especialidad:</label>
      <select name="id_especialidad" required>
        <option value="">--Selecciona--</option>
        <?php while ($e = $especialidades->fetch_assoc()): ?>
          <option value="<?= $e['Id_esp'] ?>"><?= $e['Nom_esp'] ?></option>
        <?php endwhile; ?>
      </select>

      <button type="submit">Guardar Relación</button>
    </form>
  </div>

  <div class="table-box">
    <table>
      <tr>
        <th>Abogado</th>
        <th>Especialidad</th>
        <th>Acciones</th>
      </tr>

      <?php if ($detalles->num_rows > 0): ?>
        <?php while($d = $detalles->fetch_assoc()): ?>
          <tr>
            <td><?= htmlspecialchars($d['Nom_abgd']." ".$d['App_abgd']); ?></td>
            <td><?= htmlspecialchars($d['Nom_esp']); ?></td>
            <td>
              <form action="../php/detalleae_eliminar.php" method="POST" onsubmit="return confirm('¿Eliminar esta relación?');">
                <input type="hidden" name="id_da" value="<?= $d['Id_da']; ?>">
                <button type="submit" class="eliminar">Eliminar</button>
              </form>
            </td>
          </tr>
        <?php endwhile; ?>
      <?php else: ?>
        <tr><td colspan="3">No hay relaciones registradas.</td></tr>
      <?php endif; ?>
    </table>
  </div>
</div>
</body>
</html>
