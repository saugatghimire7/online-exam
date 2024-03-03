<?php
$userType = 'student';
require_once "../includes/config.php";

// Redirect to login if not logged in
if (!isStudentLoggedIn()) {
    header("Location: $STUDENT_LOGIN_URL");
    exit();
}

$errors = [];

// Check if exam_id is provided in GET parameters
if (!isset($_GET["exam_id"]) || empty(trim($_GET["exam_id"]))) {
    setErrorMessage("Invalid exam ID");
    header("Location: $baseUrl/student/exams.php");
    exit();
}

$examId = $_GET["exam_id"];

// Check if student is in the exam group, exam is published, and has questions
$checkExamQuery = "SELECT exams.*
                   FROM exams
                   INNER JOIN groups ON exams.group_id = groups.id
                   INNER JOIN students ON groups.id = students.group_id
                   WHERE exams.id = ? AND exams.is_published = 1 AND students.id = ?
                   AND EXISTS (
                       SELECT 1
                       FROM questions
                       WHERE questions.exam_id = exams.id
                   )";
$checkExamStmt = $conn->prepare($checkExamQuery);
$checkExamStmt->bind_param("ii", $examId, $_SESSION['student_id']);
$checkExamStmt->execute();
$checkExamResult = $checkExamStmt->get_result();

if ($checkExamResult->num_rows === 0) {
    setErrorMessage("You are not allowed to take this exam or the exam has no questions.");
    header("Location: $baseUrl/student/exams.php");
    exit();
}

$exam = $checkExamResult->fetch_assoc();
$checkExamStmt->close();


// Query to retrieve total question count in an exam
$totalQuestionCountQuery = "
    SELECT COUNT(*) AS total_questions
    FROM questions
    WHERE exam_id = ?
";
$totalQuestionCountStmt = $conn->prepare($totalQuestionCountQuery);
$totalQuestionCountStmt->bind_param("i", $examId);
$totalQuestionCountStmt->execute();
$totalQuestionCountResult = $totalQuestionCountStmt->get_result();

// Get the total question count
$totalQuestionCount = $totalQuestionCountResult->fetch_assoc()['total_questions'];
$totalQuestionCountStmt->close();


// Fetch all unanswered question IDs
$unansweredQuestionIdsQuery = "
    SELECT q.id
    FROM questions q
    WHERE q.exam_id = ? AND q.id NOT IN (
        SELECT sa.question_id
        FROM student_answers sa
        WHERE sa.exam_id = ? AND sa.student_id = ?
    )
";

$unansweredQuestionIdsStmt = $conn->prepare($unansweredQuestionIdsQuery);
$unansweredQuestionIdsStmt->bind_param("iii", $examId, $examId, $_SESSION['student_id']);
$unansweredQuestionIdsStmt->execute();
$unansweredQuestionIdsResult = $unansweredQuestionIdsStmt->get_result();

$unansweredQuestionIds = [];
while ($row = $unansweredQuestionIdsResult->fetch_assoc()) {
    $unansweredQuestionIds[] = $row['id'];
}

// Get list of shuffled question ids from unanswered question ids
$shuffledQuestionIds = shuffleQuestionIds($unansweredQuestionIds);

$unansweredQuestionIdsStmt->close();
$conn->close();
?>

<?php include $publicPath . '/includes/header.php' ?>

<!-- Begin Page Content -->
<div class="container-fluid">

    <!-- Page Heading -->
    <h1 class="h3 mb-4 text-gray-800"><?= $exam['title'] ?></h1>

    <div class="card shadow mb-4">
        <div class="card-body">
            <?php include "$publicPath/includes/message.php" ?>

            <div class="row mb-3">
                <div class="col-6">Total Questions: <?= $totalQuestionCount ?>, Remaining: <span id="remaining-question-count"><?= count($shuffledQuestionIds) ?></span></div>
                <div class="col-6 text-right font-weight-bold text-info" style="height: 24px;">
                    <div id="timer"></div>
                </div>
            </div>

            <div id="question-container" class="mb-3"></div>

            <button class="btn btn-primary" id="submit-answer-btn" style="display: none;">Submit Answer</button>
            <a href="<?= $baseUrl ?>/student/exam-result-detail.php?exam_id=<?= $examId ?>" class="btn btn-success" id="view-result-btn" style="display: none;">View Result</a>
        </div>
    </div>
</div>
<!-- /.container-fluid -->

<?php include $publicPath . '/includes/footer.php' ?>

<script>
    $(document).ready(function() {
        let shuffledQuestionIds = <?= json_encode($shuffledQuestionIds) ?>;
        let currentQuestionIndex = 0;
        let timer;

        // Load the next question
        function loadNextQuestion() {
            if (currentQuestionIndex < shuffledQuestionIds.length) {
                let questionId = shuffledQuestionIds[currentQuestionIndex];
                $.ajax({
                    type: "POST",
                    url: "<?= $baseUrl ?>/student/ajax_load_question.php",
                    data: {
                        exam_id: <?= $examId ?>,
                        question_id: questionId,
                    },
                    success: function(response) {
                        // display question
                        $("#question-container").html(response);
                        $('#remaining-question-count').html(shuffledQuestionIds.length - currentQuestionIndex);

                        $("#submit-answer-btn").prop("disabled", false).show();
                        $("#view-result-btn").hide();

                        currentQuestionIndex++;
                    }
                });
            } else {
                // No more unanswered questions
                $("#question-container").html('You have answered all the questions.');
                $('#remaining-question-count').html(0);

                clearInterval(timer);
                $("#submit-answer-btn").hide();
                $("#view-result-btn").show();
            }
        }

        // Start the timer
        function startTimer() {
            let now = new Date().getTime();
            let endTime = new Date("<?= $exam['ends_at'] ?>").getTime();

            // Calculate the remaining time for the exam in seconds
            let totalTimeInSeconds = (endTime - now) / 1000;

            let timeRemaining = totalTimeInSeconds;

            timer = setInterval(function() {
                let minutes = Math.floor(timeRemaining / 60);
                let seconds = Math.round(timeRemaining % 60).toString().padStart(2, '0');

                $("#timer").text(`Time Remaining: ${minutes}:${seconds}`);

                if (timeRemaining <= 0) {
                    // Time is up, redirect to results page
                    window.location.href = "<?= $baseUrl ?>/student/exam-result-detail.php?exam_id=<?= $examId ?>"
                }

                timeRemaining--;
            }, 1000);
        }


        // Submit the answer for the current question
        function submitAnswer() {
            let selectedOptionId = $("input[name='selected_option']:checked").val();

            if (selectedOptionId === undefined) {
                alert("Please select an option before submitting.");
                return;
            }

            // AJAX call to submit the answer
            $.ajax({
                type: "POST",
                url: "<?= $baseUrl ?>/student/ajax_submit_answer.php",
                data: {
                    exam_id: <?= $examId ?>,
                    question_id: $("#question_id").val(),
                    selected_option_id: selectedOptionId,
                    marks: $("#marks").val(),
                },
                success: function(response) {
                    loadNextQuestion();
                }
            });
        }

        // Event listener for the submit answer button
        $("#submit-answer-btn").click(function() {
            submitAnswer();
        });

        // Initial load of the first question
        startTimer();
        loadNextQuestion();
    });
</script>