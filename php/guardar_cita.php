<?php
session_start();
include "../inc/conexion.php";

if(!isset($_SESSION['rol']) || $_SESSION['rol'] != "abogado"){
    header("Location: ../vistas/login.php");
    exit();
}

$id_cliente = $_POST['cliente'];
$fecha = $_POST['fecha'];
$hora = $_POST['hora'];

$nom_abg = $_SESSION['Nom_abgd'];
$app_abg = $_SESSION['App_abgd'];
$apm_abg = $_SESSION['Apm_abgd'];

// Obtener datos del cliente
$sql_cliente = "SELECT * FROM cliente WHERE Id_cl = $id_cliente LIMIT 1";
$res = $conexion->query($sql_cliente);
$cliente = $res->fetch_assoc();

// Insertar la cita
$sql_insert = "INSERT INTO cita (Hra_ct, Da_ct, Id_cl_ct, Nom_cl_ct, App_cl_ct, Apm_cl_ct, abgd_id_ct, Nom_abgd_ct, App_abgd_ct, Apm_abgd_ct)
VALUES ('$hora', '$fecha', '$id_cliente', '".$cliente['Nom_cl']."', '".$cliente['App_cl']."', '".$cliente['Apm_cl']."', NULL, '$nom_abg', '$app_abg', '$apm_abg')";

if($conexion->query($sql_insert)){
    header("Location: ../vistas/citas_abogado.php");
    exit();
}else{
    die("Error al guardar cita: " . $conexion->error);
}
?>
