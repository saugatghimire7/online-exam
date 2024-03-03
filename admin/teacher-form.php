<?php $userType = 'admin' ?>
<?php
require_once "../includes/config.php";

// Redirect to login if not logged in
if (!isAdminLoggedIn()) {
    header("Location: $ADMIN_LOGIN_URL");
    exit();
}

if (isset($_GET["teacher_id"])) {
    $teacher_id = $_GET["teacher_id"];

    // Query to retrieve teacher
    $sql = "SELECT * FROM teachers WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $teacher_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        // Teacher does not exist
        setErrorMessage('Teacher does not exist!');
        header("Location: $baseUrl/admin/teachers.php");
        exit();
    }

    $teacher = $result->fetch_assoc();

    // Query to retrieve teacher groups
    $teacherGroupsSql = "SELECT group_id FROM teacher_groups WHERE teacher_id = ?";
    $teacherGroupsStmt = $conn->prepare($teacherGroupsSql);
    $teacherGroupsStmt->bind_param("i", $teacher_id);
    $teacherGroupsStmt->execute();
    $teacherGroupsResult = $teacherGroupsStmt->get_result();
    $teacherGroups = [];
    while ($row = $teacherGroupsResult->fetch_assoc()) {
        $teacherGroups[] = $row['group_id'];
    }
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
    if (!isset($_GET["teacher_id"]) && empty(trim($data['password']))) {
        $errors['password'] = "Password is required";
    }

    if (empty(trim($data['phone']))) {
        $errors['phone'] = "Phone number is required";
    }

    if (empty(trim($data['address']))) {
        $errors['address'] = "Address is required";
    }

    if (empty(trim($data['qualification']))) {
        $errors['qualification'] = "Qualification is required";
    }

    if (empty($data['group_ids']) || !is_array($data['group_ids'])) {
        $errors['group_ids'] = "At least one group is required";
    }

    // File upload handling
    $cvName = isset($teacher) ? $teacher['cv'] : '';
    if (!empty($_FILES['cv']['name'])) {
        $cvFileName = basename($_FILES['cv']['name']);
        $cvPath = $uploadPath . '/' . $cvFileName;

        if (move_uploaded_file($_FILES['cv']['tmp_name'], $cvPath)) {
            $cvName = $cvFileName;
        } else {
            $errors['cv'] = "Error uploading CV";
        }
    }

    // Update and create
    if (empty($errors)) {
        if (isset($_GET["teacher_id"])) {
            // Update existing teacher
            $updateSql = "UPDATE teachers SET 
                      name = ?, 
                      email = ?, 
                      " . (!empty($data['password']) ? "password = ?, " : "") .
                "phone = ?, 
                      address = ?, 
                      qualification = ?, 
                      cv = ?, 
                      is_approved = ?,
                      updated_at = CURRENT_TIMESTAMP 
                      WHERE id = ?";
            $updateStmt = $conn->prepare($updateSql);

            // Hash the password if provided
            if (!empty($data['password'])) {
                $hashedPassword = password_hash($data['password'], PASSWORD_BCRYPT);
                // Bind password parameter
                $updateStmt->bind_param(
                    "sssssssii",
                    $data['name'],
                    $data['email'],
                    $hashedPassword,
                    $data['phone'],
                    $data['address'],
                    $data['qualification'],
                    $cvName,
                    $data['is_approved'],
                    $_GET["teacher_id"]
                );
            } else {
                // If password is not provided, exclude it from binding
                $updateStmt->bind_param(
                    "ssssssii",
                    $data['name'],
                    $data['email'],
                    $data['phone'],
                    $data['address'],
                    $data['qualification'],
                    $cvName,
                    $data['is_approved'],
                    $_GET["teacher_id"]
                );
            }

            if ($updateStmt->execute()) {
                // Update teacher groups
                $teacher_id = $_GET["teacher_id"];
                $deleteGroupsSql = "DELETE FROM teacher_groups WHERE teacher_id = ?";
                $deleteGroupsStmt = $conn->prepare($deleteGroupsSql);
                $deleteGroupsStmt->bind_param("i", $teacher_id);
                $deleteGroupsStmt->execute();

                $insertGroupsSql = "INSERT INTO teacher_groups (teacher_id, group_id) VALUES (?, ?)";
                $insertGroupsStmt = $conn->prepare($insertGroupsSql);
                $insertGroupsStmt->bind_param("ii", $teacher_id, $group_id);

                foreach ($data['group_ids'] as $group_id) {
                    $insertGroupsStmt->execute();
                }

                setSuccessMessage("Teacher updated successfully!");
                header("Location: {$baseUrl}/admin/teachers.php");
                exit();
            } else {
                setErrorMessage("Error updating teacher: " . $updateStmt->error);
            }
        } else {
            // Create a new teacher
            $createSql = "INSERT INTO teachers (name, email, password, phone, address, qualification, cv, is_approved, created_at, updated_at) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, CURRENT_TIMESTAMP, NULL)";
            $createStmt = $conn->prepare($createSql);
            $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);
            $createStmt->bind_param("ssssssii", $data['name'], $data['email'], $hashedPassword, $data['phone'], $data['address'], $data['qualification'], $cvPath, $data['is_approved']);

            if ($createStmt->execute()) {
                // Insert teacher groups
                $teacher_id = $conn->insert_id;
                $insertGroupsSql = "INSERT INTO teacher_groups (teacher_id, group_id) VALUES (?, ?)";
                $insertGroupsStmt = $conn->prepare($insertGroupsSql);
                $insertGroupsStmt->bind_param("ii", $teacher_id, $group_id);

                foreach ($data['group_ids'] as $group_id) {
                    $insertGroupsStmt->execute();
                }

                setSuccessMessage("Teacher created successfully!");
                header("Location: {$baseUrl}/admin/teachers.php");
                exit();
            } else {
                setErrorMessage("Error creating teacher: " . $createStmt->error);
            }
        }

        header("Location: {$_SERVER['REQUEST_URI']}");
        exit();
    }
}

// Query to retrieve teachers for the select box
$groupQuery = "SELECT id, name FROM `groups`";
$groupResult = $conn->query($groupQuery);

// Close the database connection
$conn->close();
?>

<?php include $publicPath . '/includes/header.php' ?>

<!-- Begin Page Content -->
<div class="container-fluid">

    <!-- Page Heading -->
    <h1 class="h3 mb-4 text-gray-800"><?= isset($teacher) ? 'Edit' : 'Add' ?> Teacher</h1>

    <div class="card shadow mb-4">
        <div class="card-body">
            <?php include "$publicPath/includes/message.php" ?>

            <form action="" method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="name">Name</label>
                    <input type="text" name="name" id="name" class="form-control" value="<?= isset($teacher['name']) ? $teacher['name'] : '' ?>">
                    <?= displayErrorText($errors['name'] ?? '') ?>
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" name="email" id="email" class="form-control" value="<?= isset($teacher['email']) ? $teacher['email'] : '' ?>">
                    <?= displayErrorText($errors['email'] ?? '') ?>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" name="password" id="password" class="form-control">
                    <?= displayErrorText($errors['password'] ?? '') ?>
                </div>

                <div class="form-group">
                    <label for="phone">Phone</label>
                    <input type="text" name="phone" id="phone" class="form-control" value="<?= isset($teacher['phone']) ? $teacher['phone'] : '' ?>">
                    <?= displayErrorText($errors['phone'] ?? '') ?>
                </div>

                <div class="form-group">
                    <label for="address">Address</label>
                    <input type="text" name="address" id="address" class="form-control" value="<?= isset($teacher['address']) ? $teacher['address'] : '' ?>">
                    <?= displayErrorText($errors['address'] ?? '') ?>
                </div>

                <div class="form-group">
                    <label for="qualification">Qualification</label>
                    <input type="text" name="qualification" id="qualification" class="form-control" value="<?= isset($teacher['qualification']) ? $teacher['qualification'] : '' ?>">
                    <?= displayErrorText($errors['qualification'] ?? '') ?>
                </div>

                <div class="form-group">
                    <label for="cv">CV</label>
                    <input type="file" name="cv" id="cv" class="form-control">
                    <?= displayErrorText($errors['cv'] ?? '') ?>
                </div>

                <div class="form-group">
                    <label for="group_ids">Groups (Select Multiple)</label>
                    <select name="group_ids[]" id="group_ids" class="form-control" multiple>
                        <?php while ($group = $groupResult->fetch_assoc()) : ?>
                            <option value="<?= $group['id'] ?>" <?= isset($teacherGroups) && in_array($group['id'], $teacherGroups) ? 'selected' : '' ?>>
                                <?= $group['name'] ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                    <?= displayErrorText($errors['group_ids'] ?? '') ?>
                </div>

                <div class="form-group">
                    <label for="is_approved">Is Approved</label>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="is_approved" id="approved" value="1" <?= isset($teacher['is_approved']) && $teacher['is_approved'] == 1 ? 'checked' : '' ?>>
                        <label class="form-check-label" for="approved">
                            Yes
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="is_approved" id="not_approved" value="0" <?= !isset($teacher['is_approved']) || isset($teacher['is_approved']) && $teacher['is_approved'] == 0 ? 'checked' : '' ?>>
                        <label class="form-check-label" for="not_approved">
                            No
                        </label>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary"><?= isset($teacher) ? 'Update' : 'Save' ?> Teacher</button>
            </form>
        </div>
    </div>
</div>
<!-- /.container-fluid -->

<?php include $publicPath . '/includes/footer.php' ?>