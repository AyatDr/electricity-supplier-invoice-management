<?php
// Inclure le fichier de connexion à la base de données
require_once 'ConnectionDB.php';



// Assurez-vous que le script est appelé avec un identifiant de facture valide
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    // Récupérez l'identifiant de la facture depuis l'URL
    $id_facture = $_GET['id'];
    
    // Requête pour récupérer le contenu du fichier PDF depuis la base de données
    $sql = "SELECT pdf FROM facture WHERE id_facture = :id_facture";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id_facture', $id_facture, PDO::PARAM_INT);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($row && isset($row['pdf'])) {
        // Entêtes HTTP pour indiquer que le contenu est un fichier PDF à télécharger
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="facture.pdf"');
        
        // Envoyez le contenu du fichier PDF au navigateur
        echo $row['pdf'];
        
        exit;
    } else {
        // Si l'identifiant de la facture est introuvable ou si le champ PDF est vide, affichez un message d'erreur
        echo "Fichier PDF introuvable.";
    }
} else {
    // Si l'identifiant de la facture est invalide, redirigez vers une page d'erreur ou affichez un message approprié
    echo "Identifiant de facture invalide.";
}
?>