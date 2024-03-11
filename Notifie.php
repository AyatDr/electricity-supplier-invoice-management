<?php
session_start();
$email_admin = $_SESSION['email'];

$password = $_SESSION['password'];
require 'ConnectionDB.php'; 
$id_client = $_GET['id_client'];
$date = $_GET['date'];

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

$stmt = $conn->prepare("SELECT email, nom , prenom ,gender FROM client WHERE id = :id");
$stmt->bindParam(':id', $id_client);

$stmt->execute();

// Récupération du résultat
$result = $stmt->fetch(PDO::FETCH_ASSOC);

// Destinataire
$to = $result['email'];
$nom=$result['nom'];
$prenom = $result['prenom'];
$gender = $result['gender'];
// Sujet de l'e-mail
$subject = "Important Notification";

// Contenu de l'e-mail
if ($gender == 'female') {
    $message = "Hello Mrs. " . $nom . " " . $prenom . ", You have received an important notification regarding your input anomaly regarding your consumption for the month: " . date('F', strtotime($date));
} else {
    $message = "Hello Mr. " . $nom . " " . $prenom . ", You have received an important notification regarding your input anomaly regarding your consumption for the month: " . date('F', strtotime($date));
}


// Création de l'objet PHPMailer
$mail = new PHPMailer(true);

try {
    // Paramètres SMTP
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';  
    $mail->SMTPAuth = true;
    $mail->Username = ''; 
    $mail->Password = ''; 
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; 
    $mail->Port = 587; 

    // Destinataire, sujet, message et en-tête de l'e-mail
    $mail->setFrom($email_admin);
    $mail->addAddress($to);
    $mail->Subject = $subject;
    $mail->Body = $message;

    // Envoi de l'e-mail
    $mail->send();
    
    header("Location: anomalies.php?msg=Mail Send successfully");
} catch (Exception $e) {
    echo "Échec de l'envoi de l'e-mail. Erreur : {$mail->ErrorInfo}";
}
?>
