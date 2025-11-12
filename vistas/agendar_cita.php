<?php
session_start();
if(!isset($_SESSION['rol']) || $_SESSION['rol'] != "abogado"){
    header("Location: ../vistas/login.php");
    exit();
}

include "../inc/conexion.php";
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Agendar Cita</title>
<link rel="stylesheet" href="../css/estilo_panel.css">
<style>
.form-box {
    background: white;
    padding: 20px;
    border: 1px solid #dcd6c8;
    border-radius: 8px;
    max-width: 600px;
    margin: auto;
}
label {
    display: block;
    margin-top: 10px;
    font-weight: bold;
}
input, select {
    width: 100%;
    padding: 8px;
    margin-top: 5px;
    border-radius: 5px;
    border: 1px solid #ccc;
}
button {
    background-color: #004aad;
    color: white;
    padding: 10px 15px;
    border: none;
    border-radius: 5px;
    margin-top: 15px;
    cursor: pointer;
}
button:hover {
    background-color: #00337a;
}
</style>
</head>

<body>

<div class="header">
    <span>⚖️ García & Asociados</span>
    <span>Abogado: <?php echo $_SESSION['Nom_abgd']." ".$_SESSION['App_abgd']; ?></span>
</div>

<div class="sidebar">
    <a href="panel_abogado.php">Inicio</a>
    <a href="citas_abogado.php">Mis Citas</a>
    <a href="agendar_cita.php">Agendar Cita</a>
    <a href="../php/logout.php">Cerrar Sesión</a>
</div>

<div class="content">
    <h1 class="title">Agendar nueva cita</h1>

    <div class="form-box">
        <form action="../php/guardar_cita.php" method="POST">
            <label for="cliente">Selecciona Cliente:</label>
            <select name="cliente" required>
                <option value="">--Selecciona--</option>
                <?php
                $clientes = $conexion->query("SELECT Id_cl, Nom_cl, App_cl, Apm_cl FROM cliente");
                while($c = $clientes->fetch_assoc()){
                    echo "<option value='".$c['Id_cl']."'>".$c['Nom_cl']." ".$c['App_cl']." ".$c['Apm_cl']."</option>";
                }
                ?>
            </select>

            <label>Fecha:</label>
            <input type="date" name="fecha" required>

            <label>Hora:</label>
            <input type="time" name="hora" required>

            <button type="submit">Guardar Cita</button>
        </form>
    </div>
</div>

</body>
</html>
