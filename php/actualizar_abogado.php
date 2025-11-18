<?php
session_start();
include "../inc/conexion.php";

if(!isset($_SESSION['rol']) || $_SESSION['rol'] != "abogado" || (int)$_SESSION['es_admin'] !== 1){
    header("Location: ../vistas/login.php");
    exit();
}

if(!isset($_POST['id'])){
    header("Location: ../vistas/abogados_registrados.php?msg=error_id");
    exit();
}

$id = (int)$_POST['id'];
$nombre = $_POST['nombre'];
$app = $_POST['ap_pat'];
$apm = $_POST['ap_mat'];
$dir = $_POST['dir'];
$cel = $_POST['cel'];
$tel = $_POST['tel'];
$correo = $_POST['correo'];
$pass = $_POST['password'];

if($pass == ""){
    // no actualizar contraseña
    $sql = "UPDATE abogado SET 
            Nom_abgd=?, App_abgd=?, Apm_abgd=?, Dir_abgd=?, 
            Cel_abgd=?, Tel_abgd=?, Cor_abgd=? 
            WHERE Id_abgd=?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("sssssssi", $nombre, $app, $apm, $dir, $cel, $tel, $correo, $id);

} else {
    // actualizar con contraseña
    $sql = "UPDATE abogado SET 
            Nom_abgd=?, App_abgd=?, Apm_abgd=?, Dir_abgd=?, 
            Cel_abgd=?, Tel_abgd=?, Cor_abgd=?, Con_abgd=? 
            WHERE Id_abgd=?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("ssssssssi", $nombre, $app, $apm, $dir, $cel, $tel, $correo, $pass, $id);
}

if($stmt->execute()){
    header("Location: ../vistas/abogados_registrados.php?msg=edit_ok");
} else {
    header("Location: ../vistas/abogados_registrados.php?msg=edit_err");
}
exit();
