<?php 

// Connect to Database 
$database = 'washutimes';
$host = 'localhost';
$user = 'saulet';
$password = 'saulet221';

$conn = new mysqli($host, $user, $password, $database);

if($conn->connect_error){
    die("Could not connect to database" . $conn->connect_error);
}

?>