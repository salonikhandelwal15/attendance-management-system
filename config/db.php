<?php
$host = "localhost";
$user = "root";
$pass = "Asish@2003";     
$db   = "attendance_system";

$conn = new mysqli($host, $user, $pass, $db);

if($conn->connect_error){
    die("Connection failed: " . $conn->connect_error);
}
?>
