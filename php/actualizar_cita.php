<?php
session_start();
include "../inc/conexion.php";

if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== "abogado") {
    header("Location: ../vistas/login.php");
    exit();
}

if (!isset($_POST['id_cita'])) {
    header("Location: ../vistas/citas_abogado.php?msg=error_id");
    exit();
}

$id = (int)$_POST['id_cita'];
$fecha = $_POST['fecha'];
$hora = $_POST['hora'];

$sql = "UPDATE cita SET Da_ct = ?, Hra_ct = ? WHERE Id_ct = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("ssi", $fecha, $hora, $id);

if ($stmt->execute()) {
    header("Location: ../vistas/citas_abogado.php?msg=edit_ok");
} else {
    header("Location: ../vistas/citas_abogado.php?msg=edit_err");
}
exit();
?>
