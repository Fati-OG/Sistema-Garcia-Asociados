<?php
session_start();
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'abogado') {
    die("Acceso denegado");
}

require_once __DIR__ . '/../inc/conexion.php';
require_once __DIR__ . '/../librerias/fpdf/fpdf.php';

$idCaso = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($idCaso <= 0) { die("ID inválido."); }

/* =====================
   Datos del caso
===================== */
$sql = "SELECT * FROM casos WHERE Id_cs = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $idCaso);
$stmt->execute();
$caso = $stmt->get_result()->fetch_assoc();
if (!$caso) { die("Caso no encontrado."); }

$idCliente = $caso['Id_cl_ct'];
$idAbogado = $_SESSION['Id_abgd'];

/* Cliente */
$sqlCl = "SELECT Nom_cl, App_cl, Apm_cl FROM cliente WHERE Id_cl = ?";
$stmtCl = $conexion->prepare($sqlCl);
$stmtCl->bind_param("i", $idCliente);
$stmtCl->execute();
$cl = $stmtCl->get_result()->fetch_assoc();

/* Abogado */
$sqlAb = "SELECT Nom_abgd, App_abgd, Apm_abgd FROM abogado WHERE Id_abgd = ?";
$stmtAb = $conexion->prepare($sqlAb);
$stmtAb->bind_param("i", $idAbogado);
$stmtAb->execute();
$ab = $stmtAb->get_result()->fetch_assoc();

/* =====================
   Concepto y monto
===================== */
$concepto = "Servicios legales — Caso {$caso['No_cs']}";
$monto = 1000.00;

/* =====================
   Carpeta facturas
===================== */
$dir = __DIR__ . '/../facturas/';
if (!is_dir($dir)) mkdir($dir, 0777, true);

/* =====================
   Crear PDF (Helvetica)
===================== */
$pdf = new FPDF('P', 'mm', 'A4');
$pdf->AddPage();

$pdf->SetFont("Helvetica", "B", 16);
$pdf->Cell(0, 10, utf8_decode("GARCIA & ASOCIADOS - FACTURA DE SERVICIOS LEGALES"), 0, 1, "C");
$pdf->Ln(5);

$pdf->SetFont("Helvetica", "", 12);
$pdf->Cell(0, 8, utf8_decode("Caso: {$caso['No_cs']}"), 0, 1);
$pdf->Cell(0, 8, utf8_decode("Cliente: {$cl['Nom_cl']} {$cl['App_cl']} {$cl['Apm_cl']}"), 0, 1);
$pdf->Cell(0, 8, utf8_decode("Abogado: {$ab['Nom_abgd']} {$ab['App_abgd']} {$ab['Apm_abgd']}"), 0, 1);
$pdf->Cell(0, 8, utf8_decode("Fecha: " . date("Y-m-d")), 0, 1);
$pdf->Ln(10);

/* Concepto */
$pdf->SetFont("Helvetica", "B", 14);
$pdf->Cell(0, 10, utf8_decode("Concepto:"), 0, 1);
$pdf->SetFont("Helvetica", "", 12);
$pdf->MultiCell(0, 8, utf8_decode($concepto));

$pdf->Ln(5);

/* Monto */
$pdf->SetFont("Helvetica", "B", 14);
$pdf->Cell(0, 10, utf8_decode("Monto Total: $") . number_format($monto, 2), 0, 1);

/* Guardar PDF */
$nombrePDF = "factura_{$idCaso}_" . time() . ".pdf";
$rutaPDF   = $dir . $nombrePDF;

$pdf->Output("F", $rutaPDF);

/* =====================
   Guardar en BD
===================== */
$sqlIns = "INSERT INTO facturas (Id_cs, Id_cl, Id_abgd, concepto, monto, archivo_pdf)
           VALUES (?, ?, ?, ?, ?, ?)";

$stmtIns = $conexion->prepare($sqlIns);
$stmtIns->bind_param(
    "iiisds",
    $idCaso,
    $idCliente,
    $idAbogado,
    $concepto,
    $monto,
    $nombrePDF
);
$stmtIns->execute();

/* =====================
   Redirigir al PDF
===================== */
header("Location: ../facturas/$nombrePDF");
exit;

?>
