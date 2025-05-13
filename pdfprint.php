<?php
session_start();
include "Procedures.php";
$proc = new Procedures();
$conn = $proc->getConnection();
require_once('fpdf.php');

$stmt = $conn->prepare("CALL get_attendance_details(?, ?, ?)");
$lecturer_id = $_SESSION['id'];
$classID = $_SESSION['classID'];
$date = $_SESSION['date'];
$stmt->bind_param("iis", $lecturer_id, $classID, $date);
$stmt->execute();
$attendance_result = $stmt->get_result();
$proc->drain($conn);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    class PDF extends FPDF {}

    $pdf = new PDF();
    $pdf->AddPage();

    if ($attendance_result->num_rows > 0) {
        $first_row = $attendance_result->fetch_assoc();
        $subject_name = $first_row['subject_name'];
        $subject_code = $first_row['subject_code'];
        $section = $first_row['section'];
        $attendance_date = date('F d, Y', strtotime($first_row['attendance_date']));

        $attendance_result->data_seek(0);

        $total_width = 590;
        $page_width = $pdf->GetPageWidth();
        $left_margin = ($page_width - $total_width) / 2;

        //header
        $pdf->SetFont('Times', '', 10);
        $pdf->Image('bsu_logo.png', 8, 5, 40);
        $pdf->Image('cics_logo.png', 162, 4, 40);

        $pdf->SetFont('Times', 'B', 16);
        $pdf->Cell(0, 6, 'BATANGAS STATE UNIVERSITY', 0, 1, 'C');
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->SetTextColor(255, 0, 0);
        $pdf->Cell(0, 6, 'The National Engineering University', 0, 1, 'C');
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('Times', 'B', 12);
        $pdf->Cell(0, 6, 'Lipa Campus', 0, 1, 'C');
        $pdf->Ln(4);
        $pdf->SetFont('Times', 'B', 12);
        $pdf->Cell(0, 6, 'STUDENT CLASS ATTENDANCE', 0, 1, 'C');
        $pdf->Cell(0, 6, 'AY. 2024-2025', 0, 1, 'C');
        $pdf->Ln(10);

        $pdf->SetFont('Times', '', 12);
        $pdf->Cell(95, 6, "Lecturer: " . htmlspecialchars($_SESSION['name']), 0, 0, 'L');
        $pdf->Cell(0, 6, "Date: " . $attendance_date, 0, 1, 'R');
        $pdf->Cell(0, 6, "Course Code and Title: " . $subject_code. " " . $subject_name , 0, 0, 'L');
        $pdf->Cell(0, 6, "Time: " . "                     ", 0, 1, 'R');
        $pdf->Cell(0, 6, "Section: $section", 0, 0, 'L');
        $pdf->Cell(0, 6, "Room: " . "                    ", 0, 1, 'R');
        $pdf->Ln(5);

        // Fetch all rows
        $students = [];
        while ($row = $attendance_result->fetch_assoc()) {
            $students[] = $row;
        }

        // Column layout settings
        $rowHeight = 5;
        $maxY = 275;
        $colWidth1 = 60;
        $colWidth2 = 25;
        $startX_left = $left_margin;
        $startX_right = $startX_left + $colWidth1 + $colWidth2;
        $startY = $pdf->GetY();

        $pdf->SetFont('Times', 'B', 10);
        $pdf->SetFillColor(240, 240, 240);
        $pdf->SetX($startX_left);
        $pdf->Cell($colWidth1, 7, 'Student Name', 1, 0, 'C', 1);
        $pdf->Cell($colWidth2, 7, 'Status', 1, 1, 'C', 1);

        $pdf->SetFont('Times', '', 10);
        $pdf->SetY($pdf->GetY());
        $num = 1;
        $column = 'left';

        foreach ($students as $row) {
            if ($pdf->GetY() + $rowHeight > $maxY && $column === 'left') {
                $column = 'right';
                $pdf->SetXY($startX_right, $startY);
                $pdf->SetFont('Times', 'B', 10);
                $pdf->SetFillColor(240, 240, 240);
                $pdf->Cell($colWidth1, 7, 'Student Name', 1, 0, 'C', 1);
                $pdf->Cell($colWidth2, 7, 'Status', 1, 1, 'C', 1);
                $pdf->SetFont('Times', '', 10);
            }

            if ($column === 'left') {
                $pdf->SetX($startX_left);
            } else {
                $pdf->SetX($startX_right);
            }

            $pdf->Cell($colWidth1, $rowHeight, $num . ". " . htmlspecialchars($row['name']), 1, 0, 'L');
            $pdf->Cell($colWidth2, $rowHeight, htmlspecialchars($row['status']), 1, 1, 'C');
            $num++;
        }

    } else {
        $pdf->SetFont('Arial', '', 12);
        $pdf->Cell(0, 10, 'No attendance data found.', 0, 1, 'C');
    }

    $pdf->Output('attendance.pdf', 'I');
    exit;
}

$conn->close();
?>