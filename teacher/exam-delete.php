<?php
$userType = 'teacher';
require_once "../includes/config.php";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["exam_id"])) {
    $exam_id = $_POST["exam_id"];

    try {
        // Attempt to delete the exam
        $deleteSql = "DELETE FROM exams WHERE id = ?";
        $deleteStmt = $conn->prepare($deleteSql);
        $deleteStmt->bind_param("i", $exam_id);

        if ($deleteStmt->execute()) {
            $_SESSION['success_message'] = 'Exam deleted successfully!';
        } else {
            $_SESSION['error_message'] = 'Failed to delete exam.';
        }

        $deleteStmt->close();
    } catch (Exception $e) {
        $_SESSION['error_message'] = getDBErrorMessage($e->getCode());
    } finally {
        $conn->close();
    }
}

header("Location: $baseUrl/teacher/exams.php");
exit();
