<?php include 'shared_header.php'; ?>

<?php
$lecturer_id = $_SESSION['id'];

// First execution: get classes for dropdown
$stmt = $conn->prepare("CALL view_schedules(?)");
if (!$stmt) {
    die("Prepare failed: (" . $conn->errno . ") " . $conn->error);
}
$stmt->bind_param("i", $lecturer_id);
if (!$stmt->execute()) {
    die("Execute failed: (" . $stmt->errno . ") " . $stmt->error);
}
$dropdown_result = $stmt->get_result();

// Store results in array
$classes = [];
while ($row = $dropdown_result->fetch_assoc()) {
    $classes[] = $row;
}

// Drain all results from first call
$proc->drain($conn);

$selected_class_id = isset($_POST['class_id']) ? $_POST['class_id'] : null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $selected_class_id) {
    $_SESSION['classID'] = $selected_class_id;
    
    if (isset($_POST['add'])) {
        header("Location: addstudentpt2.php");
        exit;
    } elseif (isset($_POST['del'])) {
        header("Location: deletestudentpt2.php");
        exit;
    }
}

// Second execution: get fresh results for table
$stmt2 = $conn->prepare("CALL view_managelist(?)");
if (!$stmt2) {
    die("Prepare failed: (" . $conn->errno . ") " . $conn->error);
}
$stmt2->bind_param("i", $lecturer_id);
if (!$stmt2->execute()) {
    die("Execute failed: (" . $stmt2->errno . ") " . $stmt2->error);
}
$table_result = $stmt2->get_result();

// Drain all results from second call
$proc->drain($conn);
?>

<title>Manage Class List</title>
<div class="tab-container">
    <input type="radio" name="tab" id="tab1" class="tab tab--1" checked />
    <label class="tab_label" for="tab1">Manage Classes</label>

    <input type="radio" name="tab" id="tab2" class="tab tab--2" />
    <label class="tab_label" for="tab2">Add Class</label>

    <input type="radio" name="tab" id="tab3" class="tab tab--3" />
    <label class="tab_label" for="tab3">Delete Class</label>

    <input type="radio" name="tab" id="tab4" class="tab tab--4" />
    <label class="tab_label" for="tab4">Manage List</label>

    <div class="indicator"></div>
</div>

<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Select Class to Manage</h5>
    </div>
    <div class="card-body">
        <form method="post" action="">
            <div class="form-group mb-3">
                <!-- class dropdown  -->
                <label for="class_id">Select a class:</label>
                <select class="form-control" name="class_id" id="class_id">
                    <?php if (!empty($classes)): ?>
                        <?php foreach ($classes as $row): ?>
                            <option value="<?php echo $row['id']; ?>" <?php echo ($row['id'] == $selected_class_id) ? 'selected' : ''; ?>>
                                <?php echo $row['subject_code'] . " " . $row['subject_name'] . " " . $row['section']; ?>
                            </option>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <option>No classes found.</option>
                    <?php endif; ?>
                </select>
            </div>
            <div class="d-flex justify-content-end gap-2">
                <button type="submit" name="add" class="btn btn-primary">Add Students</button>
                <button type="submit" name="del" class="btn btn-outline-danger">Delete Students</button>
            </div>
        </form>
    </div>
</div>

<div class="card mt-4">
    <div class="card-header">
        <h5 class="mb-0">Current Classes</h5>
    </div>
    <div class="card-body">
        <!-- print current handled classes with number of students  -->
        <?php $proc->printClassesWithCount($table_result, $_SESSION['name']); ?>
    </div>
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

<?php 
$conn->close();
include 'shared_footer.php'; 
?>