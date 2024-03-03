<?php
require_once 'includes/variables.php';

session_start();

$userType = $_GET['userType'];
$redirectUrl = '';

switch ($userType) {
    case 'admin':
        unset($_SESSION['admin_id']);
        unset($_SESSION['admin_name']);
        $redirectUrl = $ADMIN_LOGIN_URL;
        break;
    case 'teacher':
        unset($_SESSION['teacher_id']);
        unset($_SESSION['teacher_name']);
        $redirectUrl = $TEACHER_LOGIN_URL;
        break;
    case 'student':
        unset($_SESSION['student_id']);
        unset($_SESSION['student_name']);
        $redirectUrl = $STUDENT_LOGIN_URL;
        break;
    default:
        throw new Error('Invalid User Type');
}

if (empty($_SESSION)) {
    session_destroy();
}

header("Location: $redirectUrl");
exit();
