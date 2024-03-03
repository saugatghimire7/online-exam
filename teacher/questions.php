<?php $userType = 'teacher' ?>
<?php require_once "../includes/config.php" ?>

<?php
// Redirect to login if not logged in
if (!isTeacherLoggedIn()) {
    header("Location: $TEACHER_LOGIN_URL");
    exit();
}

// Query to retrieve questions and options
$sql = "SELECT questions.*, exams.title as exam_title, options.text as option_text, options.is_correct
        FROM questions 
        LEFT JOIN exams ON questions.exam_id = exams.id
        LEFT JOIN options ON questions.id = options.question_id
        WHERE exams.teacher_id = " . $_SESSION['teacher_id'];
$result = $conn->query($sql);

// Initialize an array to store questions and options
$questions = [];

// Check if there are any results
if ($result->num_rows > 0) {
    // Fetch each row and add it to the array
    while ($row = $result->fetch_assoc()) {
        $questionId = $row['id'];
        if (!isset($questions[$questionId])) {
            $questions[$questionId] = $row;
            $questions[$questionId]['options'] = [];
        }

        // Add options to the question
        if (!empty($row['option_text'])) {
            $questions[$questionId]['options'][] = [
                'text' => $row['option_text'],
                'is_correct' => $row['is_correct']
            ];
        }
    }
}

// Close the database connection
$conn->close();
?>

<?php include $publicPath . '/includes/header.php' ?>

<!-- Begin Page Content -->
<div class="container-fluid">

    <!-- Page Heading -->
    <h1 class="h3 mb-4 text-gray-800">Questions</h1>

    <div class="card shadow mb-4">
        <div class="card-header">
            <a href="<?= $baseUrl ?>/teacher/question-form.php" class="btn btn-success">
                <i class="fas fa-plus"></i>
                <span>Create New Question</span>
            </a>
        </div>
        <div class="card-body">
            <?php include "$publicPath/includes/message.php" ?>

            <table id="dataTable" class="table table-bordered">
                <thead>
                    <tr>
                        <th width="50">S.N.</th>
                        <th>Title</th>
                        <th width="200">Options</th>
                        <th>Marks</th>
                        <th>Exam</th>
                        <th>Created At</th>
                        <th>Updated At</th>
                        <th width="200">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $j = 0 ?>
                    <?php foreach ($questions as $i => $question) { ?>
                        <tr>
                            <td><?= ++$j ?></td>
                            <td><?= $question['title'] ?></td>
                            <td>
                                <?php if (!empty($question['options'])) { ?>
                                    <ul>
                                        <?php foreach ($question['options'] as $option) { ?>
                                            <li <?= $option['is_correct'] ? 'style="font-weight: bold; color: green;"' : '' ?>>
                                                <?= $option['text'] ?>
                                            </li>
                                        <?php } ?>
                                    </ul>
                                <?php } ?>
                            </td>
                            <td><?= $question['marks'] ?></td>
                            <td><?= $question['exam_title'] ?></td>
                            <td><?= $question['created_at'] ?></td>
                            <td><?= $question['updated_at'] ?></td>
                            <td>
                                <a href="<?= $baseUrl ?>/teacher/question-form.php?question_id=<?= $question['id'] ?>" class="btn btn-success">
                                    <i class="fas fa-edit"></i>
                                    <span>Edit</span>
                                </a>
                                <form class="d-inline-block" action="<?= $baseUrl ?>/teacher/question-delete.php" method="POST" onsubmit="return confirm('Are you sure want to delete?')">
                                    <input type="hidden" name="question_id" value="<?= $question['id'] ?>">
                                    <button type="submit" class="btn btn-danger">
                                        <i class="fas fa-trash"></i>
                                        <span>Delete</span>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<!-- /.container-fluid -->

<?php include $publicPath . '/includes/footer.php' ?>

<script>
    $(document).ready(function() {
        $('#dataTable').DataTable();
    });
</script>