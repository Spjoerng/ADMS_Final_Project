<?php include 'shared_header.php'; ?>

<?php
$lecturer_id = $_SESSION['id'];
$stmt = $conn->prepare("CALL view_schedules(?)");
$stmt->bind_param("i", $lecturer_id);
$stmt->execute();
$result = $stmt->get_result();
?>
<title>Classes Handled</title>
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

<!-- shows table of current handled classes  -->
<div class="card mt-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Classes handled by <?php echo $_SESSION['name']; ?></h5>
    </div>
    <div class="card-body">
        <table class="table">
            <thead>
                <tr>
                    <th>Subject Code</th>
                    <th>Subject Name</th>
                    <th>Section</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['subject_code']; ?></td>
                        <td><?php echo $row['subject_name']; ?></td>
                        <td><?php echo $row['section']; ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
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