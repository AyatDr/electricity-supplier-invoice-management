<?php
session_start();
$email_admin = $_SESSION['email'];


require 'ConnectionDB.php'; // Assurez-vous de fournir le bon chemin vers le fichier ConnectionDB.php
$id_client = $_GET['id_client'];
$date = $_GET['date'];
$prix_ttc = $_GET['prix_ttc'];
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
$gender = $result['$gender'];
// Sujet de l'e-mail
$subject = "Important Notification";

// Contenu de l'e-mail
if ($gender == 'female') {
    $message = "Dear Mrs. " . $nom . " " . $prenom . ", 

We would like to bring to your attention an important notification regarding your annual anomaly consumption for the year " . $date . ". Upon review, it has come to our attention that there is an outstanding balance of (DH) " . $prix_ttc . ". 

We kindly request your prompt attention to resolve this matter at your earliest convenience. 

Thank you for your cooperation.";

} else {

    $message = "Hello Mr. " . $nom . " " . $prenom . ", 

We would like to bring to your attention an important notification regarding your annual anomaly consumption for the year " . $date . ". Upon review, it has come to our attention that there is an outstanding balance of (DH) " . $prix_ttc . ". 

We kindly request your prompt attention to resolve this matter at your earliest convenience. 

Thank you for your cooperation.";

   
}


// Création de l'objet PHPMailer
$mail = new PHPMailer(true);

try {
    // Paramètres SMTP
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';  // Remplacez-le par le serveur SMTP approprié
    $mail->SMTPAuth = true;
    $mail->Username = ''; // Remplacez-le par votre adresse e-mail
    $mail->Password = ''; // Remplacez-le par votre mot de passe e-mail
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // Activer le cryptage TLS
    $mail->Port = 587; // Le port SMTP à utiliser, généralement 587 pour TLS

    // Destinataire, sujet, message et en-tête de l'e-mail
    $mail->setFrom($email_admin);
    $mail->addAddress($to);
    $mail->Subject = $subject;
    $mail->Body = $message;

    // Envoi de l'e-mail
    $mail->send();
    echo "L'e-mail a été envoyé avec succès à $to";
    header("Location: ConsommationAnnuelle.php?msg=Mail Send successfully");
} catch (Exception $e) {
    echo "Échec de l'envoi de l'e-mail. Erreur : {$mail->ErrorInfo}";
}
?>
