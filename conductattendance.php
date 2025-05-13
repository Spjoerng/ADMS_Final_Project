<?php include 'shared_header.php'; ?>

<?php
$lecturer_id = $_SESSION['id'];

// Fetch students for the selected class
$stmt2 = $conn->prepare("CALL get_students(?, ?)");
$stmt2->bind_param("ii", $lecturer_id, $_SESSION['classID']);
$stmt2->execute();
$list_result = $stmt2->get_result();
$proc->drain($conn);

// Get class details for display
$stmt3 = $conn->prepare("CALL view_schedules(?)");
$stmt3->bind_param("i", $lecturer_id);
$stmt3->execute();
$class_result = $stmt3->get_result();
$proc->drain($conn);

$class_name = "";
while ($row = $class_result->fetch_assoc()) {
    if ($row['id'] == $_SESSION['classID']) {
        $class_name = $row['subject_code'] . " " . $row['subject_name'] . " - " . $row['section'];
        break;
    }
}

// Process attendance submission
$attendance_submitted = false;
$error_message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    // Check if all students have attendance marked
    $all_marked = true;
    $list_result->data_seek(0);
    while ($student = $list_result->fetch_assoc()) {
        $status_key = 'status_' . $student['student_id'];
        if (!isset($_POST[$status_key])) {
            $all_marked = false;
            break;
        }
    }
    
    if ($all_marked) {
        $proc->addAttendance($list_result, $_SESSION['classID']);
        $attendance_submitted = true;
    } else {
        $error_message = "Please mark attendance for all students.";
    }
}
?>

<title>Conduct Attendance</title>
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Conduct Attendance: <?php echo htmlspecialchars($class_name); ?></h5>
        <span class="text-muted"><?php echo date('F d, Y'); ?></span>
    </div>
    <div class="card-body">
        <?php if ($attendance_submitted): ?>
            <div class="alert alert-success">
                Attendance has been successfully recorded.
            </div>
            <div class="d-flex justify-content-end mt-3">
                <a href="manageattend.php" class="btn btn-primary">Back to Attendance Management</a>
            </div>
        <?php else: ?>
            <?php if ($error_message): ?>
                <div class="alert alert-danger">
                    <?php echo $error_message; ?>
                </div>
            <?php endif; ?>
            
            <?php 
            // Use the classRadio function from Procedures.php to show attendance form
            $list_result->data_seek(0);
            $proc->classRadio($list_result, $_SESSION['classID']); 
            ?>
            
        <?php endif; ?>
        <style>
        .card-header {
            background-color:rgb(241, 241, 241);
            border-bottom: 1px solid #eee;
            font-weight: 600;
        }

        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        :root {
            --bs-card-border-color: transparent;

        }
    </style>
    </div>
</div>

<style>
    /* Additional styles to supplement those in Procedures.php */
    .card {
        border-radius: 10px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        margin-bottom: 20px;
    }
    
    .card-header {
        background-color: #f8f9fa;
        border-bottom: 1px solid #eee;
        font-weight: 600;
    }
    
    .alert {
        padding: 12px 15px;
        border-radius: 5px;
        margin-bottom: 20px;
    }
    
    .alert-success {
        background-color: #d1e7dd;
        color: #0a3622;
    }
    
    .alert-danger {
        background-color: #f8d7da;
        color: #842029;
    }
    
    /* Make sure radio buttons are centered */
    .text-center {
        text-align: center !important;
    }
    
    /* Override styles for table from Procedures.php if needed */
    .attendance-table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 20px;
    }
    
    .attendance-table th,
    .attendance-table td {
        padding: 10px;
        border: 1px solid #dee2e6;
        vertical-align: middle;
    }
    
    .attendance-table th {
        background-color: #f8f9fa;
        font-weight: 600;
        text-align: center;
    }
    
    /* Center radio inputs within cells */
    .attendance-table input[type="radio"] {
        margin: 0 auto;
        display: block;
    }
</style>

<?php
$conn->close();
include 'shared_footer.php';
?>