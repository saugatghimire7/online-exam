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

    if (empty(trim($data['marks']))) {
        $errors['marks'] = "Marks are required";
    }

    if (empty(trim($data['exam_id']))) {
        $errors['exam_id'] = "Exam ID is required";
    }

    if (!isset($data['options']) || count($data['options']) < 2) {
        $errors['options'] = "At least two options are required";
    } else {
        foreach ($data['options'] as $option) {
            if (empty(trim($option['text']))) {
                $errors['options'] = "Option text cannot be empty";
                break;
            }
        }
    }

    // Update and create
    if (empty($errors)) {
        try {
            $conn->begin_transaction();

            if (isset($_GET["question_id"])) {
                // Update existing question
                $updateSql = "UPDATE questions SET 
                          title = ?, 
                          marks = ?, 
                          exam_id = ?, 
                          updated_at = CURRENT_TIMESTAMP 
                          WHERE id = ?";
                $updateStmt = $conn->prepare($updateSql);

                $updateStmt->bind_param(
                    "siii",
                    $data['title'],
                    $data['marks'],
                    $data['exam_id'],
                    $_GET["question_id"]
                );

                if ($updateStmt->execute()) {
                    // Delete existing options
                    $deleteOptionsSql = "DELETE FROM options WHERE question_id = ?";
                    $deleteOptionsStmt = $conn->prepare($deleteOptionsSql);
                    $deleteOptionsStmt->bind_param("i", $_GET["question_id"]);
                    $deleteOptionsStmt->execute();
                    $deleteOptionsStmt->close();

                    // Insert new options
                    $insertOptionsSql = "INSERT INTO options (text, is_correct, question_id) VALUES (?, ?, ?)";
                    $insertOptionsStmt = $conn->prepare($insertOptionsSql);

                    foreach ($data['options'] as $option) {
                        $option['is_correct'] = $option['is_correct'] ? true : false;
                        $insertOptionsStmt->bind_param("sii", $option['text'], $option['is_correct'], $_GET["question_id"]);
                        $insertOptionsStmt->execute();
                    }

                    $insertOptionsStmt->close();

                    $conn->commit();

                    setSuccessMessage("Question updated successfully!");
                    header("Location: {$baseUrl}/teacher/questions.php");
                    exit();
                } else {
                    setErrorMessage("Error updating question: " . $updateStmt->error);
                }
            } else {
                // Create a new question
                $createSql = "INSERT INTO questions (title, marks, exam_id, created_at, updated_at) 
                VALUES (?, ?, ?, CURRENT_TIMESTAMP, NULL)";
                $createStmt = $conn->prepare($createSql);

                $createStmt->bind_param(
                    "sii",
                    $data['title'],
                    $data['marks'],
                    $data['exam_id']
                );

                if ($createStmt->execute()) {
                    $questionId = $createStmt->insert_id;

                    // Insert options
                    $insertOptionsSql = "INSERT INTO options (text, is_correct, question_id) VALUES (?, ?, ?)";
                    $insertOptionsStmt = $conn->prepare($insertOptionsSql);

                    foreach ($data['options'] as $option) {
                        $option['is_correct'] = $option['is_correct'] ? true : false;
                        $insertOptionsStmt->bind_param("sii", $option['text'], $option['is_correct'], $questionId);
                        $insertOptionsStmt->execute();
                    }

                    $insertOptionsStmt->close();

                    $conn->commit();

                    setSuccessMessage("Question created successfully!");
                    header("Location: {$baseUrl}/teacher/questions.php");
                    exit();
                } else {
                    setErrorMessage("Error creating question: " . $createStmt->error);
                }
            }
        } catch (Exception $e) {
            $conn->rollback();
            setErrorMessage("Error: " . $e->getMessage());
        } finally {
            $conn->close();
        }

        header("Location: {$_SERVER['REQUEST_URI']}");
        exit();
    }
}

// Query to retrieve exams for the select box
$examQuery = "SELECT id, title FROM exams";
$examResult = $conn->query($examQuery);

if (isset($_GET["question_id"])) {
    $question_id = $_GET["question_id"];

    // Query to retrieve questions
    $sql = "SELECT * FROM questions WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $question_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        // Question does not exist
        setErrorMessage('Question does not exist!');
        header("Location: $baseUrl/teacher/questions.php");
        exit();
    }

    $question = $result->fetch_assoc();

    // Query to retrieve options for the question
    $optionsSql = "SELECT * FROM options WHERE question_id = ?";
    $optionsStmt = $conn->prepare($optionsSql);
    $optionsStmt->bind_param("i", $question_id);
    $optionsStmt->execute();
    $optionsResult = $optionsStmt->get_result();

    // Initialize an array to store options
    $options = [];

    // Fetch each option and add it to the array
    while ($option = $optionsResult->fetch_assoc()) {
        $options[] = $option;
    }

    $optionsStmt->close();
}

// Close the database connection
$conn->close();
?>

<?php include $publicPath . '/includes/header.php' ?>

<!-- Begin Page Content -->
<div class="container-fluid">

    <!-- Page Heading -->
    <h1 class="h3 mb-4 text-gray-800"><?= isset($question) ? 'Edit' : 'Create' ?> Question</h1>

    <div class="card shadow mb-4">
        <div class="card-body">
            <?php include "$publicPath/includes/message.php" ?>

            <form action="" method="POST">
                <div class="form-group">
                    <label for="title">Title</label>
                    <input type="text" name="title" id="title" class="form-control" value="<?= isset($question['title']) ? $question['title'] : '' ?>">
                    <?= displayErrorText($errors['title'] ?? '') ?>
                </div>

                <div class="form-group">
                    <label for="marks">Marks</label>
                    <input type="number" name="marks" id="marks" class="form-control" value="<?= isset($question['marks']) ? $question['marks'] : '' ?>">
                    <?= displayErrorText($errors['marks'] ?? '') ?>
                </div>

                <div class="form-group">
                    <label for="exam_id">Exam</label>
                    <select name="exam_id" id="exam_id" class="form-control">
                        <option value="">Select</option>
                        <?php while ($exam = $examResult->fetch_assoc()) : ?>
                            <option value="<?= $exam['id'] ?>" <?= isset($question['exam_id']) && $question['exam_id'] == $exam['id'] ? 'selected' : '' ?>>
                                <?= $exam['title'] ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                    <?= displayErrorText($errors['exam_id'] ?? '') ?>
                </div>

                <div class="form-group">
                    <label for="options">Options</label>
                    <div id="options-container">
                        <?php foreach ($options ?? [] as $i => $option) : ?>
                            <div class="option-row mb-2">
                                <input type="text" name="options[<?= $i ?>][text]" class="form-control" placeholder="Option <?= $i + 1 ?>" value="<?= $option['text'] ?>">
                                <div class="form-check form-check-inline ml-2">
                                    <label class="form-check-label">
                                        <input class="form-check-input" type="checkbox" name="options[<?= $i ?>][is_correct]" value="1" <?= $option['is_correct'] ? 'checked' : '' ?>>
                                        <span>Correct</span>
                                    </label>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <button type="button" class="btn btn-success" id="add-option-btn">+ Add Option</button>
                    <?= displayErrorText($errors['options'] ?? '') ?>
                </div>

                <button type="submit" class="btn btn-primary"><?= isset($question) ? 'Update' : 'Create' ?> Question</button>
            </form>
        </div>
    </div>
</div>
<!-- /.container-fluid -->

<?php include $publicPath . '/includes/footer.php' ?>

<script>
    $(document).ready(function() {
        let optionCounter = <?= count($options ?? []) ?>;

        // Add option button click event
        $("#add-option-btn").click(function() {
            let optionRow = `<div class="option-row mb-2">
                                <input type="text" name="options[${optionCounter}][text]" class="form-control" placeholder="Option ${optionCounter + 1}">
                                <div class="form-check form-check-inline ml-2">
                                    <label class="form-check-label">
                                        <input class="form-check-input" type="checkbox" name="options[${optionCounter}][is_correct]" value="1">
                                        <span>Correct</span>
                                    </label>
                                </div>
                             </div>`;
            $("#options-container").append(optionRow);
            optionCounter++;
        });
    });
</script>