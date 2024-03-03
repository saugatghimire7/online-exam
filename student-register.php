<?php
include("includes/config.php");

// Redirect to the teacher dashboard if already logged in
if (isStudentLoggedIn()) {
    header("Location: $STUDENT_HOME_URL");
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

    if (empty(trim($data['group_id']))) {
        $errors['group_id'] = "Group is required";
    }

    // Update and create
    if (empty($errors)) {
        // Create a new student
        $createSql = "INSERT INTO students (name, email, password, phone, address, group_id, created_at, updated_at) 
        VALUES (?, ?, ?, ?, ?, ?, CURRENT_TIMESTAMP, NULL)";
        $createStmt = $conn->prepare($createSql);
        $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);
        $createStmt->bind_param("sssssi", $data['name'], $data['email'], $hashedPassword, $data['phone'], $data['address'], $data['group_id']);

        if ($createStmt->execute()) {
            header("Location: {$baseUrl}/student-login.php");
            exit();
        } else {
            setErrorMessage("Error registering account: " . $createStmt->error);
        }

        header("Location: {$_SERVER['REQUEST_URI']}");
        exit();
    }
}

// Query to retrieve groups for the select box
$groupQuery = "SELECT id, name FROM groups";
$groupResult = $conn->query($groupQuery);

// Close the database connection
$conn->close();
?>


<!DOCTYPE html>
<html>

<head>
    <title>sign page</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            background: linear-gradient(120deg, #2980b9, #8e44ad);
            height: 100vh;
            overflow: hidden;
        }

        .center {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 400px;
            background: white;
            border-radius: 10px;
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
        <h1>Sign up For student</h1>
        <form method="post">
            <div class="txt_field">
                <input type="text" name="name" placeholder="Name" required>
                <span></span>
                <label></label>
            </div>
            <?= displayErrorText($errors['name'] ?? '') ?>

            <div class="txt_field">
                <input type="text" name="address" placeholder="Adress">
                <span></span>
                <label></label>
            </div>

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
                <input type="Password" name="password" placeholder="Password" required>
                <span></span>
                <label></label>
            </div>
            <?= displayErrorText($errors['password'] ?? '') ?>

            <div class="txt_field">
                <input type="Password" name="confirm_password" placeholder="confirm password" required>
                <span></span>
                <label> </label>
            </div>
            <?= displayErrorText($errors['confirm_password'] ?? '') ?>


            <label for="sel1" class="form-label">Select course</label>
            <select class="form-select" id="sel1" name="group_id">
                <option value="">Select</option>
                <?php while ($group = $groupResult->fetch_assoc()) : ?>
                    <option value="<?= $group['id'] ?>" <?= isset($student['group_id']) && $student['group_id'] == $group['id'] ? 'selected' : '' ?>>
                        <?= $group['name'] ?>
                    </option>
                <?php endwhile; ?>
            </select>
            <?= displayErrorText($errors['group_id'] ?? '') ?>

            <br>
            <button type="submit" class="btn btn-primary mt-3">Sign Up</button>

            <div class="signup link" style="padding:20px;">
                Have an Account?<a href="student-login.php">Login Here</a>
            </div>
        </form>

    </div>






    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>