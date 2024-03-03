<?php $userType = 'admin' ?>
<?php
require_once "../includes/config.php";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["student_id"])) {
    $student_id = $_POST["student_id"];

    try {
        // Attempt to delete the student
        $deleteSql = "DELETE FROM students WHERE id = ?";
        $deleteStmt = $conn->prepare($deleteSql);
        $deleteStmt->bind_param("i", $student_id);

        if ($deleteStmt->execute()) {
            $_SESSION['success_message'] = 'Student deleted successfully!';
        } else {
            $_SESSION['error_message'] = 'Failed to delete student.';
        }

        $deleteStmt->close();
    } catch (Exception $e) {
        $_SESSION['error_message'] = getDBErrorMessage($e->getCode());
    } finally {
        $conn->close();
    }
}

header("Location: $baseUrl/admin/students.php");
exit();
