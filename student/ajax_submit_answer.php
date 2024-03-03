<?php
// Include necessary files and initialize database connection
$userType = 'student';
require_once "../includes/config.php";

// Retrieve answer details
$examId = $_POST['exam_id'];
$questionId = $_POST['question_id'];
$selectedOptionId = $_POST['selected_option_id'];
$marks = $_POST['marks'];

// Query to insert/update student's answer
$insertAnswerQuery = "INSERT INTO student_answers (student_id, exam_id, question_id, selected_option_id, marks) 
                      VALUES (?, ?, ?, ?, ?)";
$insertAnswerStmt = $conn->prepare($insertAnswerQuery);
$insertAnswerStmt->bind_param("iiiii", $_SESSION['student_id'], $examId, $questionId, $selectedOptionId, $marks);
$insertAnswerStmt->execute();
$insertAnswerStmt->close();

$conn->close();
