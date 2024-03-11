<?php

$servername = "localhost";
$username = "root";
$password = "";

	try {
    $conn = new PDO("mysql:host=$servername;dbname=Electricite", $username, $password);
    
   
    }
catch(PDOException $e)
    {
    echo "Connection failed: " . $e->getMessage();
    }
