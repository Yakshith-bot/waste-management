<?php
session_start();

$host = 'localhost';
$dbname = 'your_database';
$username = 'your_user';
$password = 'your_password';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

define('BASE_URL', '/your-project-folder/');
?>