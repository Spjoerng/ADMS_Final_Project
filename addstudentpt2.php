<?php include 'shared_header.php'; ?>

<?php
$lecturer_id = $_SESSION['id'];
$stmt = $conn->prepare("CALL view_managelist(?)");
$stmt->bind_param("i", $lecturer_id);
$stmt->execute();
$result = $stmt->get_result();

$proc->drain($conn);

$selected_student_id = isset($_POST['student_id']) ? $_POST['student_id'] : null;

if ($selected_student_id) {
    if ($selected_student_id == 1) {
        echo '<div class="alert alert-warning">Please pick a student.</div>';
    } else {
        $proc->addStudent($_SESSION['classID'], $selected_student_id);
        header("Location: addstudentpt2.php");
    }
}


$stmt2 = $conn->prepare("CALL get_students(?, ?)");
$stmt2->bind_param("ii", $lecturer_id, $_SESSION['classID']);
$stmt2->execute();
$list_result = $stmt2->get_result();
$proc->drain($conn);


$stmt3 = $conn->prepare("CALL get_unenrolled(?, ?)");
$stmt3->bind_param("ii", $lecturer_id, $_SESSION['classID']);
$stmt3->execute();
$student_result = $stmt3->get_result();
$proc->drain($conn);
?>

<title>Add Students</title>
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

<!-- shows currently enrolled students table -->
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Add Students to Class</h5>
    </div>
    <div class="card-body">
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="mb-0">Current Students</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-rounded">
                        <thead>
                            <tr>
                                <th>Student Name</th>
                                <th>Section</th>
                                <th>Subject Code</th>
                                <th>Subject Name</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $list_result->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row['name']); ?></td>
                                    <td><?php echo htmlspecialchars($row['section']); ?></td>
                                    <td><?php echo htmlspecialchars($row['subject_code']); ?></td>
                                    <td><?php echo htmlspecialchars($row['subject_name']); ?></td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- student dropdown  -->
        <form method="post" action="">
            <div class="form-group mb-3">
                <label for="student_id">Student to be included in class list:</label>
                <select class="form-control" name="student_id" id="student_id">
                    <?php if ($student_result->num_rows > 0): ?>
                        <?php while ($row = $student_result->fetch_assoc()): ?>
                            <option value="<?php echo $row['id']; ?>" <?php echo ($row['id'] == $selected_student_id) ? 'selected' : ''; ?>>
                                <?php echo $row['name']; ?>
                            </option>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <option>No unassigned students found.</option>
                    <?php endif; ?>
                </select>
            </div>
            <div class="d-flex justify-content-end gap-2">
                <a href="managelist.php" class="btn btn-outline-secondary">Cancel</a>
                <button type="submit" name="submit" class="btn btn-primary">Add Student</button>
            </div>
        </form>
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