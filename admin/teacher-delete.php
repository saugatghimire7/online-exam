<?php $userType = 'admin' ?>
<?php
require_once "../includes/config.php";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["teacher_id"])) {
    $teacher_id = $_POST["teacher_id"];

    try {
        // Attempt to delete the teacher
        $deleteSql = "DELETE FROM teachers WHERE id = ?";
        $deleteStmt = $conn->prepare($deleteSql);
        $deleteStmt->bind_param("i", $teacher_id);

        if ($deleteStmt->execute()) {
            $_SESSION['success_message'] = 'Teacher deleted successfully!';
        } else {
            $_SESSION['error_message'] = 'Failed to delete teacher.';
        }

        $deleteStmt->close();
    } catch (Exception $e) {
        $_SESSION['error_message'] = getDBErrorMessage($e->getCode());
    } finally {
        $conn->close();
    }
}

header("Location: $baseUrl/admin/teachers.php");
exit();
