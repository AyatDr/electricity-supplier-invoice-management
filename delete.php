<?php
require("ConnectionDB.php");

$id = $_GET["id"];

try {
    $stmt = $conn->prepare("DELETE FROM client WHERE id = :id");
    $stmt->bindParam(':id', $id);
    $stmt->execute();

    header("Location: AllClients.php?msg=Data deleted successfully");
} catch(PDOException $e) {
    echo "Failed: " . $e->getMessage();
}

