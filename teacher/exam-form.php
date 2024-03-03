<?php
$userType = 'teacher';
require_once "../includes/config.php";

// Redirect to login if not logged in
if (!isTeacherLoggedIn()) {
    header("Location: $TEACHER_LOGIN_URL");
    exit();
}

$errors = [];

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $data = sanitizeInput($_POST);

    // Validate form data
    if (empty(trim($data['title']))) {
        $errors['title'] = "Title is required";
    }

    if (empty(trim($data['description']))) {
        $errors['description'] = "Description is required";
    }

    if (empty(trim($data['group_id']))) {
        $errors['group_id'] = "Group is required";
    }

    // Validate start and end times
    $startDateTime = strtotime($data['starts_at']);
    $endDateTime = strtotime($data['ends_at']);

    if ($endDateTime <= $startDateTime) {
        $errors['ends_at'] = "End time must be after start time";
    }

    // Update and create
    if (empty($errors)) {
        if (isset($_GET["exam_id"])) {
            // Update existing exam
            $updateSql = "UPDATE exams SET 
                      title = ?, 
                      description = ?, 
                      group_id = ?, 
                      starts_at = ?, 
                      ends_at = ?, 
                      is_published = ?, 
                      updated_at = CURRENT_TIMESTAMP 
                      WHERE id = ?";
            $updateStmt = $conn->prepare($updateSql);

            $updateStmt->bind_param(
                "ssissii",
                $data['title'],
                $data['description'],
                $data['group_id'],
                $data['starts_at'],
                $data['ends_at'],
                $data['is_published'],
                $_GET["exam_id"]
            );

            if ($updateStmt->execute()) {
                setSuccessMessage("Exam updated successfully!");
                header("Location: {$baseUrl}/teacher/exams.php");
                exit();
            } else {
                setErrorMessage("Error updating exam: " . $updateStmt->error);
            }
        } else {
            // Create a new exam
            $createSql = "INSERT INTO exams (title, description, group_id, starts_at, ends_at, is_published, teacher_id, created_at, updated_at) 
            VALUES (?, ?, ?, ?, ?, ?, ?, CURRENT_TIMESTAMP, NULL)";
            $createStmt = $conn->prepare($createSql);

            $createStmt->bind_param(
                "ssissii",
                $data['title'],
                $data['description'],
                $data['group_id'],
                $data['starts_at'],
                $data['ends_at'],
                $data['is_published'],
                $_SESSION['teacher_id']
            );

            if ($createStmt->execute()) {
                setSuccessMessage("Exam created successfully!");
                header("Location: {$baseUrl}/teacher/exams.php");
                exit();
            } else {
                setErrorMessage("Error creating exam: " . $createStmt->error);
            }
        }

        header("Location: {$_SERVER['REQUEST_URI']}");
        exit();
    }
}

// Query to retrieve assigned teacher groups for the select box
$groupQuery = "SELECT groups.id, groups.name FROM groups
               JOIN teacher_groups ON groups.id = teacher_groups.group_id
               WHERE teacher_groups.teacher_id = ?";
$groupStmt = $conn->prepare($groupQuery);
$groupStmt->bind_param("i", $_SESSION['teacher_id']);
$groupStmt->execute();
$groupResult = $groupStmt->get_result();
$groupStmt->close();

if (isset($_GET["exam_id"])) {
    $exam_id = $_GET["exam_id"];

    // Query to retrieve exams
    $sql = "SELECT * FROM exams WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $exam_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        // Exam does not exist
        setErrorMessage('Exam does not exist!');
        header("Location: $baseUrl/teacher/exams.php");
        exit();
    }

    $exam = $result->fetch_assoc();
}

// Close the database connection
$conn->close();
?>

<?php include $publicPath . '/includes/header.php' ?>

<!-- Begin Page Content -->
<div class="container-fluid">

    <!-- Page Heading -->
    <h1 class="h3 mb-4 text-gray-800"><?= isset($exam) ? 'Edit' : 'Create' ?> Exam</h1>

    <div class="card shadow mb-4">
        <div class="card-body">
            <?php include "$publicPath/includes/message.php" ?>

            <form action="" method="POST">
                <div class="form-group">
                    <label for="title">Title</label>
                    <input type="text" name="title" id="title" class="form-control" value="<?= isset($exam['title']) ? $exam['title'] : '' ?>">
                    <?= displayErrorText($errors['title'] ?? '') ?>
                </div>

                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea name="description" id="description" class="form-control"><?= isset($exam['description']) ? $exam['description'] : '' ?></textarea>
                    <?= displayErrorText($errors['description'] ?? '') ?>
                </div>

                <div class="form-group">
                    <label for="group_id">Group</label>
                    <select name="group_id" id="group_id" class="form-control">
                        <option value="">Select</option>
                        <?php while ($group = $groupResult->fetch_assoc()) : ?>
                            <option value="<?= $group['id'] ?>" <?= isset($exam['group_id']) && $exam['group_id'] == $group['id'] ? 'selected' : '' ?>>
                                <?= $group['name'] ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                    <?= displayErrorText($errors['group_id'] ?? '') ?>
                </div>

                <div class="form-group">
                    <label for="starts_at">Starts At</label>
                    <input type="datetime-local" name="starts_at" id="starts_at" class="form-control" value="<?= isset($exam['starts_at']) ? date('Y-m-d\TH:i', strtotime($exam['starts_at'])) : '' ?>">
                </div>

                <div class="form-group">
                    <label for="ends_at">Ends At</label>
                    <input type="datetime-local" name="ends_at" id="ends_at" class="form-control" value="<?= isset($exam['ends_at']) ? date('Y-m-d\TH:i', strtotime($exam['ends_at'])) : '' ?>">
                    <?= displayErrorText($errors['ends_at'] ?? '') ?>
                </div>

                <div class="form-group">
                    <label for="is_published">Is Published <small>(If published, students can view exam in their dashboard.)</small></label>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="is_published" id="published" value="1" <?= isset($exam['is_published']) && $exam['is_published'] == 1 ? 'checked' : '' ?>>
                        <label class="form-check-label" for="published">
                            Yes
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="is_published" id="not_published" value="0" <?= !isset($exam['is_published']) || isset($exam['is_published']) && $exam['is_published'] == 0 ? 'checked' : '' ?>>
                        <label class="form-check-label" for="not_published">
                            No
                        </label>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary"><?= isset($exam) ? 'Update' : 'Create' ?> Exam</button>
            </form>
        </div>
    </div>
</div>
<!-- /.container-fluid -->

<?php include $publicPath . '/includes/footer.php' ?>