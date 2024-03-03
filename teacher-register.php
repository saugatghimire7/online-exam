<?php
include("includes/config.php");

// Redirect to the teacher dashboard if already logged in
if (isTeacherLoggedIn()) {
    header("Location: $TEACHER_HOME_URL");
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
    if (empty(trim($data['password']))) {
        $errors['password'] = "Password is required";
    }

    // Password and confirm password check
    if ($data['password'] !== $data['confirm_password']) {
        $errors['confirm_password'] = "Password and confirm password must match.";
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
    $cvName = '';
    if (!empty($_FILES['cv']['name'])) {
        $cvFileName = basename($_FILES['cv']['name']);
        $cvPath = $uploadPath . '/' . $cvFileName;

        if (move_uploaded_file($_FILES['cv']['tmp_name'], $cvPath)) {
            $cvName = $cvFileName;
        } else {
            $errors['cv'] = "Error uploading CV";
        }
    } else {
        $errors['cv'] = "Please upload you CV";
    }

    // Update and create
    if (empty($errors)) {
        // Create a new teacher
        $createSql = "INSERT INTO teachers (name, email, password, phone, address, qualification, cv, created_at, updated_at) 
            VALUES (?, ?, ?, ?, ?, ?, ?, CURRENT_TIMESTAMP, NULL)";
        $createStmt = $conn->prepare($createSql);
        $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);
        $createStmt->bind_param("ssssssi", $data['name'], $data['email'], $hashedPassword, $data['phone'], $data['address'], $data['qualification'], $cvPath);

        if ($createStmt->execute()) {
            // Insert teacher groups
            $teacher_id = $conn->insert_id;
            $insertGroupsSql = "INSERT INTO teacher_groups (teacher_id, group_id) VALUES (?, ?)";
            $insertGroupsStmt = $conn->prepare($insertGroupsSql);
            $insertGroupsStmt->bind_param("ii", $teacher_id, $group_id);

            foreach ($data['group_ids'] as $group_id) {
                $insertGroupsStmt->execute();
            }

            header("Location: {$baseUrl}/teacher-login.php");
            exit();
        } else {
            setErrorMessage("Error registering account: " . $createStmt->error);
        }

        header("Location: {$_SERVER['REQUEST_URI']}");
        exit();
    }
}

// Query to retrieve groups for the select box
$groupQuery = "SELECT id, name FROM `groups`";
$groupResult = $conn->query($groupQuery);

// Close the database connection
$conn->close();
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <title> <?php echo $appName ?>sign page</title>

    <!-- Custom fonts for this template-->
    <link href="<?php echo $baseUrl ?>/assets/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="<?php echo $baseUrl ?>/assets/css/sb-admin-2.min.css" rel="stylesheet">
    <style>
        body {
            margin: 0;
            padding: 0;
            background: linear-gradient(120deg, #2980b9, #8e44ad);
        }

        .center {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 400px;
            background: white;
            border-radius: 10px;
            height: 100vh;
            overflow: auto;
        }

        .center h1 {
            text-align: center;
            padding: 0 0 20px 0;
            border-bottom: 1px solid silver;
        }

        .center form {
            padding: 0 40px;
            box-sizing: border-box;
        }

        form .txt_field {
            position: relative;
            border-bottom: 2px solid #adadad;

        }

        .txt_field input {
            width: 100%;
            padding: 0 5px;
            height: 40px;
            font-size: 16px;
            border: none;
            background: none;
            outline: none;

        }

        .txt_field label {
            position: absolute;
            top: 50%;
            left: 5px;
            color: #adadad;
            transform: translateY(-50%);
            font-size: 16px;
            pointer-events: none;
            transition: .5s;


        }

        .txt_field span::before {
            content: '';
            position: absolute;
            top: 40px;
            left: 0;
            width: 0;
            height: 2px;
            background: #2691d9;
            transition: .5s;

        }

        .txt_field input:focus~label,
        .txt_field input:valid~label {
            top: -50;
            color: #2691d9;


        }

        .txt_field input:focus~span::before,
        .txt_field input:valid~span::before {

            width: 100%;
        }

        .pass {
            margin: -5px 0 20px 5px;
            color: #a6a6a6;
            cursor: pointer;
        }

        .pass:hover {
            text-decoration: underline;

        }

        input[type="submit"] {
            width: 100%;
            height: 50px;
            border: 1px solid;
            background: #2691d9;
            border-radius: 25px;
            font-size: 18px;
            color: #e9f4fb;
            font-weight: 700;
            cursor: pointer;
            outline: none;
            margin-top: 20px;

        }

        input[type="submit"]:hover {
            border-color: #2691d9;
            transition: .5s;
        }

        .signup_link {
            margin: 30px 0;
            text-align: center;
            font-size: 16px;
            color: #666666;


        }

        .signup_link a:hover {
            text-decoration: underline;

        }
    </style>



    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

</head>

<body>
    <div class="center">
        <h1><?= $appName ?> | Sign up For Teacher</h1>

        <?php
        if (isset($error_message)) {
            echo '<p style="color: red;">' . $error_message . '</p>';
        }
        ?>
        <form method="post" enctype="multipart/form-data">
            <div class="txt_field">
                <input type="text" name="name" placeholder="Name" required>
                <span></span>
                <label></label>
            </div>
            <?= displayErrorText($errors['name'] ?? '') ?>

            <div class="txt_field">
                <input type="text" name="address" placeholder="Adress" required>
                <span></span>
                <label></label>
            </div>
            <?= displayErrorText($errors['address'] ?? '') ?>

            <div class="txt_field">
                <input type="number" name="phone" placeholder="Contact No" required>
                <span></span>
                <label></label>
            </div>
            <?= displayErrorText($errors['phone'] ?? '') ?>


            <div class="txt_field">
                <input type="email" name="email" placeholder="Email" required>
                <span></span>
                <label></label>

            </div>
            <?= displayErrorText($errors['email'] ?? '') ?>

            <div class="txt_field">
                <input type="text" name="qualification" placeholder="Qualification" required>
                <span></span>
                <label></label>

            </div>
            <?= displayErrorText($errors['qualification'] ?? '') ?>

            <div class="txt_field">
                <p>Upload your CV</p>
                <!--<input type="none"  placeholder="select your CV" required>-->
                <span></span>
                <label></label>

                <div class="txt_field">
                    <input type="file" name="cv" placeholder="cv" accept="application/pdf" required>
                    <span></span>
                    <label></label>

                </div>
                <?= displayErrorText($errors['cv'] ?? '') ?>

                <p for="group_ids">Groups (Select Multiple)</p>
                <select name="group_ids[]" id="group_ids" class="form-control" multiple>
                    <option value="">Select</option>
                    <?php while ($group = $groupResult->fetch_assoc()) : ?>
                        <option value="<?= $group['id'] ?>" <?= isset($teacherGroups) && in_array($group['id'], $teacherGroups) ? 'selected' : '' ?>>
                            <?= $group['name'] ?>
                        </option>
                    <?php endwhile; ?>
                </select>
                <?= displayErrorText($errors['group_ids'] ?? '') ?>

                <div class="txt_field">
                    <input type="Password" name="password" placeholder="Password" required>
                    <span></span>
                    <label></label>

                </div>
                <?= displayErrorText($errors['password'] ?? '') ?>

                <div class="txt_field">
                    <input type="Password" name="confirm_password" placeholder="Confirm Password" required>
                    <span></span>
                    <label> </label>

                </div>
                <?= displayErrorText($errors['confirm_password'] ?? '') ?>

                <!--<div class="pass">Forgot password? </div>-->
                <input type="submit" value="Sign Up">

                <div class="signup link">
                    Have an Account?<a href="teacher-login.php">Login Here</a>

                </div>


        </form>

    </div>






    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>