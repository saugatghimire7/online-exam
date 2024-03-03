<?php

$servername = "localhost";
$username = "online_exam_db_user";
$password = "NewStrongerPassword!11111111111@22";
$dbname = "online_exam_db";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set the charset to utf8mb4
$conn->set_charset("utf8mb4");

session_start();

include 'variables.php';
include 'functions.php';
