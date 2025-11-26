<?php
session_start();
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'abogado') {
    die("Acceso denegado");
}

require_once __DIR__ . '/../inc/conexion.php';
require_once __DIR__ . '/../librerias/fpdf/fpdf.php';

$idAbogado = $_SESSION['Id_abgd'];

/* =========================
   CONSULTAR CITAS DEL ABOGADO
========================= */
$sql = "SELECT Da_ct, Hra_ct, Nom_cl_ct, App_cl_ct, Apm_cl_ct
        FROM cita
        WHERE abgd_id_ct = ?
        ORDER BY Da_ct, Hra_ct";

$stmt = $conexion->prepare($sql);
$stmt->bind_param('i', $idAbogado);
$stmt->execute();
$citas = $stmt->get_result();

/* =========================
   CREAR PDF PROFESIONAL
========================= */

$pdf = new FPDF("P", "mm", "Letter");
$pdf->AddPage();

// Encabezado
$pdf->SetFont("Arial", "B", 16);
$pdf->Cell(0, 10, "Reporte de Citas - Garcia & Asociados", 0, 1, "C");
$pdf->Ln(2);

$pdf->SetFont("Arial", "", 12);
$pdf->Cell(0, 7, "Abogado: " . $_SESSION['Nom_abgd'] . " " . $_SESSION['App_abgd'], 0, 1);
$pdf->Cell(0, 7, "Fecha de emision: " . date("Y-m-d H:i"), 0, 1);
$pdf->Ln(5);

// Títulos de tabla
$pdf->SetFont("Arial", "B", 12);
$pdf->SetFillColor(230, 230, 230);
$pdf->Cell(40, 8, "Fecha", 1, 0, "C", true);
$pdf->Cell(30, 8, "Hora", 1, 0, "C", true);
$pdf->Cell(120, 8, "Cliente", 1, 1, "C", true);

$pdf->SetFont("Arial", "", 12);

// Contenido
if ($citas->num_rows === 0) {
    $pdf->Cell(0, 10, "No hay citas registradas.", 1, 1, "C");
} else {
    while ($c = $citas->fetch_assoc()) {
        $pdf->Cell(40, 8, $c['Da_ct'], 1, 0, "C");
        $pdf->Cell(30, 8, $c['Hra_ct'], 1, 0, "C");

        $cliente = $c['Nom_cl_ct'] . " " . $c['App_cl_ct'] . " " . $c['Apm_cl_ct'];
        $pdf->Cell(120, 8, utf8_decode($cliente), 1, 1, "L");
    }
}

$pdf->Output("I", "reporte_citas.pdf");
exit;
?>
