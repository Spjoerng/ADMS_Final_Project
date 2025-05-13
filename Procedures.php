<?php
include "connection.php";

class Procedures
{
    private $conn;

    public function __construct()
    {
        $database = new Database();
        $this->conn = $database->conn;
    }

    public function getConnection()
    {
        return $this->conn;
    }

    //clears out all remaining results from a previous call to a multiquery
    public function drain($conn)
    {
        while ($conn->more_results() && $conn->next_result()) {
            $extraResult = $conn->use_result();
            if ($extraResult instanceof mysqli_result) {
                $extraResult->free();
            }
        }
    }

    //shows classes handled by the lecturer
    public function printClasses($result, $name)
    {
        if ($result->num_rows > 0) {
            echo '<table class="table">';
            echo '<thead>';
            echo '<tr>';
            echo '<th>Subject Code</th>';
            echo '<th>Subject Name</th>';
            echo '<th>Section</th>';
            echo '</tr>';
            echo '</thead>';
            echo '<tbody>';
            while ($row = $result->fetch_assoc()) {
                echo '<tr>';
                echo '<td>' . htmlspecialchars($row['subject_code']) . '</td>';
                echo '<td>' . htmlspecialchars($row['subject_name']) . '</td>';
                echo '<td>' . htmlspecialchars($row['section']) . '</td>';
                echo '</tr>';
            }
            echo '</tbody>';
            echo '</table>';
        } else {
            echo '<p>No classes found for ' . htmlspecialchars($name) . '.</p>';
        }
    }

    //shows classes handled by the lecturer including the number of students
    public function printClassesWithCount($result, $name)
    {
        if ($result->num_rows > 0) {
            echo '<table class="table">';
            echo '<thead>';
            echo '<tr>';
            echo '<th>Subject Code</th>';
            echo '<th>Subject Name</th>';
            echo '<th>Section</th>';
            echo '<th>No. of Students</th>';
            echo '</tr>';
            echo '</thead>';
            echo '<tbody>';
            while ($row = $result->fetch_assoc()) {
                echo '<tr>';
                echo '<td>' . htmlspecialchars($row['subject_code']) . '</td>';
                echo '<td>' . htmlspecialchars($row['subject_name']) . '</td>';
                echo '<td>' . htmlspecialchars($row['section']) . '</td>';
                echo '<td>' . htmlspecialchars($row['Students']) . '</td>';
                echo '</tr>';
            }
            echo '</tbody>';
            echo '</table>';
        } else {
            echo '<p>No classes found for ' . htmlspecialchars($name) . '.</p>';
        }
    }

    //checks for duplicates before creating a class for the lecturer
    public function checkDuplicate($lecturer_id, $section_id, $subject_id, &$checker)
    {
        $check_sql = "SELECT * FROM schedules WHERE
            section_id = ? AND subject_id = ?
        ";

        $stmt = $this->conn->prepare($check_sql);
        $stmt->bind_param("ii", $section_id, $subject_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            if (($section_id == $row['section_id'] && $subject_id == $row['subject_id']) && ($lecturer_id != $row['lecturer_id'])) {
                $checker = 1;
            } else {
                $checker = 2;
            }
            return true;
        }

        return false;
        return $checker;
    }

    //creates a new class
    public function createClass($lecturer_id, $section_id, $subject_id, &$checker)
    {
        $checker = 0;
        if ($this->checkDuplicate($lecturer_id, $section_id, $subject_id, $checker)) {
            return false;
        }

        $stmt = $this->conn->prepare("CALL create_schedule(?, ?, ?)");

        if (!$stmt) {
            die("Stored procedure preparation failed: " . $this->conn->error);
        }

        $stmt->bind_param("iii", $lecturer_id, $section_id, $subject_id);

        if ($stmt->execute()) {
            return true;
        } else {
            echo "Error executing stored procedure: " . $stmt->error;
            return false;
        }
        return $checker;
    }

    //deletes a class
    public function deleteClass($selected_class_id)
    {
        $stmt = $this->conn->prepare("CALL delete_schedule(?)");
        $stmt->bind_param("i", $selected_class_id);

        if ($stmt->execute()) {
            echo "The class has been successfully removed.<br>";
        } else {
            echo "Error removing the class: " . $this->conn->error . "<br>";
        }
        $stmt->close();
    }

    //prints the list of enrolled students
    public function printStudentList($list_result)
    {
        if ($list_result->num_rows > 0) {
            $first_row = $list_result->fetch_assoc();
            $subject_name = $first_row['subject_name'];
            $section = $first_row['section'];

            echo "Class list of $section, $subject_name:";
            $list_result->data_seek(0);

            echo '<div style="width: 100%; overflow-x: auto;">';
            echo "<table style='width: 100%; min-width: 800px;' border='1' cellpadding='8' cellspacing='0'>";
            echo "<thead>
                    <tr>
                        <th style='min-width: 200px;'>Student Name</th>
                        <th>Section</th>
                        <th>Subject Code</th>
                        <th style='min-width: 250px;'>Subject Name</th>
                    </tr>
                  </thead>";
            echo "<tbody>";

            while ($row = $list_result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($row['name']) . "</td>";
                echo "<td>" . htmlspecialchars($row['section']) . "</td>";
                echo "<td>" . htmlspecialchars($row['subject_code']) . "</td>";
                echo "<td>" . htmlspecialchars($row['subject_name']) . "</td>";
                echo "</tr>";
            }

            echo "</tbody></table>";
            echo '</div>';
        } else {
            echo "There are no students found for this class";
        }
    }

    //deletes an enrolled student from a class
    public function deleteStudent($selected_class_id, $selected_student_id)
    {
        $stmt = $this->conn->prepare("CALL delete_student(?, ?)");
        $stmt->bind_param("ii", $selected_student_id, $selected_class_id);

        if ($stmt->execute()) {
            echo "The class has been successfully removed.<br>";
        } else {
            echo "Error removing the class: " . $this->conn->error . "<br>";
        }

        $stmt->close();

    }

    //adds a student to a class
    public function addStudent($selected_class_id, $selected_student_id)
    {
        $stmt = $this->conn->prepare("CALL add_student(?, ?)");
        $stmt->bind_param("ii", $selected_class_id, $selected_student_id);

        if ($stmt->execute()) {
            return true;
        } else {
            echo "Error executing query: " . $this->conn->error;
            return false;
        }

        $stmt->close();
    }

    //shows the form for taking attendance
    public function classRadio($students, $schedule_id)
    {
        ?>
        <form method="post" action="">

            <?php
            $first_row = $students->fetch_assoc();

            $subject_name = $first_row['subject_name'];
            $section = $first_row['section'];

            echo "<p><b>Class list of $section, $subject_name:</b></p>";

            $students->data_seek(0);
            ?>
            
            <table border='1' width='70%' cellpadding='8' cellspacing='0' class="attendance-table">
                <thead>
                    <tr>
                        <th>Student Name</th>
                        <th class="text-center">Present</th>
                        <th class="text-center">Absent</th>
                        <th class="text-center">Late</th>
                        <th class="text-center">Excused</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($students as $student):
                        $input_name = "status_" . $student['student_id'];
                    ?>
                        <tr>
                            <td><?php echo htmlspecialchars($student['name']); ?></td>
                            <td class="text-center"><input type="radio" name="<?php echo $input_name; ?>" value="Present" required></td>
                            <td class="text-center"><input type="radio" name="<?php echo $input_name; ?>" value="Absent"></td>
                            <td class="text-center"><input type="radio" name="<?php echo $input_name; ?>" value="Late"></td>
                            <td class="text-center"><input type="radio" name="<?php echo $input_name; ?>" value="Excused"></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <input type="hidden" name="schedule_id" value="<?= htmlspecialchars($schedule_id) ?>">
            
            <div class="button-group">
                <input type="submit" name="submit" value="Submit Attendance" class="btn-submit">
                <button type="button" onclick="window.location.href='manageattend.php'" class="btn-cancel">
                    Cancel
                </button>
            </div>
        </form>
        
        <style>
            .attendance-table {
                width: 100%;
                border-collapse: collapse;
                margin-bottom: 20px;
            }
            
            .attendance-table th,
            .attendance-table td {
                padding: 10px;
                border: 1px solid #dee2e6;
            }
            
            .attendance-table th {
                background-color: #f8f9fa;
                font-weight: 600;
            }
            
            .text-center {
                text-align: center;
            }
            
            .button-group {
                display: flex;
                gap: 10px;
                margin-top: 20px;
            }
            
            .btn-submit {
                background-color: #0d6efd;
                color: white;
                border: none;
                padding: 8px 16px;
                border-radius: 4px;
                cursor: pointer;
            }
            
            .btn-submit:hover {
                background-color: #0b5ed7;
            }
            
            .btn-cancel {
                background-color: #6c757d;
                color: white;
                border: none;
                padding: 8px 16px;
                border-radius: 4px;
                cursor: pointer;
            }
            
            .btn-cancel:hover {
                background-color: #5c636a;
            }

            
        </style>
        <?php
    }

    //records attendance
    public function addAttendance($students, $class_id)
    {
        $students->data_seek(0);
        date_default_timezone_set('Asia/Manila');
        $current_date = date('Y-m-d');

        foreach ($students as $student) {
            $student_id = $student['student_id'];
            $status_key = 'status_' . $student_id;

            if (isset($_POST[$status_key])) {
                $status = $_POST[$status_key];

                $stmt = $this->conn->prepare("CALL insert_record(?, ?, ?, ?)");
                $stmt->bind_param("iiss", $student_id, $class_id, $status, $current_date);
                $stmt->execute();
            }
        }


        echo "Attendance has been taken successfully!";
    }

    //prints the attendance filtered by the selected attendance date
    public function printClassAttendance($result, $sessionOwner)
    {
        if ($result->num_rows > 0) {

            $first_row = $result->fetch_assoc();

            $subject_name = $first_row['subject_name'];
            $section = $first_row['section'];
            $date = $first_row['attendance_date'];

            echo "<p><b>Class list of $section, $subject_name on $date:</b></p>";

            $result->data_seek(0);

            echo "<table border='1' cellpadding='8' cellspacing='0'>";
            echo "<thead>
                    <tr>
                        <th>Student Name</th>
                        <th>Status</th>
                    </tr>
                  </thead>";
            echo "<tbody>";

            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($row['name']) . "</td>";
                echo "<td>" . htmlspecialchars($row['status']) . "</td>";
                echo "</tr>";
            }
            echo "</tbody></table>";
        } else {

            echo "There are no schedules found for " . htmlspecialchars($sessionOwner);
        }

    }

    //shows a table for the available attendance dates for filtering
    public function printDates($result, $sessionOwner)
    {
        if ($result->num_rows > 0) {
            echo "<table border='1' cellpadding='8' cellspacing='0'>";
            echo "<thead>
                    <tr> 
                        <th>Attendance Date</th>
                    </tr>
                  </thead>";
            echo "<tbody>";

            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($row['attendance_date']) . "</td>";
                echo "</tr>";
            }
            echo "</tbody></table>";
        } else {
            echo "There are no schedules found for " . htmlspecialchars($sessionOwner);
        }
    }

}

?>