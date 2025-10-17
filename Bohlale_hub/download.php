<?php
// Show all errors (for debugging)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include FPDF library (make sure fpdf/fpdf.php exists in your project folder)
require(__DIR__ . '/fpdf/fpdf.php');

// Connect to MySQL
$conn = new mysqli("localhost", "root", "", "bohlale_hub");
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// Create PDF
$pdf = new FPDF();
$pdf->AddPage();

// Title
$pdf->SetFont('Arial','B',16);
$pdf->Cell(0,10,'Career Roadmap',0,1,'C');
$pdf->Ln(10);

// Fetch milestones from DB
$result = $conn->query("SELECT * FROM milestones ORDER BY created_at ASC");
if (!$result) {
    die("DB Error: " . $conn->error);
}

if ($result->num_rows > 0) {
    $pdf->SetFont('Arial','',12);
    while ($row = $result->fetch_assoc()) {
        $pdf->Cell(0,10,"- " . $row['title'],0,1);
    }
} else {
    $pdf->SetFont('Arial','I',12);
    $pdf->Cell(0,10,"No milestones found.",0,1);
}

// Output PDF as download
$pdf->Output("D","career_roadmap.pdf");
exit;
