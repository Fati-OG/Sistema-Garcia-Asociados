<?php
session_start();
include "../inc/conexion.php";

if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== "abogado") {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['id'])) {
    die("ID de cita no recibido");
}

$id = (int)$_GET['id'];

// Obtener datos actuales
$sql = "SELECT Id_ct, Da_ct, Hra_ct FROM cita WHERE Id_ct = ? LIMIT 1";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$res = $stmt->get_result();
$cita = $res->fetch_assoc();

if (!$cita) {
    die("Cita no encontrada.");
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Editar Cita</title>
<link rel="stylesheet" href="../css/estilo_panel.css">
<style>
.form-box {
    background: white;
    padding: 25px;
    border: 1px solid #dcd6c8;
    border-radius: 8px;
    width: 400px;
    margin: auto;
}
label { font-weight: bold; margin-top: 10px; display:block; }
input { width:100%; padding:8px; border:1px solid #bbb; border-radius:5px; margin-top:5px; }
button {
    margin-top: 15px;
    width:100%;
    background:#004aad;
    color:white;
    padding:10px;
    border:none;
    border-radius:5px;
    cursor:pointer;
}
button:hover { background:#00337a; }
</style>
</head>
<body>

<div class="header">
  <span>⚖️ García & Asociados</span>
  <span>Abogado: <?= $_SESSION['Nom_abgd'] ?></span>
</div>

<div class="sidebar">
    <a href="panel_abogado.php">Inicio</a>
    <a href="citas_abogado.php">Mis Citas</a>
    <a href="agendar_cita.php">Agendar Cita</a>
    <a href="registro_cliente.php">Registrar Cliente</a>
</div>

<div class="content">
<h1 class="title">Editar Cita</h1>

<div class="form-box">
    <form action="../php/actualizar_cita.php" method="POST">
        <input type="hidden" name="id_cita" value="<?= $cita['Id_ct'] ?>">

        <label>Fecha:</label>
        <input type="date" name="fecha" value="<?= $cita['Da_ct'] ?>">

        <label>Hora:</label>
        <input type="time" name="hora" value="<?= $cita['Hra_ct'] ?>">

        <button type="submit">Guardar Cambios</button>
    </form>
</div>

</div>
</body>
</html>
