<?php
$userType = 'teacher';
require_once "../includes/config.php";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["question_id"])) {
    $question_id = $_POST["question_id"];

    try {
        // Query to get the teacher_id associated with the question's exam
        $examTeacherQuery = "SELECT exams.teacher_id 
                             FROM questions
                             JOIN exams ON questions.exam_id = exams.id
                             WHERE questions.id = ?";
        $examTeacherStmt = $conn->prepare($examTeacherQuery);
        $examTeacherStmt->bind_param("i", $question_id);
        $examTeacherStmt->execute();
        $examTeacherResult = $examTeacherStmt->get_result();

        if ($examTeacherResult->num_rows === 1) {
            $examTeacherData = $examTeacherResult->fetch_assoc();

            // Check if the teacher_id matches the logged-in teacher
            if ($examTeacherData['teacher_id'] == $_SESSION['teacher_id']) {
                // Attempt to delete the question and its associated options
                $deleteSql = "DELETE FROM questions WHERE id = ?";
                $deleteStmt = $conn->prepare($deleteSql);
                $deleteStmt->bind_param("i", $question_id);

                if ($deleteStmt->execute()) {
                    $_SESSION['success_message'] = 'Question deleted successfully!';
                } else {
                    $_SESSION['error_message'] = 'Failed to delete question.';
                }

                $deleteStmt->close();
            } else {
                $_SESSION['error_message'] = 'You do not have permission to delete this question.';
            }
        } else {
            $_SESSION['error_message'] = 'Question not found.';
        }

        $examTeacherStmt->close();
    } catch (Exception $e) {
        $_SESSION['error_message'] = getDBErrorMessage($e->getCode());
    } finally {
        $conn->close();
    }
}

header("Location: $baseUrl/teacher/questions.php");
exit();
