<?php
session_start();
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'abogado') {
    header('Location: ../vistas/login.php');
    exit();
}

require_once __DIR__ . '/../inc/conexion.php';

$idAbogado = $_SESSION['Id_abgd'];
$esAdmin   = $_SESSION['es_admin'];
$idCaso    = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($idCaso <= 0) {
    die("Error: ID de caso inválido");
}

/* ============================
   OBTENER DATOS DEL CASO
   ============================ */
$sql = "SELECT * FROM casos WHERE Id_cs = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $idCaso);
$stmt->execute();
$caso = $stmt->get_result()->fetch_assoc();

if (!$caso) {
    die("❌ Caso no encontrado");
}

/* ============================
   OBTENER NOTAS DEL CASO
   ============================ */
$sqlNotas = "SELECT n.*, a.Nom_abgd, a.App_abgd
             FROM notas_caso n
             INNER JOIN abogado a ON n.Id_abgd = a.Id_abgd
             WHERE n.Id_cs = ?
             ORDER BY n.fecha DESC";
$stmtNotas = $conexion->prepare($sqlNotas);
$stmtNotas->bind_param("i", $idCaso);
$stmtNotas->execute();
$notas = $stmtNotas->get_result();

/* ============================
   OBTENER DOCUMENTOS DEL CASO
   ============================ */
$sqlDocs = "SELECT * FROM documentos_caso WHERE Id_cs = ? ORDER BY fecha_subida DESC";
$stmtDocs = $conexion->prepare($sqlDocs);
$stmtDocs->bind_param("i", $idCaso);
$stmtDocs->execute();
$docs = $stmtDocs->get_result();
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Detalle del Caso</title>
<link rel="stylesheet" href="../css/estilo_panel.css">

<style>
.container-box{
    background:#fff;
    padding:25px;
    border-radius:8px;
    border:1px solid #dcd6c8;
    max-width:1100px;
    margin:auto;
    box-shadow:0 2px 8px rgba(0,0,0,0.08);
}

/* Títulos de sección */
.section-title{
    font-size:20px;
    margin-top:30px;
    margin-bottom:12px;
    font-weight:700;
    border-bottom:2px solid #004aad;
    padding-bottom:4px;
    color:#003b82;
}

/* Datos generales */
.data-row{
    display:flex;
    gap:25px;
    margin-bottom:12px;
}
.data-row div{
    width:50%;
    font-size:15px;
}

/* Badges estado */
.badge{
    padding:6px 12px;
    border-radius:12px;
    font-size:13px;
    font-weight:600;
    color:white;
}
.badge-abierto{ background:#28a745; }
.badge-proceso{ background:#ffc107; color:#333; }
.badge-cerrado{ background:#dc3545; }

/* Timeline de notas */
.timeline{
    list-style:none;
    padding-left:0;
    border-left:3px solid #004aad;
    margin-left:15px;
}
.timeline li{
    margin-bottom:20px;
    padding-left:12px;
}
.timeline .fecha{
    font-size:12px;
    color:#777;
    margin-bottom:4px;
}
.timeline .nota{
    background:#f8f9fa;
    padding:12px;
    border-radius:6px;
    border:1px solid #ddd;
}

/* Documentos */
.doc-item{
    display:flex;
    justify-content:space-between;
    align-items:center;
    padding:14px 10px;
    border-bottom:1px solid #ddd;
    background:#fafafa;
    border-radius:6px;
    margin-bottom:8px;
}

.doc-left{
    display:flex;
    flex-direction:column;
    max-width:75%;
}

.doc-left b{
    font-size:15px;
    color:#003b82;
    word-break: break-word;   /* <-- evita empalmes */
}

.doc-left small{
    font-size:12px;
    color:#666;
    margin-top:4px;
}

.doc-btn{
    background:#004aad;
    color:white;
    padding:8px 16px;
    border-radius:6px;
    text-decoration:none;
    font-size:14px;
    white-space:nowrap;
    transition:.2s;
}

.doc-btn:hover{
    background:#00337a;
}

/* Responsivo */
@media(max-width:768px){
    .doc-item{
        flex-direction:column;
        align-items:flex-start;
        gap:10px;
    }
    .doc-btn{
        width:100%;
        text-align:center;
    }
}
.doc-actions {
    display:flex;
    gap:8px;
}

.btn-blue{ background:#004aad; }
.btn-green{ background:#28a745; }
.btn-red{ background:#dc3545; }

.btn-blue:hover{ background:#00337a; }
.btn-green:hover{ background:#1e7e34; }
.btn-red:hover{ background:#b52a2a; }


/* Botón */
.btn{
    background:#004aad;
    color:white;
    padding:8px 16px;
    border-radius:6px;
    text-decoration:none;
    font-size:14px;
    border:none;
    cursor:pointer;
    transition:0.2s;
}
.btn:hover{
    background:#00337a;
}

/* Inputs */
textarea, input[type=file]{
    width:100%;
    padding:10px;
    border-radius:6px;
    border:1px solid #ccc;
    font-size:14px;
    margin-top:8px;
}

/* Responsive */
@media(max-width:768px){
    .data-row{ flex-direction:column; }
    .data-row div{ width:100%; }
    .doc-item{ flex-direction:column; align-items:flex-start; }
    .btn{ margin-top:8px; }
}


</style>
</head>

<body>

<?php include "../inc/header.php"; ?>
<?php include "../inc/sidebar.php"; ?>

<div class="content">
<div class="container-box">

<h1 class="title">Detalle del Caso</h1>
<!-- BOTONES DE ACCIONES SUPERIORES -->
<div class="top-actions">
    <a href="../php/generar_reporte.php?id=<?= $idCaso ?>" target="_blank" class="action-btn dark">
        📄 Generar Reporte PDF
    </a>

    <a href="../php/generar_factura.php?id=<?= $idCaso ?>" target="_blank" class="action-btn dark">
        🧾 Generar Factura
    </a>
</div>

<style>
.top-actions{
    display:flex;
    justify-content:flex-end;
    gap:12px;
    margin-bottom:20px;
}

.action-btn{
    padding:10px 18px;
    border-radius:6px;
    color:white;
    text-decoration:none;
    font-size:14px;
    font-weight:600;
    transition:0.2s;
    display:inline-flex;
    align-items:center;
    gap:6px;
}

.action-btn.dark{
    background:#2c3e50;
}
.action-btn.dark:hover{
    background:#1b2836;
}

.action-btn.grey{
    background:#7f8c8d;
}
.action-btn.grey:hover{
    background:#586466;
}

/* RESPONSIVO */
@media(max-width:768px){
    .top-actions{
        flex-direction:column;
        align-items:stretch;
    }
    .action-btn{
        width:100%;
        justify-content:center;
    }
}
</style>



<!-- ===========================
     INFORMACIÓN DEL CASO
=========================== -->
<div class="section-title">Información General</div>

<div class="data-row">
  <div><b>No. Caso:</b> <?= $caso['No_cs'] ?></div>
  <div><b>Tipo:</b> <?= $caso['Tipo_cs'] ?></div>
</div>

<div class="data-row">
  <div>
    <b>Estado:</b>
    <span class="badge badge-<?= strtolower($caso['Estado_cs']) ?>">
      <?= $caso['Estado_cs'] ?>
    </span>
  </div>
  <div><b>Fecha Inicio:</b> <?= $caso['Fecha_ini'] ?></div>
</div>

<div class="data-row">
  <div><b>Última actualización:</b> <?= $caso['Fecha_act'] ?? "—" ?></div>
</div>

<div class="data-row">
  <div><b>Cliente:</b> <?= $caso['Nom_cl_ct'] . " " . $caso['App_cl_ct'] ?></div>
  <div><b>Abogado:</b> <?= $caso['Nom_abgd_ct'] . " " . $caso['App_abgd_ct'] ?></div>
</div>

<div class="data-row">
  <div><b>Descripción:</b><br><?= nl2br($caso['Desc_cs']) ?></div>
</div>


<!-- ===========================
     NOTAS DEL CASO
=========================== -->
<div class="section-title">Notas del Abogado</div>

<ul class="timeline">
<?php while ($n = $notas->fetch_assoc()): ?>
  <li>
    <div class="fecha"><?= $n['fecha'] ?> — <b><?= $n['Nom_abgd'] ?></b></div>
    <div class="nota">
    <?= nl2br($n['nota']) ?>

    <div style="text-align:right; margin-top:6px;">
        <a href="../php/eliminar_nota.php?id=<?= $n['Id_nota'] ?>&caso=<?= $idCaso ?>"
           class="btn btn-red"
           style="padding:4px 10px;font-size:12px;"
           onclick="return confirm('¿Eliminar nota?')">
            Eliminar
        </a>
    </div>
</div>

  </li>
<?php endwhile; ?>
</ul>

<!-- Agregar nueva nota -->
<form action="../php/agregar_nota.php" method="POST">
  <input type="hidden" name="id_caso" value="<?= $idCaso ?>">
  <textarea name="nota" placeholder="Escribe una nota..." required></textarea>
  <br><br>
  <button class="btn"> Agregar nota</button>
</form>


<!-- ===========================
     DOCUMENTOS DEL CASO
=========================== -->
<div class="section-title">Documentos del Caso</div>

<?php if ($docs->num_rows === 0): ?>
  <p>No hay documentos aún.</p>
<?php else: ?>
  <?php while($d = $docs->fetch_assoc()): ?>
    <div class="doc-item">
        <div class="doc-left">
            📄 <b><?= $d['nombre_archivo'] ?></b>
            <small><?= $d['fecha_subida'] ?></small>
        </div>

        <div class="doc-item">

    <div class="doc-actions">
        <a class="btn btn-blue" href="<?= $d['ruta_archivo'] ?>" target="_blank">
            Ver
        </a>

        <a class="btn btn-green" href="../php/descargar_documento.php?id=<?= $d['Id_doc'] ?>">
            Descargar
        </a>

        <a class="btn btn-red" 
           href="../php/eliminar_documento.php?id=<?= $d['Id_doc'] ?>&caso=<?= $idCaso ?>"
           onclick="return confirm('¿Eliminar este documento?')">
            Eliminar
        </a>
    </div>
</div>

    </div>
<?php endwhile; ?>

<?php endif; ?>

<!-- Subir documento -->
<form action="../php/subir_documento.php" method="POST" enctype="multipart/form-data">
  <input type="hidden" name="id_caso" value="<?= $idCaso ?>">
  <br>
  <label><b>Subir documento:</b></label>
  <input type="file" name="archivo" required>
  <br><br>
  <button class="btn"> Subir documento</button>
</form>

</div>
</div>


</body>
</html>
