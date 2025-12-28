<?php $host = "localhost";
$dbname = "unity care cli";
$user = "root";
$password = "";

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $user, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Connection reussie!";
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
