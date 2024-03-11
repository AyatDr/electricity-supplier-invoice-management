
<?php 
require("ConnectionDB.php");

// Vérifier si un fichier a été téléchargé
if(isset($_FILES['fileToUpload'])){
    $file = $_FILES['fileToUpload']['tmp_name'];
   
    // Ouvrir le fichier en mode lecture
    $fileHandle = fopen($file, "r");

    // Initialiser une variable pour stocker les informations du client
    $client = array();
    
    while(($line = fgets($fileHandle)) !== false){
        // Supprimer les retours à la ligne
        $line = trim($line);
        
        // Si la ligne contient "###", cela signifie que nous avons terminé de traiter les informations pour un client
        if(strpos($line, "###") !== false){
            // Traitement des informations du client
            // Comparer la consommation annuelle avec la consommation du mois 12
            $consommation_annuelle = $client['consommation'];
            $id_client = $client['id_client'];
            $annee = $client['annee'];
            $date_saisie = $client['date_saisie'];
            
            // Récupérer la consommation du mois 12 de ce client depuis la base de données
            $stmt = $conn->prepare("SELECT quantite FROM consomation WHERE id_client = :id_client AND MONTH(date) = 12 and YEAR(date) = :annee");
            $stmt->bindParam(':id_client', $id_client);
            $stmt->bindParam(':annee', $annee);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Vérifier si une ligne a été récupérée
            if($row){
                // Comparer la consommation annuelle avec la consommation du mois 12
                $consommation_mois_12 = $row['quantite'];
                $difference = $consommation_annuelle - $consommation_mois_12;
                
                 // Calculer le prix HT en fonction de la quantité
             if ($difference >= 0 && $difference  <= 100) {
                $prix_ht = 0.8 * $difference ;
            } elseif ($difference  >= 101 && $difference  <= 200) {
                $prix_ht = 0.9 * $difference ;
            } else {
                $prix_ht = 1 * $difference ;
            }
        
            // Calculer le prix TTC
            $prix_ttc = $prix_ht +  0.14 * $prix_ht;
                
                // Déterminer si la différence dépasse 50
                $difference_flag = ($difference > 50) ? 'Oui' : 'Non';
                $prix_a_inserer = ($difference_flag === 'Oui') ? $prix_ttc : '--';
                
                // Maintenant, vous pouvez stocker les informations du client dans la table consommationannuelle
                // et inclure le flag de différence dans la colonne difference
                
                $stmt_insert = $conn->prepare("INSERT INTO consommationannuelle (id_client, consommation, annee , date_saisie , difference,prix) VALUES (:id_client, :consommation_annuelle, :annee , :date_saisie , :difference_flag,:prix)");
                $stmt_insert->bindParam(':id_client', $id_client);
                $stmt_insert->bindParam(':consommation_annuelle', $consommation_annuelle);
                $stmt_insert->bindParam(':annee', $annee);
                $stmt_insert->bindParam(':date_saisie', $date_saisie);
                $stmt_insert->bindParam(':difference_flag', $difference_flag);
                $stmt_insert->bindParam(':prix', $prix_a_inserer);
                $stmt_insert->execute();
            }
            
            // Réinitialiser le tableau client pour le prochain client
            $client = array();
        } else {
            // Diviser la ligne en clé et valeur en utilisant ":"
            $infoArray = explode(":", $line, 2);
            // Vérifier si l'élément $infoArray contient suffisamment d'éléments
            if(count($infoArray) < 2){
                // Ignorer cette ligne
                continue;
            }

            // Accéder aux éléments du tableau
            $key = trim($infoArray[0]);
            $value = trim($infoArray[1]);

            // Stocker les informations dans le tableau du client
            $client[$key] = $value;
        }
    }
    
    
    // Fermer le fichier
    fclose($fileHandle);
    header("Location: ConsommationAnnuelle.php?msg=File Saved successfully");
   
    exit();
}
?>