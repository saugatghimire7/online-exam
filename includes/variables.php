<?php

$appName = 'Online Exam';
$projectDirectory = 'online-exam';

$publicPath = $_SERVER['DOCUMENT_ROOT'];
$baseUrl = 'http://localhost:8080';

$uploadPath = $publicPath . '/uploads';

$ADMIN_LOGIN_URL = "{$baseUrl}/admin-login.php";
$ADMIN_HOME_URL = "{$baseUrl}/admin/dashboard.php";

$TEACHER_LOGIN_URL = "{$baseUrl}/teacher-login.php";
$TEACHER_HOME_URL = "{$baseUrl}/teacher/dashboard.php";

$STUDENT_LOGIN_URL = "{$baseUrl}/student-login.php";
$STUDENT_HOME_URL = "{$baseUrl}/student/dashboard.php";
