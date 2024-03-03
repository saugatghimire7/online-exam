<?php $userType = 'admin' ?>
<?php
require_once "../includes/config.php";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["group_id"])) {
    $group_id = $_POST["group_id"];

    try {
        // Attempt to delete the group
        $deleteSql = "DELETE FROM groups WHERE id = ?";
        $deleteStmt = $conn->prepare($deleteSql);
        $deleteStmt->bind_param("i", $group_id);

        if ($deleteStmt->execute()) {
            $_SESSION['success_message'] = 'Group deleted successfully!';
        } else {
            $_SESSION['error_message'] = 'Failed to delete group.';
        }

        $deleteStmt->close();
    } catch (Exception $e) {
        $_SESSION['error_message'] = getDBErrorMessage($e->getCode());
    } finally {
        $conn->close();
    }
}

header("Location: $baseUrl/admin/groups.php");
exit();
