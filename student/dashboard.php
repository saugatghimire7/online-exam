<?php $userType = 'student' ?>
<?php require_once "../includes/config.php" ?>

<?php
// Redirect to login if not logged in
if (!isStudentLoggedIn()) {
    header("Location: $STUDENT_LOGIN_URL");
    exit();
}

?>

<?php include $publicPath . '/includes/header.php' ?>

<!-- Begin Page Content -->
<div class="container-fluid">

    <!-- Page Heading -->
    <h1 class="h3 mb-4 text-gray-800">Dashboard</h1>

    <div class="card shadow mb-4">
        <div class="card-body">
            <p>Welcome <?= $_SESSION['student_name'] ?></p>
        </div>
    </div>
</div>
<!-- /.container-fluid -->

<?php include $publicPath . '/includes/footer.php' ?>