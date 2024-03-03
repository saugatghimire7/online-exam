<?php $userType = 'teacher' ?>
<?php require_once "../includes/config.php" ?>

<?php
// Redirect to login if not logged in
if (!isTeacherLoggedIn()) {
    header("Location: $TEACHER_LOGIN_URL");
    exit();
}

// Query to retrieve exams
$sql = "SELECT exams.*, groups.name as group_name 
        FROM exams 
        LEFT JOIN groups ON exams.group_id = groups.id 
        WHERE exams.teacher_id = " . $_SESSION['teacher_id'];
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

    <div class="card shadow mb-4">
        <div class="card-header">
            <a href="<?= $baseUrl ?>/teacher/exam-form.php" class="btn btn-success">
                <i class="fas fa-plus"></i>
                <span>Create New Exam</span>
            </a>
        </div>
        <div class="card-body">
            <?php include "$publicPath/includes/message.php" ?>

            <table id="dataTable" class="table table-bordered">
                <thead>
                    <tr>
                        <th width="50">S.N.</th>
                        <th>Title</th>
                        <th>Description</th>
                        <th>Group</th>
                        <th>Starts At</th>
                        <th>Ends At</th>
                        <th>Is Published</th>
                        <th>Created At</th>
                        <th>Updated At</th>
                        <th width="200">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($exams as $i => $exam) { ?>
                        <tr>
                            <td><?= ++$i ?></td>
                            <td><?= $exam['title'] ?></td>
                            <td><?= $exam['description'] ?></td>
                            <td><?= $exam['group_name'] ?></td>
                            <td><?= date_format(date_create($exam['starts_at']), 'Y-m-d <br/> h:i A') ?></td>
                            <td><?= date_format(date_create($exam['ends_at']), 'Y-m-d <br/> h:i A') ?></td>
                            <td>
                                <span class="badge badge-pill badge-<?= $exam['is_published'] ? 'success' : 'danger' ?>">
                                    <?= $exam['is_published'] ? 'Yes' : 'No' ?>
                                </span>
                            </td>
                            <td><?= $exam['created_at'] ?></td>
                            <td><?= $exam['updated_at'] ?></td>
                            <td>
                                <a href="<?= $baseUrl ?>/teacher/exam-form.php?exam_id=<?= $exam['id'] ?>" class="btn btn-success">
                                    <i class="fas fa-edit"></i>
                                    <span>Edit</span>
                                </a>
                                <form class="d-inline-block" action="<?= $baseUrl ?>/teacher/exam-delete.php" method="POST" onsubmit="return confirm('Are you sure want to delete?')">
                                    <input type="hidden" name="exam_id" value="<?= $exam['id'] ?>">
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