<?php $userType = 'admin' ?>
<?php
require_once "../includes/config.php";

// Redirect to login if not logged in
if (!isAdminLoggedIn()) {
    header("Location: $ADMIN_LOGIN_URL");
    exit();
}

// Query to retrieve groups
$sql = "SELECT * FROM `groups`";
$result = $conn->query($sql);

// Initialize an array to store groups
$groups = [];

// Check if there are any results
if ($result->num_rows > 0) {
    // Fetch each row and add it to the array
    while ($row = $result->fetch_assoc()) {
        $groups[] = $row;
    }
}

// Close the database connection
$conn->close();
?>

<?php include $publicPath . '/includes/header.php' ?>

<!-- Begin Page Content -->
<div class="container-fluid">

    <!-- Page Heading -->
    <h1 class="h3 mb-4 text-gray-800">Groups</h1>

    <div class="card shadow mb-4">
        <div class="card-header">
            <a href="<?= $baseUrl ?>/admin/group-form.php" class="btn btn-success">
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
                        <th>Group Name</th>
                        <th width="200">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($groups as  $i => $group) { ?>
                        <tr>
                            <td><?= ++$i ?></td>
                            <td><?= $group['name'] ?></td>
                            <td>
                                <a href="<?= $baseUrl ?>/admin/group-form.php?group_id=<?= $group['id'] ?>" class="btn btn-success">
                                    <i class="fas fa-edit"></i>
                                    <span>Edit</span>
                                </a>
                                <form class="d-inline-block" action="<?= $baseUrl ?>/admin/group-delete.php" method="POST" onsubmit="return confirm('Are you sure want to delete?')">
                                    <input type="hidden" name="group_id" value="<?= $group['id'] ?>">
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