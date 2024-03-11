<?php 
require("ConnectionDB.php");

// Vérifiez si l'ID de la réclamation est défini dans l'URL

if (isset($_GET['id'])) {
    // Récupérez l'ID de la réclamation
    $id_reclamation = $_GET['id'];
   

    try {

        // Mettez à jour l'état de la réclamation à "traite" dans la base de données
        $sql = "UPDATE reclamation SET etat = 'traite' WHERE id_rec = :id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':id', $id_reclamation);
        $stmt->execute();
        
       
       
        header("Location: ReclamationAdmin.php?msg=Claim processed successfully");
    } catch(PDOException $e) {
        echo "Erreur de connexion à la base de données : " . $e->getMessage();
    }

   
} else {
    echo "ID de réclamation manquant.";
}
?>
