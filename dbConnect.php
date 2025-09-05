<?php

$host = "localhost";
// for security purpose username, password is not shown in the code
$username ="";
$password = ""; 
$database = "admin_admission_july_25";

if(basename(__FILE__) == basename($_SERVER['PHP_SELF'])){
    header('HTTP/1.0 403 Forbidden');
    exit('Direct Access is Not Allowed.');
}

try{
    $pdo = new PDO("mysql:host=$host; dbname=$database", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // echo 'connection successfull';
} catch (PDOException $e){
    echo 'Connection Failed: '. $e->getMessage();
}

?>