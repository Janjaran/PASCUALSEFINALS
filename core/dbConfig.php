<?php 

$host = "localhost";
$user = "root";
$password = "";
$PASCUALFINALS = "PASCUALFINALS";
$dsn = "mysql:host={$host};dbname={$PASCUALFINALS}";

$pdo = new PDO($dsn,$user,$password);
$pdo->exec("SET time_zone = '+08:00';");
date_default_timezone_set('Asia/Manila');


?>