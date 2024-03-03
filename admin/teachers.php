<?php $userType = 'admin' ?>
<?php
require_once "../includes/config.php";

// Redirect to login if not logged in
if (!isAdminLoggedIn()) {
    header("Location: $ADMIN_LOGIN_URL");
    exit();
}

// Query to retrieve teachers
$sql = "SELECT teachers.*, GROUP_CONCAT(`groups`.name SEPARATOR ', ') as group_names
    FROM teachers
    LEFT JOIN teacher_groups ON teachers.id = teacher_groups.teacher_id
    LEFT JOIN `groups` ON teacher_groups.group_id = `groups`.id
    GROUP BY teachers.id";
$result = $conn->query($sql);

// Initialize an array to store teachers
$teachers = [];

// Check if there are any results
if ($result->num_rows > 0) {
    // Fetch each row and add it to the array
    while ($row = $result->fetch_assoc()) {
        $teachers[] = $row;
    }
}

// Close the database connection
$conn->close();
?>

<?php include $publicPath . '/includes/header.php' ?>

<!-- Begin Page Content -->
<div class="container-fluid">

    <!-- Page Heading -->
    <h1 class="h3 mb-4 text-gray-800">Teachers</h1>

    <div class="card shadow mb-4">
        <div class="card-header">
            <a href="<?= $baseUrl ?>/admin/teacher-form.php" class="btn btn-success">
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
                    <th>Teacher Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Address</th>
                    <th>Qualification</th>
                    <th>Assigned Groups</th>
                    <th>CV</th>
                    <th>Register Date</th>
                    <th>Is Approved</th>
                    <th width="200">Action</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($teachers as $i => $teacher) { ?>
                    <tr>
                        <td><?= ++$i ?></td>
                        <td><?= $teacher['name'] ?></td>
                        <td><?= $teacher['email'] ?></td>
                        <td><?= $teacher['phone'] ?></td>
                        <td><?= $teacher['address'] ?></td>
                        <td><?= $teacher['qualification'] ?></td>
                        <td><?= $teacher['group_names'] ?></td>
                        <td>
                            <?php if (!empty($teacher['cv'])) { ?>
                                <a href="<?= $baseUrl ?>/uploads/<?= $teacher['cv'] ?>" target="_blank">Download CV</a>
                            <?php } else { ?>
                                N/A
                            <?php } ?>
                        </td>
                        <td><?= date_format(date_create($teacher['created_at']), 'Y-m-d') ?></td>
                        <td>
                                <span class="badge badge-pill badge-<?= $teacher['is_approved'] ? 'success' : 'danger' ?>">
                                    <?= $teacher['is_approved'] ? 'Yes' : 'No' ?>
                                </span>
                        </td>
                        <td>
                            <a href="<?= $baseUrl ?>/admin/teacher-form.php?teacher_id=<?= $teacher['id'] ?>"
                               class="btn btn-success">
                                <i class="fas fa-edit"></i>
                                <span>Edit</span>
                            </a>
                            <form class="d-inline-block" action="<?= $baseUrl ?>/admin/teacher-delete.php" method="POST"
                                  onsubmit="return confirm('Are you sure want to delete?')">
                                <input type="hidden" name="teacher_id" value="<?= $teacher['id'] ?>">
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
    $(document).ready(function () {
        $('#dataTable').DataTable();
    });
</script>