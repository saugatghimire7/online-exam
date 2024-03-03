<?php
require_once "../includes/config.php";

// Redirect to login if not logged in
if (!isAdminLoggedIn()) {
    header("Location: $ADMIN_LOGIN_URL");
    exit();
}

// Save or update
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $data = sanitizeInput($_POST);
    $errors = [];

    if (!isset($data['name']) || empty(trim($data['name']))) {
        $errors['name'] = "Group Name is required";
    }

    // update and create
    if (empty($errors)) {
        if (isset($_GET["group_id"])) {
            // Update existing group
            $updateSql = "UPDATE groups SET name = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?";
            $updateStmt = $conn->prepare($updateSql);
            $updateStmt->bind_param("si", $data['name'], $_GET["group_id"]);

            if ($updateStmt->execute()) {
                setSuccessMessage("Group updated successfully!");
                header("Location: {$baseUrl}/admin/groups.php");
                exit();
            } else {
                setErrorMessage("Error updating group: " . $updateStmt->error);
            }
        } else {
            // Create a new group
            $createSql = "INSERT INTO `groups` (name, created_at, updated_at) VALUES (?, CURRENT_TIMESTAMP, NULL)";
            $createStmt = $conn->prepare($createSql);
            $createStmt->bind_param("s", $data['name']);

            if ($createStmt->execute()) {
                setSuccessMessage("Group created successfully!");
                header("Location: {$baseUrl}/admin/groups.php");
                exit();
            } else {
                setErrorMessage("Error creating group: " . $createStmt->error);
            }
        }

        header("Location: {$_SERVER['REQUEST_URI']}");
        exit();
    }
}

if (isset($_GET["group_id"])) {
    $group_id = $_GET["group_id"];

    // Query to retrieve groups
    $sql = "SELECT * FROM groups WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $group_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        // Group does not exist
        setErrorMessage('Group does not exist!');
        header("Location: $baseUrl/admin/groups.php");
        exit();
    }

    $group = $result->fetch_assoc();
}

// Close the database connection
$conn->close();
?>

<?php $userType = 'admin' ?>
<?php include $publicPath . '/includes/header.php' ?>

<!-- Begin Page Content -->
<div class="container-fluid">

    <!-- Page Heading -->
    <h1 class="h3 mb-4 text-gray-800">Add Group</h1>

    <div class="card shadow mb-4">
        <div class="card-body">
            <?php include "$publicPath/includes/message.php" ?>

            <form action="" method="POST">
                <label>Group Name</label>
                <div class="form-group">
                    <input type="text" class="form-control" name="name" value="<?= $group['name'] ?? '' ?>">
                    <?= displayErrorText($errors['name'] ?? '') ?>
                </div>

                <button type="submit" class="btn btn-primary"><?= isset($group) ? 'Update' : 'Save' ?> Group</button>
            </form>
        </div>
    </div>
</div>
<!-- /.container-fluid -->

<?php include $publicPath . '/includes/footer.php' ?>