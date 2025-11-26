<?php
session_start();
require_once __DIR__ . '/../inc/conexion.php';

$correo   = isset($_POST['correo'])   ? trim($_POST['correo'])   : '';
$password = isset($_POST['password']) ? trim($_POST['password']) : '';

if ($correo === '' || $password === '') {
    header('Location: ../vistas/login.php?error=1'); exit;
}

/* 1) Intentar como ABOGADO */
$sql = "SELECT Id_abgd, Nom_abgd, App_abgd, Apm_abgd, Cor_abgd, Con_abgd, es_admin
        FROM abogado
        WHERE Cor_abgd = ? AND Con_abgd = ? LIMIT 1";

$stmt = $conexion->prepare($sql);
if (!$stmt) {
    die("Error SQL (abogado->prepare): " . $conexion->error);
}
$stmt->bind_param('ss', $correo, $password);
$stmt->execute();
$res = $stmt->get_result();

if ($row = $res->fetch_assoc()) {
    $_SESSION['rol']       = 'abogado';
    $_SESSION['Id_abgd']   = (int)$row['Id_abgd'];
    $_SESSION['Nom_abgd']  = $row['Nom_abgd'];
    $_SESSION['App_abgd']  = $row['App_abgd'];
    $_SESSION['Apm_abgd']  = $row['Apm_abgd'];
    $_SESSION['usuario']   = $row['Nom_abgd'];
    $_SESSION['es_admin']  = (int)$row['es_admin']; // 👈 FIX REAL


    // 🔍 Verificación temporal
    // var_dump($_SESSION); exit();

    header('Location: ../vistas/panel_abogado.php');
    exit();
}
$stmt->close();



/* 2) Intentar como CLIENTE */
$sql = "SELECT Id_cl, Nom_cl, App_cl, Apm_cl, Cor_cl, Con_cli
        FROM cliente
        WHERE Cor_cl = ? AND Con_cli = ? LIMIT 1";
if (!$stmt = $conexion->prepare($sql)) {
    die("Error SQL (cliente->prepare): " . $conexion->error);
}
$stmt->bind_param('ss', $correo, $password);
$stmt->execute();
$res = $stmt->get_result();
if ($row = $res->fetch_assoc()) {
    $_SESSION['rol']     = 'cliente';
    $_SESSION['id_cl']   = (int)$row['Id_cl'];
    $_SESSION['Nom_cl']  = $row['Nom_cl'];
    $_SESSION['App_cl']  = $row['App_cl'];
    $_SESSION['Apm_cl']  = $row['Apm_cl'];
    $_SESSION['usuario'] = $row['Nom_cl']; // si lo usas en algún header
    $stmt->close();
    header('Location: ../vistas/panel_cliente.php'); exit;
}
$stmt->close();

/* 3) Si no coincide */
header('Location: ../vistas/login.php?error=1'); exit;
