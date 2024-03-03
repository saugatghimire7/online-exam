<?php
// Include necessary files and initialize database connection
$userType = 'student';
require_once "../includes/config.php";

// Retrieve exam details
$examId = $_POST['exam_id'];

// Query to retrieve a random unanswered question for the exam
$randomUnansweredQuestionQuery = "
    SELECT q.id, q.title, q.marks
    FROM questions q
    WHERE q.exam_id = ? AND q.id NOT IN (
        SELECT sa.question_id
        FROM student_answers sa
        WHERE sa.exam_id = ? AND sa.student_id = ?
    )
    ORDER BY RAND()
    LIMIT 1
";

$randomUnansweredQuestionStmt = $conn->prepare($randomUnansweredQuestionQuery);
$randomUnansweredQuestionStmt->bind_param("iii", $examId, $examId, $_SESSION['student_id']);
$randomUnansweredQuestionStmt->execute();
$randomUnansweredQuestionResult = $randomUnansweredQuestionStmt->get_result();

if ($randomUnansweredQuestionResult->num_rows > 0) {
    $question = $randomUnansweredQuestionResult->fetch_assoc();

    // Query to retrieve options for the question
    $optionsQuery = "SELECT * FROM options WHERE question_id = ?";
    $optionsStmt = $conn->prepare($optionsQuery);
    $optionsStmt->bind_param("i", $question['id']);
    $optionsStmt->execute();
    $optionsResult = $optionsStmt->get_result();

    // Initialize an array to store options
    $options = [];

    // Fetch each option and add it to the array
    while ($option = $optionsResult->fetch_assoc()) {
        $options[] = $option;
    }

    $optionsStmt->close();
} else {
    // No unanswered questions found
    echo "";
    exit();
}

$randomUnansweredQuestionStmt->close();
$conn->close();
?>

<!-- Display the question and options -->
<input id="question_id" type="hidden" value="<?= $question['id']; ?>">
<input id="marks" type="hidden" value="<?= $question['marks']; ?>">
<h5 class="card-title"><?= $question['title'] ?></h5>
<form id="answer-form">
    <?php foreach ($options as $i => $option) : ?>
        <div class="form-check">
            <input class="form-check-input" type="radio" name="selected_option" id="option<?= $i ?>" value="<?= $option['id'] ?>">
            <label class="form-check-label" for="option<?= $i ?>">
                <?= $option['text'] ?>
            </label>
        </div>
    <?php endforeach; ?>
</form>