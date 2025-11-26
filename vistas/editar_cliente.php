<?php
session_start();
if(!isset($_SESSION['rol']) || $_SESSION['rol'] != "abogado"){
    header("Location: ../vistas/login.php");
    exit();
}

if ((int)$_SESSION['es_admin'] !== 1) {
    echo "<script>alert('⚠️ No tienes permiso para editar clientes.'); window.location='clientes_registrados.php';</script>";
    exit();
}

include "../inc/conexion.php";

if (!isset($_GET['id'])) {
    echo "<script>alert('ID no recibido'); window.location='clientes_registrados.php';</script>";
    exit();
}

$id = (int)$_GET['id'];

$sql = "SELECT * FROM cliente WHERE Id_cl = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$cliente = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$cliente) {
    echo "<script>alert('Cliente no encontrado'); window.location='clientes_registrados.php';</script>";
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Editar Cliente</title>
<link rel="stylesheet" href="../css/estilo_panel.css">

<style>
.form-box {
    background:white;
    padding:25px;
    max-width:600px;
    margin:auto;
    border:1px solid #ccc;
    border-radius:10px;
}
label {
    font-weight:bold;
    margin-top:10px;
    display:block;
}
input {
    width:100%;
    padding:10px;
    margin-top:5px;
    border-radius:6px;
    border:1px solid #ccc;
}
button {
    background:#004aad;
    color:white;
    padding:12px;
    border:none;
    border-radius:6px;
    width:100%;
    margin-top:20px;
    cursor:pointer;
}
button:hover {
    background:#00337a;
}
</style>
</head>

<body>

<?php include "../inc/header.php"; ?>

<div class="sidebar">
    <a href="clientes_registrados.php">Clientes Registrados</a>
</div>

<div class="content">
<h1 class="title">Editar Cliente</h1>

<div class="form-box">
<form action="../php/actualizar_cliente.php" method="POST">

<input type="hidden" name="id" value="<?= $cliente['Id_cl']; ?>">

<label>Nombre</label>
<input type="text" name="nom" value="<?= $cliente['Nom_cl']; ?>" required>

<label>Apellido Paterno</label>
<input type="text" name="app" value="<?= $cliente['App_cl']; ?>" required>

<label>Apellido Materno</label>
<input type="text" name="apm" value="<?= $cliente['Apm_cl']; ?>" required>

<label>Correo</label>
<input type="email" name="correo" value="<?= $cliente['Cor_cl']; ?>" required>

<label>Teléfono</label>
<input type="text" name="tel" value="<?= $cliente['tel_cl']; ?>" required>

<label>RFC</label>
<input type="text" name="rfc" value="<?= $cliente['Rfc_cl']; ?>" required>

<label>Dirección</label>
<input type="text" name="dir" value="<?= $cliente['Dir_cl']; ?>" required>

<button type="submit">Guardar Cambios</button>

</form>
</div>

</div>
</body>
</html>
