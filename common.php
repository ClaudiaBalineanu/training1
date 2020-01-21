<?php
require_once 'config.php';

try {
    // connect to the server
    $conn = new PDO("mysql:host=" . SERVERNAME . ";dbname=" . DBNAME, USERNAME, PASSWORD);
    // set the PDO error mode to exceptions
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}

session_start();

function trans($label)
{
    return $label;
}

function redirect($page)
{
    header("Location: $page");
    exit();
}