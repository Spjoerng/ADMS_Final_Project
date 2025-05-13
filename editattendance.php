<?php include 'shared_header.php'; ?>

<?php
$lecturer_id = $_SESSION['id'];
$class_id = $_SESSION['classID'];
$date = $_SESSION['date'];


$stmt = $conn->prepare("CALL get_attendance_details(?, ?, ?)");
$stmt->bind_param("iis", $lecturer_id, $class_id, $date);
$stmt->execute();
$attendance_result = $stmt->get_result();
$proc->drain($conn);

// Get class details for display
$stmt2 = $conn->prepare("CALL view_schedules(?)");
$stmt2->bind_param("i", $lecturer_id);
$stmt2->execute();
$class_result = $stmt2->get_result();
$proc->drain($conn);

$class_name = "";
while ($row = $class_result->fetch_assoc()) {
    if ($row['id'] == $class_id) {
        $class_name = $row['subject_code'] . " " . $row['subject_name'] . " - " . $row['section'];
        break;
    }
}

// Store a copy of attendance records to display in the table
$attendance_data = [];
while ($row = $attendance_result->fetch_assoc()) {
    $attendance_data[] = $row;
}
$attendance_result->data_seek(0); 

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $selected_attendance_id = $_POST['attendance_id'] ?? null;
    $selected_status = $_POST['status'] ?? null;

    if ($selected_attendance_id && $selected_status) {
        if (updateStatus($conn, $selected_attendance_id, $selected_status)) {
            // Add success message
            $success_message = "Attendance status has been updated successfully.";
            
            // Refresh attendance data
            $stmt = $conn->prepare("CALL get_attendance_details(?, ?, ?)");
            $stmt->bind_param("iis", $lecturer_id, $class_id, $date);
            $stmt->execute();
            $attendance_result = $stmt->get_result();
            $proc->drain($conn);
            
            // Update attendance data 
            $attendance_data = [];
            while ($row = $attendance_result->fetch_assoc()) {
                $attendance_data[] = $row;
            }
            $attendance_result->data_seek(0);
        } else {
           
            $server_error = "Failed to update attendance status. Please try again.";
        }
    }
    
}

// Function to display the status dropdown
function statusDropdown($name, $selectedStatus = null) {
    $statuses = ['Present', 'Absent', 'Late', 'Excused'];
    echo "<select class=\"form-control\" name=\"$name\" id=\"$name\">";
    echo "<option value=\"\">Select Status</option>";
    foreach ($statuses as $status) {
        $selected = ($status === $selectedStatus) ? "selected" : "";
        echo "<option value=\"$status\" $selected>$status</option>";
    }
    echo "</select>";
}

// Function to update attendance status
function updateStatus($conn, $attendance_id, $new_status) {
    $stmt = $conn->prepare("CALL update_status(?, ?)");
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }
    $stmt->bind_param("is", $attendance_id, $new_status);
    return $stmt->execute();
}

// Helper function to get status badge class
function getStatusBadgeClass($status) {
    switch (strtolower($status)) {
        case 'present':
            return 'bg-success';
        case 'absent':
            return 'bg-danger';
        case 'late':
            return 'bg-warning text-dark';
        case 'excused':
            return 'bg-info text-dark';
        default:
            return 'bg-secondary';
    }
}
?>

<title>Edit Attendance</title>
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Edit Attendance: <?php echo htmlspecialchars($class_name); ?></h5>
        <span class="text-muted"><?php echo date('F d, Y', strtotime($date)); ?></span>
    </div>
    <div class="card-body">
        <div id="alert-container">
            <?php if (isset($success_message)): ?>
                <div class="alert alert-success alert-fade">
                    <?php echo $success_message; ?>
                </div>
            <?php endif; ?>
            
            <?php if (isset($server_error)): ?>
                <div class="alert alert-danger alert-fade">
                    <?php echo $server_error; ?>
                </div>
            <?php endif; ?>
        </div>
        
        <div class="row">
            <!-- Left side: Form to edit attendance -->
            <div class="col-md-5">
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0">Update Attendance Status</h6>
                    </div>
                    <div class="card-body">
                        <form method="post" action="" id="attendance-form" onsubmit="return validateForm()">
                            <div class="form-group mb-3">
                                <label for="attendance_id" class="form-label">Select Student:</label>
                                <select class="form-control" name="attendance_id" id="attendance_id">
                                    <option value="">Select Student</option>
                                    <?php
                                    $attendance_result->data_seek(0);
                                    while ($row = $attendance_result->fetch_assoc()):
                                        echo "<option value=\"" . $row['id'] . "\">" . htmlspecialchars($row['name']) . " (" . $row['status'] . ")</option>";
                                    endwhile;
                                    ?>
                                </select>
                            </div>
                            
                            <div class="form-group mb-4">
                                <label for="status" class="form-label">Change Status To:</label>
                                <?php statusDropdown("status"); ?>
                            </div>
                            
                            <div class="d-flex justify-content-end gap-2">
                                <a href="printattendancept2.php" class="btn btn-outline-secondary">Cancel</a>
                                <button type="submit" name="submit" class="btn btn-primary">Update Status</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            
            <!-- Right side: Current attendance display -->
            <div class="col-md-7">
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0">Current Attendance Status</h6>
                    </div>
                    <div class="card-body">
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
                                    foreach ($attendance_data as $row): 
                                        $status_class = getStatusBadgeClass($row['status']);
                                    ?>
                                        <tr>
                                            <td><?php echo $count++; ?></td>
                                            <td><?php echo htmlspecialchars($row['name']); ?></td>
                                            <td>
                                                <span class="badge rounded-pill <?php echo $status_class; ?>">
                                                    <?php echo htmlspecialchars($row['status']); ?>
                                                </span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="d-flex justify-content-between mt-4">
            <a href="printattendancept2.php" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i> Back to Attendance Preview
            </a>
            <a href="manageattend.php" class="btn btn-primary">
                Return to Attendance Management
            </a>
        </div>
    </div>
</div>

<style>
    .card-header {
        background-color: rgb(241, 241, 241);
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
    
 
    .alert-fade {
        animation: fadeOut 5s forwards;
    }
    
    @keyframes fadeOut {
        0% { opacity: 1; }
        70% { opacity: 1; }
        100% { opacity: 0; display: none; }
    }
    
   
    select option:first-child {
        color: #999;
        opacity: 0.6;
    }
    
    
    select:invalid,
    select option:first-child,
    select:not(:focus):valid:not([multiple]):not([size]) {
        color: #757575;
        opacity: 0.7;
    }
    
    
    #attendance_id:not(:focus), #status:not(:focus) {
        color: #757575;
        opacity: 0.7;
    }
</style>

<script>
function validateForm() {
    const studentSelect = document.getElementById('attendance_id');
    const statusSelect = document.getElementById('status');
    const alertContainer = document.getElementById('alert-container');
    
    if (studentSelect.value === '' || statusSelect.value === '') {
       
        alertContainer.innerHTML = `
            <div class="alert alert-danger alert-fade">
                Please select both a student and a status.
            </div>
        `;
        
        
        setTimeout(() => {
            alertContainer.innerHTML = '';
        }, 5000);
        
        return false;
    }
    
    return true;
}


document.addEventListener('DOMContentLoaded', function() {

    const alerts = document.querySelectorAll('.alert-fade');
    alerts.forEach(alert => {
        setTimeout(() => {
            if (alert && alert.parentNode) {
                alert.parentNode.removeChild(alert);
            }
        }, 5000);
    });
    
    
    const selectElements = document.querySelectorAll('select');
    selectElements.forEach(select => {
        // Add a class when the select has a value selected
        select.addEventListener('change', function() {
            if (this.value) {
                this.style.color = '#212529';
                this.style.opacity = '1';
            } else {
                this.style.color = '#757575';
                this.style.opacity = '0.7';
            }
        });
    });
});
</script>

<?php
$conn->close();
include 'shared_footer.php';
?>