<?php $userType = 'admin' ?>
<?php
require_once "../includes/config.php";

// Redirect to login if not logged in
if (!isAdminLoggedIn()) {
    header("Location: $ADMIN_LOGIN_URL");
    exit();
}

$errors = [];

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $data = sanitizeInput($_POST);

    // Validate form data
    if (empty(trim($data['name']))) {
        $errors['name'] = "Name is required";
    }

    if (empty(trim($data['email']))) {
        $errors['email'] = "Email is required";
    } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Invalid email format";
    }

    // Password optional on update
    if (!isset($_GET["student_id"]) && empty(trim($data['password']))) {
        $errors['password'] = "Password is required";
    }

    if (empty(trim($data['phone']))) {
        $errors['phone'] = "Phone number is required";
    }

    if (empty(trim($data['group_id']))) {
        $errors['group_id'] = "Group is required";
    }

    // Update and create
    if (empty($errors)) {
        if (isset($_GET["student_id"])) {
            // Update existing student
            $updateSql = "UPDATE students SET 
                      name = ?, 
                      email = ?, 
                      " . (!empty($data['password']) ? "password = ?, " : "") .
                "phone = ?, 
                      address = ?, 
                      is_approved = ?,
                      group_id = ?, 
                      updated_at = CURRENT_TIMESTAMP 
                      WHERE id = ?";
            $updateStmt = $conn->prepare($updateSql);

            // Hash the password if provided
            if (!empty($data['password'])) {
                $hashedPassword = password_hash($data['password'], PASSWORD_BCRYPT);
                // Bind password parameter
                $updateStmt->bind_param(
                    "sssssiii",
                    $data['name'],
                    $data['email'],
                    $hashedPassword,
                    $data['phone'],
                    $data['address'],
                    $data['is_approved'],
                    $data['group_id'],
                    $_GET["student_id"]
                );
            } else {
                // If password is not provided, exclude it from binding
                $updateStmt->bind_param(
                    "ssssiii",
                    $data['name'],
                    $data['email'],
                    $data['phone'],
                    $data['address'],
                    $data['is_approved'],
                    $data['group_id'],
                    $_GET["student_id"]
                );
            }

            if ($updateStmt->execute()) {
                setSuccessMessage("Student updated successfully!");
                header("Location: {$baseUrl}/admin/students.php");
                exit();
            } else {
                setErrorMessage("Error updating student: " . $updateStmt->error);
            }
        } else {
            // Create a new student
            $createSql = "INSERT INTO students (name, email, password, phone, address, is_approved, group_id, created_at, updated_at) 
            VALUES (?, ?, ?, ?, ?, ?, ?, CURRENT_TIMESTAMP, NULL)";
            $createStmt = $conn->prepare($createSql);
            $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);
            $createStmt->bind_param("sssssii", $data['name'], $data['email'], $hashedPassword, $data['phone'], $data['address'], $data['is_approved'], $data['group_id']);

            if ($createStmt->execute()) {
                setSuccessMessage("Student created successfully!");
                header("Location: {$baseUrl}/admin/students.php");
                exit();
            } else {
                setErrorMessage("Error creating student: " . $createStmt->error);
            }
        }

        header("Location: {$_SERVER['REQUEST_URI']}");
        exit();
    }
}

// Query to retrieve groups for the select box
$groupQuery = "SELECT id, name FROM groups";
$groupResult = $conn->query($groupQuery);

if (isset($_GET["student_id"])) {
    $student_id = $_GET["student_id"];

    // Query to retrieve groups
    $sql = "SELECT * FROM students WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $student_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        // Group does not exist
        setErrorMessage('Student does not exist!');
        header("Location: $baseUrl/admin/students.php");
        exit();
    }

    $student = $result->fetch_assoc();
}

// Close the database connection
$conn->close();
?>

<?php include $publicPath . '/includes/header.php' ?>

<!-- Begin Page Content -->
<div class="container-fluid">

    <!-- Page Heading -->
    <h1 class="h3 mb-4 text-gray-800">Add Student</h1>

    <div class="card shadow mb-4">
        <div class="card-body">
            <?php include "$publicPath/includes/message.php" ?>

            <form action="" method="POST">
                <div class="form-group">
                    <label for="name">Name</label>
                    <input type="text" name="name" id="name" class="form-control" value="<?= isset($student['name']) ? $student['name'] : '' ?>">
                    <?= displayErrorText($errors['name'] ?? '') ?>
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" name="email" id="email" class="form-control" value="<?= isset($student['email']) ? $student['email'] : '' ?>">
                    <?= displayErrorText($errors['email'] ?? '') ?>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" name="password" id="password" class="form-control">
                    <?= displayErrorText($errors['password'] ?? '') ?>
                </div>

                <div class="form-group">
                    <label for="phone">Phone</label>
                    <input type="text" name="phone" id="phone" class="form-control" value="<?= isset($student['phone']) ? $student['phone'] : '' ?>">
                    <?= displayErrorText($errors['phone'] ?? '') ?>
                </div>

                <div class="form-group">
                    <label for="address">Address</label>
                    <input type="text" name="address" id="address" class="form-control" value="<?= isset($student['address']) ? $student['address'] : '' ?>">
                </div>

                <div class="form-group">
                    <label for="group_id">Group</label>
                    <select name="group_id" id="group_id" class="form-control">
                        <option value="">Select</option>
                        <?php while ($group = $groupResult->fetch_assoc()) : ?>
                            <option value="<?= $group['id'] ?>" <?= isset($student['group_id']) && $student['group_id'] == $group['id'] ? 'selected' : '' ?>>
                                <?= $group['name'] ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                    <?= displayErrorText($errors['group_id'] ?? '') ?>
                </div>

                <div class="form-group">
                    <label for="is_approved">Is Approved</label>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="is_approved" id="approved" value="1" <?= isset($student['is_approved']) && $student['is_approved'] == 1 ? 'checked' : '' ?>>
                        <label class="form-check-label" for="approved">
                            Yes
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="is_approved" id="not_approved" value="0" <?= !isset($student['is_approved']) || isset($student['is_approved']) && $student['is_approved'] == 0 ? 'checked' : '' ?>>
                        <label class="form-check-label" for="not_approved">
                            No
                        </label>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary"><?= isset($student) ? 'Update' : 'Save' ?> Student</button>
            </form>
        </div>
    </div>
</div>
<!-- /.container-fluid -->

<?php include $publicPath . '/includes/footer.php' ?>