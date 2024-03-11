<?php
require('ConnectionDB.php');
require('fpdf/fpdf.php');
session_start();
$id = $_SESSION['id'];
$prenom = $_SESSION['prenom'];

if (isset($_POST["submit"])) {
    $date = $_POST['date'];
    $quantite = $_POST['quantite'];    
    if (isset($_FILES['compteur']) && $_FILES['compteur']['error'] === UPLOAD_ERR_OK) {
        // Lire le contenu du fichier téléchargé
        $compteurContent = file_get_contents($_FILES['compteur']['tmp_name']);
    } else {
        // Gérer l'erreur si aucun fichier n'a été téléchargé
        exit();
    }
    
       // Extraire l'année et le mois de la date
    $annee = date('Y', strtotime($date));
    $mois = date('m', strtotime($date));
    
    $query = "SELECT * FROM consomation WHERE YEAR(date) = :annee AND MONTH(date) = :mois  AND id_client = :id_client";

    $stmt = $conn->prepare($query);
    $stmt->bindParam(':annee', $annee);
    $stmt->bindParam(':mois', $mois);
    $stmt->bindParam(':id_client', $id);
    $stmt->execute();
    $result = $stmt->fetch();
    
    if ($result) {
            
        header("Location: consomation.php?msg=Un enregistrement pour cette année et ce mois existe déjà");
        
    } else {


    // Récupération de la quantité du mois précédent depuis la base de données
    $previousMonth = date('Y-m', strtotime('-1 month', strtotime($date)));
    $sql_previous_quantity = "SELECT quantite FROM consomation WHERE id_client = :id AND DATE_FORMAT(date, '%Y-%m') = :previousMonth";
    $previous_quantity = 0 ;
    $stmt_previous_quantity = $conn->prepare($sql_previous_quantity);
    $stmt_previous_quantity->bindParam(':id', $id);
    $stmt_previous_quantity->bindParam(':previousMonth', $previousMonth);
    $stmt_previous_quantity->execute();
    $previous_quantity_row = $stmt_previous_quantity->fetch(PDO::FETCH_ASSOC);
    if ($previous_quantity_row !== false) {
        $previous_quantity = $previous_quantity_row['quantite'];
    }
  

    $diff=$quantite-$previous_quantity;
    
    
    // Vérification des conditions
    if ($quantite < $previous_quantity ) {
        $etat = 'en attente';
        

    } else {
        $etat = 'valide'; // ou tout autre état approprié
    }
    
    // Insertion des données dans la table consommation après vérification
    try {
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        $sql = "INSERT INTO consomation (id_client, quantite, date, compteur, etat) 
                VALUES (:id_client, :quantite, :date, :compteur, :etat)";
        
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':quantite', $quantite);
        $stmt->bindParam(':date', $date);
        $stmt->bindParam(':id_client', $id);
        $stmt->bindParam(':compteur', $compteurContent, PDO::PARAM_LOB);
        $stmt->bindParam(':etat', $etat);
        $stmt->execute();

        // Si l'état est valide, insérer les données dans la table facture
        if ($etat === 'valide') {

            // Récupérer l'ID de la dernière consommation insérée

            $sql = "SELECT MAX(id_c) AS max_id FROM consomation";
            $stmt = $conn->prepare($sql);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $consomation_id = $row['max_id'];

             // Calculer le prix HT en fonction de la quantité
             if ($diff >= 0 && $diff <= 100) {
                $prix_ht = 0.8 * $diff;
            } elseif ($diff >= 101 && $diff <= 200) {
                $prix_ht = 0.9 * $diff;
            } else {
                $prix_ht = 1 * $diff;
            }
        
           
        
            // Calculer le prix TTC
            $prix_ttc = $prix_ht + 0.14 * $prix_ht;

            $etat = "non paye" ; 
            // Insérer les données dans la table facture
            $stmt_facture = $conn->prepare("INSERT INTO facture (id_client, prix_HT, prix_TTC, id_consomation, pdf , etat) 
            VALUES (:id_client, :prix_ht, :prix_ttc, :id_consomation,  :pdf , :etat)");
           $stmt_facture->bindParam(':id_client', $id);
           $stmt_facture->bindParam(':prix_ht', $prix_ht);
           $stmt_facture->bindParam(':prix_ttc', $prix_ttc);
           $stmt_facture->bindParam(':etat', $etat);
           $stmt_facture->bindParam(':id_consomation', $consomation_id);
           $stmt_facture->bindParam(':pdf', $pdf_content, PDO::PARAM_LOB);
           $stmt_facture->execute();
            
            // Générer le PDF
 $pdf = new FPDF('P', 'mm', 'A4'); // 210x297 => 210 - 20 = 190

$pdf->AddPage();


//--------------recuperation des donnes------//
$sql = "SELECT 
client.nom, client.prenom, client.prefix, client.Tel, client.email, client.adresse, YEAR(consomation.date) as annee,MONTH(consomation.date) as mois,
consomation.quantite, consomation.compteur,
facture.prix_HT, facture.prix_TTC, facture.id_facture , facture.etat
FROM 
client
INNER JOIN 
consomation ON client.id = consomation.id_client
INNER JOIN 
facture ON consomation.id_c = facture.id_consomation
WHERE 
client.id = :id and consomation.id_c =  :consomation_id";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':id', $id);
$stmt->bindParam(':consomation_id', $consomation_id);
$stmt->execute();
$result = $stmt->fetch(PDO::FETCH_ASSOC);
$date = date('Y-m-d');
$annee = $result['annee'];
$mois = $result['mois'];
$etat = $result['etat'];


 // 1ère Ligne
 $pdf->SetFont('Arial', 'B', 12);
 $pdf->Cell(35,8, utf8_decode('FACTURE N° :'), 0 , 0);
 $pdf->SetFont('Arial', 'B', 12);
 $pdf->SetTextColor(255,0,0);
 $pdf->Cell(40,8, utf8_decode($result['id_facture']), 0 ,0);  // Fin de la ligne
 $pdf->Ln(3);


 $pdf->Cell(20, 8, '', 0, 0);

// Récupérez les données de l'image de la base de données
$imageData = $result['compteur'];

// Chemin vers le dossier temporaire pour stocker l'image
$uploadDir = 'uploads/';

// Assurez-vous que le dossier existe, sinon créez-le
if (!file_exists($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

// Générez un nom de fichier unique pour l'image
$imageFileName = uniqid('image_');

// Examinez les premiers octets des données pour déterminer le type d'image
if (strpos($imageData, "\xFF\xD8") === 0) {
    // Les premiers octets indiquent une image JPEG
    $imageType = IMAGETYPE_JPEG;
    $imageFileName .= '.jpg';
} elseif (strpos($imageData, "\x89\x50\x4E\x47\x0D\x0A\x1A\x0A") === 0) {
    // Les premiers octets indiquent une image PNG
    $imageType = IMAGETYPE_PNG;
    $imageFileName .= '.png';
} elseif (strpos($imageData, "GIF") === 0) {
    // Les premiers octets indiquent une image GIF
    $imageType = IMAGETYPE_GIF;
    $imageFileName .= '.gif';
} else {
    // Si le type d'image ne peut pas être déterminé, utilisez un type par défaut
    $imageType = IMAGETYPE_JPEG;
    $imageFileName .= '.jpg';
}

// Chemin complet vers le fichier image
$imagePath = $uploadDir . $imageFileName;

// Écrivez les données de l'image dans le fichier
file_put_contents($imagePath, $imageData);


switch ($imageType) {
    case IMAGETYPE_JPEG:
        $pdf->Image($imagePath,$pdf->GetX() + 100, $pdf->GetY() - 8, 55, 30);
        break;
   
    case IMAGETYPE_PNG:
        $pdf->Image($imagePath, $pdf->GetX() + 100, $pdf->GetY() - 8, 55, 30);
        break;
    case IMAGETYPE_GIF:
        $pdf->Image($imagePath,$pdf->GetX() + 100, $pdf->GetY() - 8, 55, 30);
        break;
    
}

// Supprimez le fichier image après utilisation
unlink($imagePath);


$pdf->Ln(5);















  // Début de la 3ème ligne
  $pdf->SetFont('Arial', 'B', 10); // Changement de la police et de la taille
  $pdf->SetTextColor(0,0,0);
  $pdf->Cell(60,6, utf8_decode('Tétouan le  '.$date), 0 , 1);
  $pdf->SetFont('Arial', '', 12); // Revenir à la police normale
  $pdf->SetTextColor(0); // Revenir à la couleur par défaut (noir)
  $pdf->Cell(80, 6, '', 0, 0); // Cellule vide
  $pdf->Cell(60, 6, '', 0, 1); // Fin de la ligne
  // Début de la 4ème ligne
  $pdf->SetFont('Arial', 'B', 10); // Changement de la police et de la taille
  $pdf->SetTextColor(0, 0, 0); // Changement de la couleur (orange)
  $pdf->Cell(50, 6, utf8_decode('93000, Tétouan'), 0, 0); // Cellule avec nouvelle configuration
  $pdf->SetFont('Arial', '', 12); // Revenir à la police normale
  $pdf->SetTextColor(0); // Revenir à la couleur par défaut (noir)
  $pdf->Cell(80, 6, '', 0, 0); // Cellule vide
  $pdf->Cell(60, 6, '', 0, 1); // Fin de la ligne
  $pdf->Ln(7);

 // 2ème ligne 
 $pdf->SetTextColor(255,255,255);
 $pdf->SetFillColor(3, 46, 62);
 $pdf->SetFont('Arial', 'B', 14);
 $pdf->Cell(190, 6, 'Electricity : ',0, 1,'C', true );
 

 

 
 // Début de la 5ème ligne
 $pdf->SetFont('Arial', 'B', 10); // Changement de la police et de la taille
 $pdf->SetTextColor(0, 0, 0); // Changement de la couleur (orange)
 $pdf->Cell(50, 6, utf8_decode('Téléphone :'), 0, 0); // Cellule avec nouvelle configuration
 $pdf->SetFont('Arial', '', 12); // Revenir à la police normale
 $pdf->SetTextColor(0); // Revenir à la couleur par défaut (noir)
 $pdf->Cell(80, 6, utf8_decode('+212 675 680 208'), 0, 0); // Cellule avec nouvelle configuration
 $pdf->Cell(60, 6, '', 0, 1); // Fin de la ligne
 
 // Début de la 6ème ligne
 $pdf->SetFont('Arial', 'B', 10); // Changement de la police et de la taille
 $pdf->SetTextColor(0, 0, 0); // Changement de la couleur (orange)
 $pdf->Cell(50, 6, 'Adresse Email :', 0, 0); // Cellule avec nouvelle configuration
 $pdf->SetFont('Arial', '', 12); // Revenir à la police normale
 $pdf->SetTextColor(0); // Revenir à la couleur par défaut (noir)
 $pdf->Cell(80, 6, utf8_decode('electricity@gmail.com'), 0, 0); // Cellule avec nouvelle configuration
 $pdf->Cell(60, 6, '', 0, 1); // Fin de la ligne
 $pdf->Ln(10); // Espacement
 // Début de la 7ème ligne
 $pdf->SetTextColor(255,255,255);
 $pdf->SetFillColor(3, 46, 62);
 $pdf->SetFont('Arial', 'B', 12);
 $pdf->Cell(190, 6, 'CLIENT(E) : ',0, 1,'C', true );
 
 // Début des informations du client
 $pdf->SetFont('Arial', 'B', 10); // Changement de la police et de la taille
 $pdf->SetTextColor(0, 0, 0); // Changement de la couleur (orange)
 $pdf->Cell(50,6,'Nom :', 0 , 0);
 $pdf->SetFont('Arial', '', 12); // Revenir à la police normale
 $pdf->SetTextColor(0); // Revenir à la couleur par défaut (noir)
 $pdf->Cell(65,6,utf8_decode($result['nom']), 0 , 1);
 $pdf->Cell(60, 6, '', 0, 1); // Fin de la ligne
 
 
 $pdf->SetFont('Arial', 'B', 12); // Changement de la police et de la taille
 $pdf->SetTextColor(0, 0, 0); // Changement de la couleur (orange)
 $pdf->Cell(50,6,utf8_decode('Prénom :'), 0 , 0);
 $pdf->SetFont('Arial', '', 12); // Revenir à la police normale
 $pdf->SetTextColor(0); // Revenir à la couleur par défaut (noir)
 $pdf->Cell(65,6,utf8_decode($result['prenom']), 0 , 1);
 $pdf->Cell(60, 6, '', 0, 1); // Fin de la ligne
 
 
 $pdf->SetFont('Arial', 'B', 12); // Changement de la police et de la taille
 $pdf->SetTextColor(0, 0, 0);
 $pdf->Cell(50,6,utf8_decode('Adresse :'), 0 , 0);
 $pdf->SetFont('Arial', '', 12); // Revenir à la police normale
 $pdf->SetTextColor(0); // Revenir à la couleur par défaut (noir)
 $pdf->Cell(65,6,utf8_decode($result['adresse']), 0 , 1);
 $pdf->Cell(60, 6, '', 0, 1); // Fin de la ligne
 
 
 $pdf->SetFont('Arial', 'B', 12); // Changement de la police et de la taille
 $pdf->SetTextColor(0, 0, 0); // Changement de la couleur (orange)
 $pdf->Cell(50,6,utf8_decode('Email :'), 0 , 0);
 $pdf->SetFont('Arial', '', 12); // Revenir à la police normale
 $pdf->SetTextColor(0); // Revenir à la couleur par défaut (noir)
 $pdf->Cell(65,6,utf8_decode($result['email']), 0 , 1);
 $pdf->Cell(60, 6, '', 0, 1); // Fin de la ligne
 
 
 // Partie du tableau des inforamtions de payement
 $pdf->SetFillColor(3, 46, 62);
 $pdf->SetDrawColor(3, 46, 62);
 $pdf->SetFont('Arial', 'B', 14);
 $pdf->SetTextColor(255, 255, 255);
 $pdf->Cell(20, 11, utf8_decode('QTE'),1, 0,'C', true );
 $pdf->SetTextColor(255, 255, 255);
 $pdf->Cell(20, 11, utf8_decode('UNITE'),1, 0,'C', true );
 $pdf->SetTextColor(255, 255, 255);
 $pdf->Cell(35, 11, utf8_decode('Prix UNIT.HTC'),1, 0,'C', true );
 $pdf->SetTextColor(255, 255, 255);
 $pdf->Cell(20, 11, utf8_decode('TVA%'),1, 0,'C', true );
 $pdf->SetTextColor(255, 255, 255);
 $pdf->Cell(30, 11, utf8_decode('Prix HT'),1, 0,'C', true );
 $pdf->SetTextColor(255, 255, 255);
 $pdf->Cell(30, 11, utf8_decode('Prix TTC'),1, 0,'C', true );
 $pdf->SetTextColor(255, 255, 255);
 $pdf->Cell(35, 11, utf8_decode('Mois'),1, 1,'C', true ); // Fin de la ligne
 
 // Les dnnées dans le tableau
 
 $pdf->SetFillColor(255,255,255);
 $pdf->SetTextColor(0,0,0);
 $pdf->SetFont('Arial', '', 9);
 $pdf->Cell(20, 60, utf8_decode($diff),1, 0,'C', true );
 $pdf->Cell(20, 60, utf8_decode('KWH'),1, 0,'C', true );
 $pdf->SetTextColor(0, 0, 0);

if ($diff >= 0 && $diff <= 100) {
    $cts = 0.8;
} elseif ($diff >= 101 && $diff <= 200) {
    $cts = 0.9 ;
} else {
    $cts = 1 ;
}




$pdf->SetTextColor(0,0,0);
$pdf->Cell(35, 60, utf8_decode( $cts.'DH'),1, 0,'C', true );
$pdf->Cell(20, 60, utf8_decode('14%'),1, 0,'C', true );
$pdf->Cell(30, 60, utf8_decode($result['prix_HT']),1, 0,'C', true );
$pdf->Cell(30, 60, utf8_decode($result['prix_TTC']),1, 0,'C', true );
$pdf->Cell(35, 60, utf8_decode($annee.'/'.$mois),1, 1,'C', true );

// Prix Total à payer 
$pdf->SetFont('Arial', 'B', 11);
$pdf->SetTextColor(255,255,255);
$pdf->Cell(125, 11,'',0, 0,'C', true );
$pdf->SetFillColor(3, 46, 62);
$pdf->Cell(30, 11,utf8_decode('Total : '),1, 0,'C', true );
$pdf->SetTextColor(255,0,0);
$pdf->SetFillColor(255,255,255);
$pdf->Cell(35, 11,$result['prix_TTC'],1, 1,'C', true );
$pdf->Ln(4);


// Ajouter une ligne vide pour créer de l'espace
$pdf->Ln(7);






$pdf->Ln(20);

// Texte de remeciement  
$pdf->SetTextColor(255,255,255);
$pdf->SetFont('Arial', '', 11);
$pdf->Cell(190, 9,'Electricity vous remercie !',0, 0,'C', true );
$pdf->Ln(10);

// Pied de page 
$pdf->SetFillColor(3, 46, 62);
$pdf->Cell(190, 8, '',0, 0,'C', true );
$pdf_content = $pdf->Output('S'); // Sauvegarde du contenu du PDF dans une variable



            // Mettre à jour le champ PDF dans la table facture avec le contenu du PDF
            $stmt_update_pdf = $conn->prepare("UPDATE facture SET pdf = :pdf  WHERE id_consomation = :id_consomation");
            $stmt_update_pdf->bindParam(':pdf', $pdf_content, PDO::PARAM_LOB);
            $stmt_update_pdf->bindParam(':id_consomation', $consomation_id);
         
            $stmt_update_pdf->execute();
           
           
           
          header("Location: consomation.php?msg=You can check your invoice space to view the invoice");



        } 
        else {
           
            $consomation_id = $conn->lastInsertId();
            // Si l'état est en attente, stocker les informations dans la table facture avec l'état en attente et le PDF en cours de traitement
            $pdf = 'En cours de traitement';
            $etat ="non paye";
            $stmt_facture = $conn->prepare("INSERT INTO facture (id_client, prix_HT, prix_TTC, id_consomation,  pdf ,etat) 
            VALUES (:id_client, :prix_ht, :prix_ttc, :id_consomation,  :pdf , :etat)");
            $stmt_facture->bindParam(':id_client', $id);
            $stmt_facture->bindParam(':prix_ht', $prix_ht);
            $stmt_facture->bindParam(':prix_ttc', $prix_ttc);
            $stmt_facture->bindParam(':etat', $etat);
            $stmt_facture->bindParam(':id_consomation', $consomation_id);
            $stmt_facture->bindParam(':pdf', $pdf);
            $stmt_facture->execute();
           
         
           
          

          header("Location: consomation.php?msg=Your invoice is being processed");

        } 
        
    } catch(PDOException $e) {
        echo "Failed: " . $e->getMessage();
    }
}

}
?>







<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

        <link rel="stylesheet" href="style.css">
    <title>Complaints</title>
    <style>
       
body {
    background: linear-gradient(rgba(0, 0, 0, 0.9), #032E3E) fixed; /* Dégradé linéaire de noir à #032E3E */
    color: white; /* Couleur du texte */
}

        .container {
         
            padding: 20px;
            border-radius: 10px;
            margin-top: 20px;
          
        }

        .container1 {
          
            padding: 20px;
            border-radius: 10px;
            margin-top: 20px;
          
            width: 800px;
            margin-left: 280px;
        }

        .container:hover {
            transform: scale(1.02); /* Effet d'agrandissement sur le survol */
        }

        .text-center {
            color: #060B32; /* Noir */
        }

        .form-label {
            color: #212529; /* Noir */
        }

        .form-control {
            background-color: #fff; /* Blanc */
            border-color: #ced4da; /* Bleu marine clair */
        }

        .btn-success {
            background-color: #659931; /* Orange */
            border-color: #060B32; /* Orange */
            font-size: 1.2rem; /* Taille de police plus grande */
            margin-right: 10px; /* Espacement entre les boutons */
            transition: background-color 0.3s ease; /* Effet de transition sur le changement de couleur */
        }

        .btn-danger {
            background-color: #FE0000; /* Rouge clair */
            border-color: #ff6b6b; /* Rouge clair */
            font-size: 1.2rem; /* Taille de police plus grande */
            transition: background-color 0.3s ease; /* Effet de transition sur le changement de couleur */
        }

        .btn-success:hover {
            background-color: black; /* Orange foncé pour survol */
        }

        .btn-danger:hover {
            background-color: black; /* Rouge foncé pour survol */
        }

        .form-check-input {
            margin-right: 10px; /* Espacement entre les boutons radios */
        }

        h3 {

            color:  #F2A42D; /* Noir */
            margin-top: 30px;
            margin-bottom: 20px;
            font-size: 36px; /* Taille de la police */
            text-align: center; /* Centrer le texte */
            text-transform: uppercase; /* Mettre en majuscules */
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3); /* Ombre portée */
        }

        .text {
            text-align: center;
            color: #000;
            font-weight: bold;
        }

        label {
            font-weight: bold;
            color: #032E3E;
        }

        .mr-custom {
    margin-right: 270px; /* Ou la valeur de votre choix */
}
.mr-custom2 {
    margin-left: 400px; /* Ou la valeur de votre choix */
    margin-right: 10px;
    color: #2DDCE8; /* Couleur du texte */
    text-decoration: none; /* Suppression du soulignement par défaut des liens */
    transition: color 0.3s ease; /* Animation de transition pour la couleur du texte */
    font-weight: bold;
}

.custom-alert {
    background-color: black; /* Couleur de fond noire */
    color: #2DDCE8; /* Couleur du texte blanc */
}

.btn-close-white {
    color: #2DDCE8; /* Couleur du bouton de fermeture en blanc */
}


    </style>
</head>

<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark-transparent">
<a class="navbar-brand mr-custom" style="color: #F2A42D;">Electricity</a>
       
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item ">
                <a class="nav-link" href="Acceuil.php">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="AcceuilClient.php">Profil</a>
                </li>
                <li class="nav-item ">
                    <a class="nav-link" href="ReclamationClient.php">Complaints</a>
                </li>
                <li class="nav-item active">
                    <a class="nav-link" style="color: #2DDCE8;" href="consomation.php">Consumption</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="facture.php">Invoice</a>
                </li>
                <li class="nav-item">
                <a class="nav-link" href="login.html">Logout</a>
                </li>
            </ul>
            <span class="user-avatar mr-custom2" ><?php echo $prenom ?></span>
            <img src="images/rectangle 12.png" class="rounded-circle" alt="User Avatar" style="width: 32px; height: 32px;">
        </div>
    </nav>



 
    <div class="container">
    <?php
if (isset($_GET["msg"])) {
    $msg = $_GET["msg"];
    echo '<div class="alert alert-warning alert-dismissible fade show custom-alert" role="alert">
    ' . $msg . '
    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close" onclick="removeMsgParam()"></button>
    </div>';
}
?>
        <h3>Add Consumption</h3>
        <p class="text">Remember to click 'Save' after making any changes</p>

        <div class="row justify-content-center">
            <div class="col-md-7">
            <form action="" method="post" style="width:50vw; min-width:300px;"  enctype="multipart/form-data">
    
    
    <div class="mb-3">
       
        <input type="date" class="form-control" name="date" id="date" placeholder="Date" required>
    </div>
    <div class="mb-3">
       
        <input type="number" class="form-control" name="quantite" placeholder=" Exemple :200,15 KWH" pattern="^\d+(\.\d{1,2})?$" min="0" required>
    </div>
    <div class="mb-3">
    
        <input type="file" class="form-control" name="compteur" placeholder="Entrer une image de compteur" required>
    </div>

    

    <div>
        <button type="submit" class="btn btn-success" name="submit">Save</button>
        <button type="reset" class="btn btn-danger" name="submit">Cancel</button>
    </div>
</form>

            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
    // Obtenez la date système
    var today = new Date();
    var year = today.getFullYear();
    var month = today.getMonth() + 1; // Les mois commencent à partir de zéro, donc on ajoute 1
    var day = today.getDate();
    
    // Formattez la date système dans le format YYYY-MM-DD pour la comparaison avec la date sélectionnée
    var formattedDate = year + '-' + (month < 10 ? '0' : '') + month + '-' + (day < 10 ? '0' : '') + day;

    // Définissez la valeur maximale de l'input date sur la date système
    document.getElementById('date').setAttribute('max', formattedDate);
</script>
   

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>
