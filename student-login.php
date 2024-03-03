<?php
include("includes/config.php");

// Redirect to the student dashboard if already logged in
if (isStudentLoggedIn()) {
    header("Location: $STUDENT_HOME_URL");
    exit();
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get user input
    $email = $_POST["email"];
    $password = $_POST["password"];

    // Validate input
    if (empty($email) || empty($password)) {
        $error_message = "Please enter both email and password.";
    } else {
        // Check user credentials for students
        $query = "SELECT * FROM students WHERE email = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 1) {
            // Student found, check password
            $student = $result->fetch_assoc();

            if (password_verify($password, $student['password'])) {
                if ($student['is_approved']) {
                    $_SESSION["student_id"] = $student['id'];
                    $_SESSION["student_name"] = $student['name'];

                    // Redirect to the student dashboard
                    header("Location: $baseUrl/student/dashboard.php");
                    exit();
                } else {
                    $error_message = "Your account has not been verified. Please contact Admin.";
                }
            } else {
                $error_message = "Invalid email or password.";
            }
        } else {
            $error_message = "Invalid email or password.";
        }

        $stmt->close();
    }
}
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

    <title><?php echo $appName ?> | Student Login</title>

    <!-- Custom fonts for this template-->
    <link href="<?php echo $baseUrl ?>/assets/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="<?php echo $baseUrl ?>/assets/css/sb-admin-2.min.css" rel="stylesheet">

</head>

<body class="bg-gradient-primary">

    <div class="container">

        <!-- Outer Row -->
        <div class="row justify-content-center">

            <div class="col-md-6">

                <div class="card o-hidden border-0 shadow-lg my-5">
                    <div class="card-body p-0">
                        <div class="p-5">
                            <div class="text-center">
                                <h1 class="h4 text-gray-900 mb-4"><?= $appName ?> | Student Login</h1>
                            </div>

                            <?php
                            if (isset($error_message)) {
                                echo '<p style="color: red;">' . $error_message . '</p>';
                            }
                            ?>

                            <form class="user" method="POST">
                                <div class="form-group">
                                    <input type="email" name="email" class="form-control form-control-user" placeholder="Email">
                                </div>
                                <div class="form-group">
                                    <input type="password" name="password" class="form-control form-control-user" placeholder="Password">
                                </div>
                                <button type="submit" class="btn btn-primary btn-user btn-block">
                                    Login
                                </button>
                                <div class="signup link">
                   Dont Have an Account?<a href="student-register.php">Sign up Here</a>

                </div>
                            
                            </form>

                        </div>
                    </div>
                </div>

            </div>

        </div>

    </div>

    <!-- Bootstrap core JavaScript-->
    <script src="<?php echo $baseUrl ?>/assets/vendor/jquery/jquery.min.js"></script>
    <script src="<?php echo $baseUrl ?>/assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Core plugin JavaScript-->
    <script src="<?php echo $baseUrl ?>/assets/vendor/jquery-easing/jquery.easing.min.js"></script>

    <!-- Custom scripts for all pages-->
    <script src="<?php echo $baseUrl ?>/assets/js/sb-admin-2.min.js"></script>

</body>

</html>