<?php
session_start();
if(!isset($_SESSION['rol']) || $_SESSION['rol'] != "abogado" || (int)$_SESSION['es_admin'] !== 1){
    header("Location: ../vistas/login.php");
    exit();
}

include "../inc/conexion.php";

if(!isset($_GET['id'])){
    header("Location: abogados_registrados.php?msg=error_id");
    exit();
}

$id = (int)$_GET['id'];

$sql = "SELECT * FROM abogado WHERE Id_abgd = ? LIMIT 1";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if($result->num_rows == 0){
    header("Location: abogados_registrados.php?msg=no_found");
    exit();
}

$ab = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Editar Abogado</title>
<link rel="stylesheet" href="../css/estilo_panel.css">

<style>
.form-box{
    background: white;
    padding: 20px;
    border:1px solid #dcd6c8;
    border-radius:8px;
    max-width:600px;
    margin:auto;
}
label{
    font-weight:bold;
    margin-top:10px;
    display:block;
}
input{
    width:100%;
    padding:8px;
    margin-top:5px;
    border-radius:5px;
    border:1px solid #ccc;
}
button{
    background:#004aad;
    color:white;
    padding:10px 15px;
    border:none;
    border-radius:5px;
    margin-top:15px;
    cursor:pointer;
}
button:hover{ background:#00337a; }
</style>

</head>

<body>

<div class="header">
    <span>⚖️ García & Asociados</span>
    <span>Administrador: <?= $_SESSION['Nom_abgd']." ".$_SESSION['App_abgd']; ?></span>
</div>

<div class="sidebar">
    <a href="panel_abogado.php">Inicio</a>
    <a href="abogados_registrados.php">Volver</a>
</div>

<div class="content">
<h1 class="title">Editar Abogado</h1>

<div class="form-box">

<form action="../php/actualizar_abogado.php" method="POST">
    
    <input type="hidden" name="id" value="<?= $ab['Id_abgd']; ?>">

    <label>Nombre</label>
    <input type="text" name="nombre" value="<?= $ab['Nom_abgd']; ?>" required>

    <label>Apellido Paterno</label>
    <input type="text" name="ap_pat" value="<?= $ab['App_abgd']; ?>" required>

    <label>Apellido Materno</label>
    <input type="text" name="ap_mat" value="<?= $ab['Apm_abgd']; ?>" required>

    <label>Dirección</label>
    <input type="text" name="dir" value="<?= $ab['Dir_abgd']; ?>" required>

    <label>Celular</label>
    <input type="text" name="cel" value="<?= $ab['Cel_abgd']; ?>" required>

    <label>Teléfono</label>
    <input type="text" name="tel" value="<?= $ab['Tel_abgd']; ?>" required>

    <label>Correo</label>
    <input type="email" name="correo" value="<?= $ab['Cor_abgd']; ?>" required>

    <label>Contraseña (opcional)</label>
    <input type="password" name="password" placeholder="Dejar vacío si no cambiarás">

    <button type="submit">Guardar Cambios</button>

</form>

</div>
</div>

</body>
</html>
