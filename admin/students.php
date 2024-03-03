<?php $userType = 'admin' ?>
<?php
require_once "../includes/config.php";

// Redirect to login if not logged in
if (!isAdminLoggedIn()) {
    header("Location: $ADMIN_LOGIN_URL");
    exit();
}

// Query to retrieve students
$sql = "SELECT students.*, `groups`.name as group_name 
        FROM students 
        LEFT JOIN `groups` ON students.group_id = `groups`.id";
$result = $conn->query($sql);

// Initialize an array to store students
$students = [];

// Check if there are any results
if ($result->num_rows > 0) {
    // Fetch each row and add it to the array
    while ($row = $result->fetch_assoc()) {
        $students[] = $row;
    }
}

// Close the database connection
$conn->close();
?>

<?php include $publicPath . '/includes/header.php' ?>

<!-- Begin Page Content -->
<div class="container-fluid">

    <!-- Page Heading -->
    <h1 class="h3 mb-4 text-gray-800">Students</h1>

    <div class="card shadow mb-4">
        <div class="card-header">
            <a href="<?= $baseUrl ?>/admin/student-form.php" class="btn btn-success">
                <i class="fas fa-plus"></i>
                <span>Add New</span>
            </a>
        </div>
        <div class="card-body">
            <?php include "$publicPath/includes/message.php" ?>

            <table id="dataTable" class="table table-bordered">
                <thead>
                    <tr>
                        <th width="50">S.N.</th>
                        <th>Student Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Address</th>
                        <th>Group</th>
                        <th>Register Date</th>
                        <th>Is Approved</th>
                        <th width="200">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($students as  $i => $student) { ?>
                        <tr>
                            <td><?= ++$i ?></td>
                            <td><?= $student['name'] ?></td>
                            <td><?= $student['email'] ?></td>
                            <td><?= $student['phone'] ?></td>
                            <td><?= $student['address'] ?></td>
                            <td><?= $student['group_name'] ?></td>
                            <td><?= date_format(date_create($student['created_at']), 'Y-m-d') ?></td>
                            <td>
                                <span class="badge badge-pill badge-<?= $student['is_approved'] ? 'success' : 'danger' ?>">
                                    <?= $student['is_approved'] ? 'Yes' : 'No' ?>
                                </span>
                            </td>
                            <td>
                                <a href="<?= $baseUrl ?>/admin/student-form.php?student_id=<?= $student['id'] ?>" class="btn btn-success">
                                    <i class="fas fa-edit"></i>
                                    <span>Edit</span>
                                </a>
                                <form class="d-inline-block" action="<?= $baseUrl ?>/admin/student-delete.php" method="POST" onsubmit="return confirm('Are you sure want to delete?')">
                                    <input type="hidden" name="student_id" value="<?= $student['id'] ?>">
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