<?php include 'shared_header.php'; ?>

<?php
$lecturer_id = $_SESSION['id'];

$stmt = $conn->prepare("CALL view_schedules(?)");
$stmt->bind_param("i", $lecturer_id);
$stmt->execute();
$result = $stmt->get_result();

$proc->drain($conn);

$subject_sql = "SELECT id, subject_name FROM subjects";
$subject_result = $conn->query($subject_sql);

$section_sql = "SELECT id, section FROM sections";
$section_result = $conn->query($section_sql);

$selected_subject_id = isset($_POST['subject_id']) ? $_POST['subject_id'] : null;
$selected_section_id = isset($_POST['section_id']) ? $_POST['section_id'] : null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $selected_section_id && $selected_subject_id) {
    if (($selected_section_id != 1) && ($selected_subject_id != 1)) {
        $proc->createClass($_SESSION['id'], $selected_section_id, $selected_subject_id, $checker);

        if ($checker == 1) {
            echo '<div class="alert alert-danger">Another lecturer has been assigned to this class.</div>';
        } else if ($checker == 2) {
            echo '<div class="alert alert-danger">This class already exists.</div>';
        } else {
            header("Location: addclass.php");
            exit;
        }
    } else {
        echo '<div class="alert alert-warning">Please choose a subject/section.</div>';
    }
}
?>
<title>Add a Class</title>
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
        <h5 class="mb-0">Add New Class</h5>
    </div>
    <div class="card-body">
        <form method="post" action="">
            <div class="row mb-3">
                <div class="col-md-6">
                    <div class="form-group">
                        <!-- dropdowns for subject -->
                        <label for="subject_id">Subject:</label>
                        <select class="form-control" name="subject_id" id="subject_id">
                            <?php if ($subject_result->num_rows > 0): ?>
                                <?php while ($row = $subject_result->fetch_assoc()): ?>
                                    <option value="<?php echo $row['id']; ?>" <?php echo ($row['id'] == $selected_subject_id) ? 'selected' : ''; ?>>
                                        <?php echo $row['subject_name']; ?>
                                    </option>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <option>No Subjects found</option>
                            <?php endif; ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <!-- dropdowns for section -->
                        <label for="section_id">Section:</label>
                        <select class="form-control" name="section_id" id="section_id">
                            <?php if ($section_result->num_rows > 0): ?>
                                <?php while ($row = $section_result->fetch_assoc()): ?>
                                    <option value="<?php echo $row['id']; ?>" <?php echo ($row['id'] == $selected_section_id) ? 'selected' : ''; ?>>
                                        <?php echo $row['section']; ?>
                                    </option>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <option>No Sections found</option>
                            <?php endif; ?>
                        </select>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2">
                <a href="manageclass.php" class="btn btn-outline-secondary">Cancel</a>
                <button type="submit" name="submit" class="btn btn-primary">Add</button>

            </div>
        </form>
    </div>
    
</div>

<div class="card mt-4">
    <div class="card-header">
        <h5 class="mb-0">Current Classes</h5>
    </div>
    <div class="card-body">
        <?php $proc->printClasses($result, $_SESSION['name']); ?>
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
</div>

<?php
$conn->close();
?>



<?php include 'shared_footer.php'; ?>