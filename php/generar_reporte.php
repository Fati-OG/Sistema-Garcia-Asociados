<?php
session_start();
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'abogado') {
    die("Acceso denegado");
}

require_once __DIR__ . '/../inc/conexion.php';
require_once __DIR__ . '/../librerias/fpdf/fpdf.php';

$idCaso = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($idCaso <= 0) { die("ID inválido"); }

/* ==========================
   OBTENER DATOS DEL CASO
========================== */
$sql = "SELECT * FROM casos WHERE Id_cs = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $idCaso);
$stmt->execute();
$caso = $stmt->get_result()->fetch_assoc();

if (!$caso) { die("Caso no encontrado"); }

/* ==========================
   OBTENER NOTAS
========================== */
$sqlNotas = "SELECT n.*, a.Nom_abgd, a.App_abgd
             FROM notas_caso n
             JOIN abogado a ON n.Id_abgd = a.Id_abgd
             WHERE n.Id_cs = ?
             ORDER BY n.fecha DESC";
$stmtNotas = $conexion->prepare($sqlNotas);
$stmtNotas->bind_param("i", $idCaso);
$stmtNotas->execute();
$notas = $stmtNotas->get_result();

/* ==========================
   OBTENER DOCUMENTOS
========================== */
$sqlDocs = "SELECT * FROM documentos_caso WHERE Id_cs = ?";
$stmtDocs = $conexion->prepare($sqlDocs);
$stmtDocs->bind_param("i", $idCaso);
$stmtDocs->execute();
$docs = $stmtDocs->get_result();

/* ==========================
   CREAR PDF
========================== */

$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetMargins(20, 20, 20);

/* ---------- ENCABEZADO ---------- */
$pdf->SetFont("Arial", "B", 16);
$pdf->Cell(0, 10, "GARCIA & ASOCIADOS - REPORTE DE CASO", 0, 1, "C");
$pdf->Ln(2);

$pdf->SetDrawColor(80, 80, 80);
$pdf->Line(20, 32, 190, 32);
$pdf->Ln(8);

/* ---------- DATOS GENERALES ---------- */
$pdf->SetFont("Arial", "B", 13);
$pdf->Cell(0, 10, "1. Informacion General del Caso", 0, 1);

$pdf->SetFont("Arial", "", 12);
$pdf->Cell(0, 8, "Numero de Caso: " . $caso['No_cs'], 0, 1);
$pdf->Cell(0, 8, "Tipo: " . $caso['Tipo_cs'], 0, 1);
$pdf->Cell(0, 8, "Estado: " . $caso['Estado_cs'], 0, 1);
$pdf->Cell(0, 8, "Fecha inicio: " . $caso['Fecha_ini'], 0, 1);
$pdf->Cell(0, 8, "Ultima actualizacion: " . ($caso['Fecha_act'] ?: "—"), 0, 1);

$pdf->Ln(4);
$pdf->SetFont("Arial", "B", 12);
$pdf->Cell(0, 8, "Descripcion:", 0, 1);

$pdf->SetFont("Arial", "", 12);
$pdf->MultiCell(0, 7, $caso['Desc_cs']);
$pdf->Ln(3);


/* ---------- NOTAS ---------- */
$pdf->SetFont("Arial", "B", 13);
$pdf->Cell(0, 10, "2. Notas del Abogado", 0, 1);

$pdf->SetFont("Arial", "", 12);

if ($notas->num_rows === 0) {
    $pdf->Cell(0, 8, "- No hay notas registradas", 0, 1);
} else {
    while ($n = $notas->fetch_assoc()) {
        $pdf->SetFont("Arial", "B", 11);
        $pdf->Cell(0, 7, $n['fecha'] . " - " . $n['Nom_abgd'] . " " . $n['App_abgd'], 0, 1);

        $pdf->SetFont("Arial", "", 12);
        $pdf->MultiCell(0, 7, $n['nota']);
        $pdf->Ln(2);
    }
}

$pdf->Ln(5);

/* ---------- DOCUMENTOS ---------- */
$pdf->SetFont("Arial", "B", 13);
$pdf->Cell(0, 10, "3. Documentos Asociados", 0, 1);

$pdf->SetFont("Arial", "", 12);

if ($docs->num_rows === 0) {
    $pdf->Cell(0, 8, "- No hay documentos", 0, 1);
} else {
    while ($d = $docs->fetch_assoc()) {
        $pdf->Cell(0, 8, "• " . $d['nombre_archivo'], 0, 1);
    }
}

/* ---------- PIE DE DOCUMENTO ---------- */
$pdf->Ln(10);
$pdf->SetFont("Arial", "I", 9);
$pdf->SetTextColor(120, 120, 120);
$pdf->Cell(0, 8, "Reporte generado automaticamente por el Sistema RADCS.", 0, 1, "C");

$pdf->Output("I", "reporte_caso_{$idCaso}.pdf");
exit;

?>
