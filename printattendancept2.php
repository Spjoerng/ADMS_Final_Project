<?php include 'shared_header.php'; ?>

<?php
$lecturer_id = $_SESSION['id'];

// Fetch attendance details
$stmt = $conn->prepare("CALL get_attendance_details(?, ?, ?)");
$stmt->bind_param("iis", $lecturer_id, $_SESSION['classID'], $_SESSION['date']);
$stmt->execute();
$attendance_result = $stmt->get_result();
$proc->drain($conn);

// Store the first row to get class details
$class_details = null;
if ($attendance_result->num_rows > 0) {
    $class_details = $attendance_result->fetch_assoc();
    $attendance_result->data_seek(0); // Reset pointer
}
?>

<title>Attendance Preview</title>
<!-- shows preview of the student attendance record  -->
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Attendance Sheet Preview</h5>
    </div>
    <div class="card-body">
        <?php if ($class_details): ?>
            <div class="attendance-info mb-4">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Lecturer:</strong> <?php echo htmlspecialchars($_SESSION['name']); ?></p>
                        <p><strong>Course Code and Title:</strong> <?php echo htmlspecialchars($class_details['subject_code'] . ' ' . $class_details['subject_name']); ?></p>
                        <p><strong>Section:</strong> <?php echo htmlspecialchars($class_details['section']); ?></p>
                    </div>
                    <div class="col-md-6 text-md-end">
                        <p><strong>Date:</strong> <?php echo date('F d, Y', strtotime($class_details['attendance_date'])); ?></p>
                    </div>
                </div>
            </div>
            
            <div class="table-responsive">
                <table class="table table-rounded">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Student Name</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $count = 1;
                        while ($row = $attendance_result->fetch_assoc()): 
                        ?>
                            <tr>
                                <td><?php echo $count++; ?></td>
                                <td><?php echo htmlspecialchars($row['name']); ?></td>
                                <td>
                                    <span class="status-badge <?php echo strtolower($row['status']); ?>">
                                        <?php echo htmlspecialchars($row['status']); ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
            
            <div class="d-flex justify-content-between mt-4">
                <a href="printattendance.php" class="btn btn-outline-secondary">Back</a>
                <form method="POST" action="editattendance.php">
                    <button type="submit" name="edit" class="btn btn-outline-secondary">Edit Attendance</button>
                </form>
            
                <form method="POST" action="pdfprint.php" target="_blank">
                    <button type="submit" name="print" class="btn btn-primary">
                        <i class="fas fa-print me-2"></i> Print Attendance
                    </button>
                </form>
            </div>
        <?php else: ?>
            <div class="alert alert-warning">
                No attendance records found for the selected date.
            </div>
            <div class="d-flex justify-content-end mt-3">
                <a href="printattendance.php" class="btn btn-primary">Back to Select Date</a>
            </div>
        <?php endif; ?>
    </div>
</div>

<style>
    .table-rounded {
        border-collapse: separate;
        border-spacing: 0;
    }
    
    .table-rounded thead th {
        background-color: #f8f9fa;
        border-bottom: 1px solid #dee2e6;
    }
    
    .table-rounded th, .table-rounded td {
        padding: 12px 15px;
        vertical-align: middle;
    }
    
    .status-badge {
        display: inline-block;
        padding: 5px 10px;
        border-radius: 4px;
        font-size: 0.85rem;
        font-weight: 500;
    }
    
    .status-badge.present {
        background-color: #d1e7dd;
        color: #0a3622;
    }
    
    .status-badge.late {
        background-color: #fff3cd;
        color: #664d03;
    }
    
    .status-badge.absent {
        background-color: #f8d7da;
        color: #842029;
    }
    
    .attendance-info {
        background-color: #f8f9fa;
        padding: 15px;
        border-radius: 8px;
    }
    
    .attendance-info p {
        margin-bottom: 0.5rem;
    }
</style>

<?php
$conn->close();
include 'shared_footer.php';
?>