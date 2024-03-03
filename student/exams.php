<?php $userType = 'student' ?>
<?php require_once "../includes/config.php" ?>

<?php
// Redirect to login if not logged in
if (!isStudentLoggedIn()) {
    header("Location: $STUDENT_LOGIN_URL");
    exit();
}

// Query to retrieve exams that has at least one question for students with associated questions
$sql = "SELECT DISTINCT exams.*, 
               COALESCE(teachers.name, admins.name) AS creator_name,
               groups.name as group_name 
        FROM exams 
        LEFT JOIN groups ON exams.group_id = groups.id 
        LEFT JOIN teachers ON exams.teacher_id = teachers.id 
        LEFT JOIN admins ON exams.admin_id = admins.id 
        INNER JOIN questions ON exams.id = questions.exam_id
        WHERE exams.is_published = 1 
          AND exams.group_id IN (
            SELECT group_id FROM students WHERE id = " . $_SESSION['student_id'] . "
        )";
$result = $conn->query($sql);

// Initialize an array to store exams
$exams = [];

// Check if there are any results
if ($result->num_rows > 0) {
    // Fetch each row and add it to the array
    while ($row = $result->fetch_assoc()) {
        $exams[] = $row;
    }
}

// Close the database connection
$conn->close();
?>

<?php include $publicPath . '/includes/header.php' ?>

<!-- Begin Page Content -->
<div class="container-fluid">

    <!-- Page Heading -->
    <h1 class="h3 mb-4 text-gray-800">Exams</h1>

    <?php include "$publicPath/includes/message.php" ?>

    <div class="row">
        <?php foreach ($exams as $i => $exam) { ?>
            <div class="col-md-4 mb-4">
                <div class="card shadow exam-card" data-start="<?= $exam['starts_at'] ?>" data-end="<?= $exam['ends_at'] ?>">
                    <div class="card-body">
                        <h5 class="card-title"><?= $exam['title'] ?></h5>
                        <p class="card-text"><?= $exam['description'] ?></p>
                        <p class="card-text">Creator: <?= $exam['creator_name'] ?></p>
                        <p class="card-text">Group: <?= $exam['group_name'] ?></p>
                        <p class="card-text">Starts At: <?= date_format(date_create($exam['starts_at']), 'Y-m-d h:i A') ?></p>
                        <p class="card-text">Ends At: <?= date_format(date_create($exam['ends_at']), 'Y-m-d h:i A') ?></p>

                        <!-- Timer for exams not started yet -->
                        <div class="font-weight-bold mb-2 text-info">
                            <div id="timer<?= $i ?>"></div>
                        </div>

                        <!-- Exam buttons -->
                        <a href="<?= $baseUrl ?>/student/exam-start.php?exam_id=<?= $exam['id'] ?>" class="btn btn-primary btn-exam-start" style="display: none;" ?>
                            <i class="fas fa-play"></i> Start Exam
                        </a>
                        <a href="<?= $baseUrl ?>/student/exam-result-detail.php?exam_id=<?= $exam['id'] ?>" class="btn btn-success btn-exam-result" style="display: none;">
                            View Result
                        </a>
                    </div>
                </div>
            </div>
        <?php } ?>
    </div>
</div>
<!-- /.container-fluid -->

<?php include $publicPath . '/includes/footer.php' ?>

<script src="https://cdn.rawgit.com/hilios/jQuery.countdown/2.2.0/dist/jquery.countdown.min.js"></script>

<script>
    function handleButtonVisibility(examCard, startsAt, endsAt) {
        var startBtn = examCard.querySelector('.btn-exam-start');
        var viewBtn = examCard.querySelector('.btn-exam-result');

        var currentTime = new Date().getTime();
        var startTime = new Date(startsAt).getTime();
        var endTime = new Date(endsAt).getTime();

        if (currentTime < startTime) {
            startBtn.classList.add('disabled');
            startBtn.style.display = 'block';
        } else if (currentTime >= startTime && currentTime <= endTime) {
            startBtn.classList.remove('disabled');
            startBtn.style.display = 'block';
            viewBtn.style.display = 'none';
        } else {
            startBtn.style.display = 'none';
            viewBtn.style.display = 'block';
        }
    }

    $(function() {
        document.querySelectorAll('.exam-card').forEach((examCard, index) => {
            const startsAt = examCard.getAttribute('data-start');
            const endsAt = examCard.getAttribute('data-end');
            handleButtonVisibility(examCard, startsAt, endsAt);

            // Countdown timer for exams not started yet
            if (new Date(startsAt).getTime() > new Date().getTime()) {
                $('#timer' + index).countdown(startsAt, function(event) {
                    $(this).html(event.strftime('%H:%M:%S'));
                }).on('finish.countdown', function() {
                    // Call handleButtonVisibility once the timer turns 0
                    handleButtonVisibility(examCard, startsAt, endsAt);
                });
            }
        });
    });
</script>