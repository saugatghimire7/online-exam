<?php

function setSuccessMessage($message)
{
    $_SESSION['success_message'] = $message;
}

function setErrorMessage($message)
{
    $_SESSION['error_message'] = $message;
}

function displayErrorText($error)
{
    if (!empty($error)) {
        return  "<p style='color: red;'>{$error}</p>";
    }
}

function sanitizeInput($data)
{
    if (is_array($data)) {
        foreach ($data as $key => $value) {
            $data[$key] = sanitizeInput($value);
        }
        return $data;
    } else {
        return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    }
}

function getDBErrorMessage($errorCode)
{
    switch ($errorCode) {
        case '1451':
            return 'Cannot delete group. Related data exists.';

        default:
            return 'Failed to delete group';
    }
}

function getUserProfileName($userType)
{
    switch ($userType) {
        case 'admin':
            return $_SESSION['admin_name'];
        case 'teacher':
            return $_SESSION['teacher_name'];
        case 'student':
            return $_SESSION['student_name'];
        default:
            throw new Error('Invalid User Type');
    }
}

function isAdminLoggedIn()
{
    return isset($_SESSION['admin_id']);
}

function isTeacherLoggedIn()
{
    return isset($_SESSION['teacher_id']);
}

function isStudentLoggedIn()
{
    return isset($_SESSION['student_id']);
}

function shuffleQuestionIds($array)
{
    $count = count($array);
    for ($i = $count - 1; $i > 0; $i--) {
        $j = mt_rand(0, $i);
        [$array[$i], $array[$j]] = [$array[$j], $array[$i]];
    }

    return $array;
}
