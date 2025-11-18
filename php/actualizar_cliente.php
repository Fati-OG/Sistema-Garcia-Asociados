<?php
session_start();
include "../inc/conexion.php";

if(!isset($_SESSION['rol']) || $_SESSION['rol'] != "abogado" || (int)$_SESSION['es_admin'] !== 1){
    header("Location: ../vistas/login.php");
    exit();
}

$id   = $_POST['id'];
$nom  = $_POST['nom'];
$app  = $_POST['app'];
$apm  = $_POST['apm'];
$cor  = $_POST['correo'];
$tel  = $_POST['tel'];
$rfc  = $_POST['rfc'];
$dir  = $_POST['dir'];

$sql = "UPDATE cliente 
        SET Nom_cl=?, App_cl=?, Apm_cl=?, Cor_cl=?, tel_cl=?, Rfc_cl=?, Dir_cl=?
        WHERE Id_cl=?";

$stmt = $conexion->prepare($sql);
$stmt->bind_param("sssssssi", $nom, $app, $apm, $cor, $tel, $rfc, $dir, $id);

if ($stmt->execute()) {
    header("Location: ../vistas/clientes_registrados.php?msg=edit_ok");
} else {
    header("Location: ../vistas/clientes_registrados.php?msg=edit_err");
}
$stmt->close();
exit();
?>
