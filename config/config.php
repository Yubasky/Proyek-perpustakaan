<?php
// Config for Laragon Environment

$host = 'localhost';
$dbname = 'simbad_db';
$username = 'root';
$password = ''; // Default Laragon password is empty

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    // Set the PDO error mode to exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Base URL configuration - change this if your folder name is different
define('BASE_URL', 'http://localhost/Simbad/');

// Start Session globally
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
