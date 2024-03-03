<?php $userType = 'student' ?>
<?php require_once "../includes/config.php" ?>

<?php
// Redirect to login if not logged in
if (!isStudentLoggedIn()) {
    header("Location: $STUDENT_LOGIN_URL");
    exit();
}

// Retrieve exam ID from the query parameter
$examId = $_GET['exam_id'];

// Query to retrieve exam details
$examQuery = "SELECT exams.*, groups.name as group_name 
              FROM exams 
              LEFT JOIN groups ON exams.group_id = groups.id 
              WHERE exams.id = ?";

$examStmt = $conn->prepare($examQuery);
$examStmt->bind_param("i", $examId);
$examStmt->execute();
$examResult = $examStmt->get_result();

// Check if the exam exists
if ($examResult->num_rows === 0) {
    setErrorMessage('Exam does not exist!');
    header("Location: $baseUrl/student/exams.php");
    exit();
}

$exam = $examResult->fetch_assoc();

// Query to retrieve questions and options for the exam
$questionsQuery = "SELECT questions.*, options.id as option_id, options.is_correct, options.text as option_text, student_answers.selected_option_id
                   FROM questions
                   LEFT JOIN options ON questions.id = options.question_id
                   LEFT JOIN student_answers ON questions.id = student_answers.question_id AND student_answers.exam_id = ? AND student_answers.student_id = ?
                   WHERE questions.exam_id = ?";

$questionsStmt = $conn->prepare($questionsQuery);
$questionsStmt->bind_param("iii", $examId, $_SESSION['student_id'], $examId);
$questionsStmt->execute();
$questionsResult = $questionsStmt->get_result();

// Fetch questions and student answers
$questions = [];
$totalScore = 0;

while ($row = $questionsResult->fetch_assoc()) {
    $questionId = $row['id'];
    if (!isset($questions[$questionId])) {
        $questions[$questionId] = [
            'id' => $row['id'],
            'title' => $row['title'],
            'marks' => $row['marks'],
            'options' => [],
            'selected_option_id' => $row['selected_option_id'],
        ];
    }

    // Add each option to the question
    if (!is_null($row['option_text'])) {
        if ($row['is_correct'] && $row['option_id'] == $row['selected_option_id']) {
            $totalScore += $row['marks'];
        }

        $questions[$questionId]['options'][] = [
            'text' => $row['option_text'],
            'is_selected' => $row['option_id'] == $row['selected_option_id'],
            'is_correct' => $row['is_correct'],
        ];
    }
}

$questionsStmt->close();

$conn->close();
?>

<?php include $publicPath . '/includes/header.php' ?>

<!-- Begin Page Content -->
<div class="container-fluid">

    <!-- Page Heading -->
    <h1 class="h3 mb-4 text-gray-800">Exam Detail</h1>

    <div class="card shadow mb-4">
        <div class="card-header">
            <a href="<?= $baseUrl ?>/student/exams.php" class="btn btn-primary">
                <i class="fas fa-arrow-left"></i>
                <span>Back to Exams</span>
            </a>
        </div>
        <div class="card-body">
            <?php include "$publicPath/includes/message.php" ?>

            <h4 class="font-weight-bold"><?= $exam['title'] ?></h4>
            <p class="text-primary font-weight-bold">Total Score: <?= $totalScore ?></p>
            <hr>
            <h5>Questions List:</h5>
            <?php foreach ($questions as $i => $question) : ?>
                <div class="mb-4">
                    <p><?= ++$i ?>. <?= $question['title'] ?> (<?= $question['marks'] ?> marks)</p>
                    <div class="pl-3">
                        <?php foreach ($question['options'] as $option) : ?>
                            <div class="<?= $option['is_correct'] ? 'text-success font-weight-bold' : '' ?>">
                                <?= $option['text'] ?> <?= $option['is_selected'] ? '<strong>(Your Answer)</strong>' : '' ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
<!-- /.container-fluid -->

<?php include $publicPath . '/includes/footer.php' ?>