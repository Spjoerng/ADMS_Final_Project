<?php include 'shared_header.php'; ?>

<?php
$lecturer_id = $_SESSION['id'];


$stmt = $conn->prepare("CALL view_schedules(?)");
$stmt->bind_param("i", $lecturer_id);
$stmt->execute();
$dropdown_result = $stmt->get_result();


$classes = [];
while ($row = $dropdown_result->fetch_assoc()) {
    $classes[] = $row;
}


$proc->drain($conn);

$selected_class_id = isset($_POST['class_id']) ? $_POST['class_id'] : null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $selected_class_id) {
    $proc->deleteClass($selected_class_id);
    header("Location: deleteclass.php");
    exit;
}


$stmt = $conn->prepare("CALL view_schedules(?)");
$stmt->bind_param("i", $lecturer_id);
$stmt->execute();
$table_result = $stmt->get_result();


$proc->drain($conn);
?>

<title>Delete a Class</title>
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

<!-- class dropdown -->
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Delete Class</h5>
    </div>
    <div class="card-body">
        <form method="post" action="">
            <div class="form-group mb-3">
                <label for="class_id">Select the class to be deleted:</label>
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
            <div class="d-flex justify-content-end">
                <button type="submit" name="submit" class="btn btn-danger">Delete</button>
            </div>
        </form>
    </div>
</div>

<div class="card mt-4">
    <div class="card-header">
        <h5 class="mb-0">Current Classes</h5>
    </div>
    <div class="card-body">
        <!-- shows current handled classes  -->
        <?php $proc->printClasses($table_result, $_SESSION['name']); ?>
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