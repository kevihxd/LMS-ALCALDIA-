<?php
$host="localhost";
$user="root";
$pass=""; 
$db="lms";

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $conn = new mysqli($host, $user, $pass, $db);

    session_start(); 
} catch (mysqli_sql_exception $e) {
   
    die("Error conectando a la base de datos: " . $e->getMessage());
}
?>