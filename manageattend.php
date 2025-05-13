<?php include 'shared_header.php'; ?>

<?php
$lecturer_id = $_SESSION['id'];
$stmt = $conn->prepare("CALL view_manageattend(?)");
$stmt->bind_param("i", $lecturer_id);
$stmt->execute();
$result = $stmt->get_result();
$proc->drain($conn);


$classes = [];
while ($row = $result->fetch_assoc()) {
    $classes[] = $row;
}


$stmt = $conn->prepare("CALL view_manageattend(?)");
$stmt->bind_param("i", $lecturer_id);
$stmt->execute();
$table_result = $stmt->get_result();
$proc->drain($conn);

$selected_class_id = isset($_POST['class_id']) ? $_POST['class_id'] : null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $selected_class_id) {
    $_SESSION['classID'] = $selected_class_id;

    if (isset($_POST['print'])) {
        header("Location: printattendance.php");
        exit;
    } elseif (isset($_POST['conduct'])) {
        header("Location: conductattendance.php");
        exit;
    }
}
?>

<title>Manage Class Attendance</title>
<div class="attendance-tabs">
    <ul class="nav nav-tabs mb-4">
        <li class="nav-item">
            <a class="nav-link active" id="classes-tab" data-bs-toggle="tab" href="#classes-content">Classes</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="conduct-tab" data-bs-toggle="tab" href="#conduct-content">Manage Attendance</a>
        </li>
    </ul>

    <!-- shows table of classes handled with the number of students  -->
    <div class="tab-content">
        <div class="tab-pane fade show active" id="classes-content">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Classes handled by <?php echo $_SESSION['name']; ?></h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-rounded">
                            <thead>
                                <tr>
                                    <th>Subject Code</th>
                                    <th>Subject Name</th>
                                    <th>Section</th>
                                    <th>No. of Students</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = $table_result->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($row['subject_code']); ?></td>
                                        <td><?php echo htmlspecialchars($row['subject_name']); ?></td>
                                        <td><?php echo htmlspecialchars($row['section']); ?></td>
                                        <td><?php echo htmlspecialchars($row['Students']); ?></td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="tab-pane fade" id="conduct-content">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Conduct Attendance</h5>
                </div>
                <div class="card-body">
                    <form method="post" action="">
                        <div class="form-group mb-3">
                            <!-- class dropdown  -->
                            <label for="class_id">ID of the class to be selected:</label>
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
                            <button type="submit" name="print" class="btn btn-outline-secondary">Print
                                Attendance</button>
                            <button type="submit" name="conduct" class="btn btn-primary">Conduct Attendance</button>
                        </div>
                    </form>
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
        </style>
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

    .table-rounded th,
    .table-rounded td {
        padding: 12px 15px;
        vertical-align: middle;
    }

    .tab-content {
        transition: all 0.3s ease;
    }

    
    .attendance-tabs .nav-tabs {
        position: relative;
        display: flex;
        flex-direction: row;
        align-items: flex-start;
        padding: 2px;
        background-color: #dadadb;
        border-radius: 9px;
        margin-bottom: 20px;

    }

    .attendance-tabs .nav-item {
        margin: 0;
    }

    .attendance-tabs .nav-link {
        color: #4A4949;
        border: none;
        border-radius: 7px;
        padding: 8px 25px;
        font-size: 14px;
        font-weight: 500;
        margin: 0 3px;
        transition: all 0.3s ease;
        opacity: 0.7;
    }

    .attendance-tabs .nav-link:hover {
        opacity: 1;
    }

    .attendance-tabs .nav-link.active {
        background-color: #ffffff;
        color: #dc3545;
        opacity: 1;
        box-shadow: 0px 3px 8px rgba(0, 0, 0, 0.12), 0px 3px 1px rgba(0, 0, 0, 0.04);
    }

    .attendance-tabs .tab-pane {
        padding-top: 20px;
    }
</style>


<script>
    document.addEventListener('DOMContentLoaded', function () {
        
        const classesTab = document.getElementById('classes-tab');
        const conductTab = document.getElementById('conduct-tab');
        const classesContent = document.getElementById('classes-content');
        const conductContent = document.getElementById('conduct-content');

        classesTab.addEventListener('click', function (e) {
            e.preventDefault();

            
            classesTab.classList.add('active');
            conductTab.classList.remove('active');

            
            classesContent.classList.add('show', 'active');
            conductContent.classList.remove('show', 'active');
        });

        conductTab.addEventListener('click', function (e) {
            e.preventDefault();

           
            conductTab.classList.add('active');
            classesTab.classList.remove('active');

           
            conductContent.classList.add('show', 'active');
            classesContent.classList.remove('show', 'active');
        });
    });
</script>

<?php
$conn->close();
include 'shared_footer.php';
?>