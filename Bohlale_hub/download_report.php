<?php
require('fpdf/fpdf.php'); // Only this, no composer needed

$conn = new mysqli("localhost", "root", "", "bohlale_hub");
if ($conn->connect_error) die("DB Connection failed: " . $conn->connect_error);

$industry = isset($_GET['industry']) ? $_GET['industry'] : '';
$sql = "SELECT * FROM industries WHERE name = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $industry);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    $pdf = new FPDF();
    $pdf->AddPage();

    $pdf->SetFont('Arial', 'B', 16);
    $pdf->Cell(0, 10, 'Industry Report - ' . $row['name'], 0, 1, 'C');
    $pdf->Ln(10);

    $pdf->SetFont('Arial', '', 12);
    $pdf->Cell(0, 10, 'Monthly Reports: ' . $row['reports'], 0, 1);
    $pdf->Cell(0, 10, 'Top Skills: ' . $row['top_skills'], 0, 1);
    $pdf->Cell(0, 10, 'Job Market: ' . $row['job_market'], 0, 1);

    $pdf->Ln(10);
    $pdf->SetFont('Arial', 'I', 10);
    $pdf->Cell(0, 10, 'Generated on ' . date("Y-m-d H:i:s"), 0, 1, 'C');

    $pdf->Output('D', $row['name'] . '_Report.pdf');
} else {
    echo "Industry not found.";
}

$stmt->close();
$conn->close();
?>
