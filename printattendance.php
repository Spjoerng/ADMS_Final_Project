<?php include 'shared_header.php'; ?>

<?php
$lecturer_id = $_SESSION['id'];

// Get attendance dates for the selected class
$stmt = $conn->prepare("CALL get_dates(?, ?)");
$stmt->bind_param("ii", $lecturer_id, $_SESSION['classID']);
$stmt->execute();
$result = $stmt->get_result();
$proc->drain($conn);

// Get class details for display
$stmt2 = $conn->prepare("CALL view_schedules(?)");
$stmt2->bind_param("i", $lecturer_id);
$stmt2->execute();
$class_result = $stmt2->get_result();
$proc->drain($conn);

$class_name = "";
while ($row = $class_result->fetch_assoc()) {
    if ($row['id'] == $_SESSION['classID']) {
        $class_name = $row['subject_code'] . " " . $row['subject_name'] . " - " . $row['section'];
        break;
    }
}

$selected_date = isset($_POST['date_']) ? $_POST['date_'] : null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $selected_date) {
    $_SESSION['date'] = $selected_date;
    header("Location: printattendancept2.php");
    exit;
}
?>

<title>Print Attendance Sheet</title>
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Print Attendance: <?php echo htmlspecialchars($class_name); ?></h5>
    </div>
    <div class="card-body">
        <?php if ($result->num_rows > 0): ?>
            <form method="post" action="">
                <div class="form-group mb-4">
                <!-- date dropdown  -->
                <label for="date_" class="form-label">Select attendance date to print:</label>
                    <select class="form-control" name="date_" id="date_">
                        <?php 
                        $result->data_seek(0);
                        while ($row = $result->fetch_assoc()): 
                            $formatted_date = date('F d, Y', strtotime($row['attendance_date']));
                        ?>
                            <option value="<?php echo $row['attendance_date']; ?>" <?php echo ($row['attendance_date'] == $selected_date) ? 'selected' : ''; ?>>
                                <?php echo $formatted_date; ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                
                <div class="d-flex justify-content-between">
                    <a href="manageattend.php" class="btn btn-outline-secondary">Back</a>
                    <button type="submit" name="submit" class="btn btn-primary">Select Date</button>
                </div>
            </form>
        <?php else: ?>
            <div class="alert alert-info">
                No attendance records available for this class. Please conduct attendance first.
            </div>
            <div class="d-flex justify-content-end mt-3">
                <a href="manageattend.php" class="btn btn-primary">Back to Attendance Management</a>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- date table  -->
<?php if ($result->num_rows > 0): ?>
<div class="card mt-4">
    <div class="card-header">
        <h5 class="mb-0">Available Attendance Dates</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-rounded">
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $result->data_seek(0);
                    $count = 1;
                    while ($row = $result->fetch_assoc()): 
                        $formatted_date = date('F d, Y', strtotime($row['attendance_date']));
                    ?>
                        <tr>
                            <td><?php echo $count++; ?></td>
                            <td><?php echo $formatted_date; ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php endif; ?>

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
</style>

<?php
$conn->close();
include 'shared_footer.php';
?>