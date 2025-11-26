<?php
session_start();
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'cliente') {
    header('Location: ../vistas/login.php');
    exit;
}

require_once __DIR__ . '/../inc/conexion.php';

$idCliente = isset($_SESSION['id_cl']) ? (int)$_SESSION['id_cl'] : 0;
if ($idCliente <= 0) {
    die("Cliente no válido.");
}

$idCaso = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($idCaso <= 0) {
    die("Caso no válido.");
}

/* Verificar que el caso pertenece al cliente */
$sql = "SELECT * FROM casos WHERE Id_cs = ? AND Id_cl_ct = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("ii", $idCaso, $idCliente);
$stmt->execute();
$caso = $stmt->get_result()->fetch_assoc();

if (!$caso) {
    die("Caso no encontrado.");
}

/* Documentos */
$sqlDocs = "SELECT * FROM documentos_caso WHERE Id_cs = ? ORDER BY fecha_subida DESC";
$stmtDocs = $conexion->prepare($sqlDocs);
$stmtDocs->bind_param("i", $idCaso);
$stmtDocs->execute();
$docs = $stmtDocs->get_result();

/* Notas */
$sqlNotas = "SELECT n.*, a.Nom_abgd, a.App_abgd
             FROM notas_caso n
             JOIN abogado a ON n.Id_abgd = a.Id_abgd
             WHERE n.Id_cs = ?
             ORDER BY n.fecha DESC";
$stmtNotas = $conexion->prepare($sqlNotas);
$stmtNotas->bind_param("i", $idCaso);
$stmtNotas->execute();
$notas = $stmtNotas->get_result();
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Detalle del Caso</title>
<link rel="stylesheet" href="../css/estilo_panel.css">

<style>
/* Caja general */
.container-box{
  background:#fff;
  padding:25px;
  border-radius:10px;
  border:1px solid #dcd6c8;
  max-width:850px;
  margin:auto;
  box-shadow:0 2px 8px rgba(0,0,0,0.10);
}

/* Títulos */
.section-title{
  font-size:20px;
  font-weight:700;
  margin-top:25px;
  margin-bottom:10px;
  border-bottom:2px solid #004aad;
  color:#003b82;
}

/* Información general */
.info-item{
  margin-bottom:10px;
  font-size:15px;
}

/* Notas */
.note{
  background:#f7f8fa;
  border-left:4px solid #004aad;
  padding:10px;
  margin-bottom:12px;
  border-radius:6px;
}

/* Documentos */
.doc-item{
  padding:12px 8px;
  border-bottom:1px solid #ddd;
  display:flex;
  align-items:center;
  justify-content:space-between;
}
.doc-item b{ font-size:15px; }
.doc-item small{ font-size:12px;color:#666; }

/* Botón */
.btn{
  background:#004aad;
  color:#fff;
  padding:7px 12px;
  border-radius:6px;
  text-decoration:none;
  font-size:14px;
}
.btn:hover{ background:#00337a; }
</style>
</head>

<body>

<div class="header">
  <span>⚖️ García &amp; Asociados</span>
  <span>Cliente: <?php echo htmlspecialchars($_SESSION['Nom_cl']." ".$_SESSION['App_cl']); ?></span>
</div>

<div class="sidebar">

  <a href="casos_cliente.php">Volver</a>
 
</div>

<div class="content">
<div class="container-box">

<h1 class="title">Detalle del Caso</h1>

<!-- Información general -->
<div class="section-title">Información General</div>

<p class="info-item"><b>Número de Caso:</b> <?= $caso['No_cs'] ?></p>
<p class="info-item"><b>Tipo:</b> <?= $caso['Tipo_cs'] ?></p>
<p class="info-item"><b>Estado:</b> <?= $caso['Estado_cs'] ?></p>
<p class="info-item"><b>Abogado:</b> <?= $caso['Nom_abgd_ct']." ".$caso['App_abgd_ct'] ?></p>
<p class="info-item"><b>Descripción:</b><br><?= nl2br($caso['Desc_cs']) ?></p>


<!-- Notas -->
<div class="section-title">Notas del Abogado</div>

<?php if ($notas->num_rows === 0): ?>
  <p>No hay notas registradas.</p>
<?php else: ?>
  <?php while($n = $notas->fetch_assoc()): ?>
    <div class="note">
      <b><?= $n['Nom_abgd'] ?>:</b>
      <small>(<?= $n['fecha'] ?>)</small><br>
      <?= nl2br($n['nota']) ?>
    </div>
  <?php endwhile; ?>
<?php endif; ?>


<!-- Documentos -->
<div class="section-title">Documentos del Caso</div>

<?php if ($docs->num_rows === 0): ?>
  <p>No hay documentos cargados.</p>
<?php else: ?>
  <?php while($d = $docs->fetch_assoc()): ?>
    <div class="doc-item">
      <div>
        📄 <b><?= $d['nombre_archivo'] ?></b><br>
        <small><?= $d['fecha_subida'] ?></small>
      </div>

      <a class="btn" href="<?= $d['ruta_archivo'] ?>" download>Descargar</a>
    </div>
  <?php endwhile; ?>
<?php endif; ?>

</div>
</div>

</body>
</html>
