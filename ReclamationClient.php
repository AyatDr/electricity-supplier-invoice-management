<?php
require('ConnectionDB.php');
session_start();
$id = $_SESSION['id'];
$prenom = $_SESSION['prenom'];


if (isset($_POST["submit"])) {
    $type = $_POST['type'];
    $date = $_POST['month'];
    $autre = $_POST['autre'];
    $numeroFacture = $_POST['numcontrat'];
    $etat = "non traite";
    $message = $_POST['message'];
   
     
        
   
    try {
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        $sql = "INSERT INTO Reclamation (id_client, type, autre, date, numcontrat, etat,message) 
        VALUES (:id_client, :type, :autre, :date, :num, :etat,:message)";

$stmt = $conn->prepare($sql);
$stmt->bindParam(':id_client', $id);
$stmt->bindParam(':type', $type);
$stmt->bindParam(':autre', $autre);
$stmt->bindParam(':date', $date);
$stmt->bindParam(':num', $numeroFacture);
$stmt->bindParam(':etat', $etat); // Lier la variable $etat ici
$stmt->bindParam(':message', $message);
$stmt->execute();



header("Location: ReclamationClient.php?msg=Your complaint has been successfully recorded.");
       
    } catch(PDOException $e) {
        echo "Failed: " . $e->getMessage();
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
                <li class="nav-item active">
                    <a class="nav-link" style="color: #2DDCE8;" href="ReclamationClient.php">Complaints</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="consomation.php">Consumption</a>
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

        <h3>Send complaint</h3>
        <p class="text">Remember to click 'Send' after filling the information</p>

        <div class="row justify-content-center">
            <div class="col-md-6">
                <form action="" method="post" enctype="multipart/form-data">
                    <div class="mb-3">
                      
                        <select class="form-select custom-select-bg-orange" id="type" name="type" style="background-color: #000000;">
                           <option value="" selected disabled>Choose a type</option>
                            <option value="fuite interne">fuite interne</option>
                            <option value="fuite externe">fuite externe</option>
                            <option value="facture">facture</option>
                            <option value="autre">Autre</option>
                        </select>
                    </div>
                    
                    <div class="mb-3" id="autre" style="display: none;">
                     
                        <input type="textarea" class="form-control" id="autre" name="autre" placeholder="Autre type">
                    </div>
                    
                    <div class="mb-3" id="numcontrat" style="display: none;">
                     
                        <input type="text" class="form-control" id="numcontrat" name="numcontrat" placeholder="Numero Facture">
                    </div>
                    
                    <div class="mb-3" id="date" style="display: none;">
                      
                        <input type="date" class="form-control"  id="date1" name="month" placeholder="Date facture">
                    </div>
                    
                    <div class="mb-3" id="message" style="display: none;">
                       
                        <textarea class="form-control" id="message" name="message" placeholder="Enter Your complaint"></textarea>
                    </div>

                    <div class="mb-3">
                        <button type="submit" class="btn btn-success" name="submit">Complaint</button>
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
         function removeMsgParam() {
    // Effacer le paramètre "msg" de l'URL
    var urlWithoutMsg = window.location.href.split("?")[0];
    window.history.replaceState({}, document.title, urlWithoutMsg);
}

    // Obtenez la date système
    var today = new Date();
    var year = today.getFullYear();
    var month = today.getMonth() + 1; // Les mois commencent à partir de zéro, donc on ajoute 1
    var day = today.getDate();
    
    // Formattez la date système dans le format YYYY-MM-DD pour la comparaison avec la date sélectionnée
    var formattedDate = year + '-' + (month < 10 ? '0' : '') + month + '-' + (day < 10 ? '0' : '') + day;

    // Définissez la valeur maximale de l'input date sur la date système
    document.getElementById('date1').setAttribute('max', formattedDate);
</script>
   
    <script>
        document.addEventListener("DOMContentLoaded", function () {
    // Cache les champs "Numero Contrat", "Date", "Autre" et "Message" au chargement de la page
    var num = document.getElementById("numcontrat");
    var date = document.getElementById("date");
    var autre = document.getElementById("autre");
    var message = document.getElementById("message");

    // Ajoute un écouteur d'événements sur le menu déroulant "Type"
    document.getElementById("type").addEventListener("change", function () {
        // Affiche ou cache les champs en fonction de la sélection dans le menu déroulant
        if (this.value === "facture") {
            num.style.display = "block";
            date.style.display = "block";
            autre.style.display = "none";
        } else if (this.value === "autre") {
            autre.style.display = "block";
            num.style.display = "none";
            date.style.display = "none";
        } else {
            num.style.display = "none";
            date.style.display = "none";
            autre.style.display = "none";
        }

        // Toujours afficher le champ "Message" dans tous les cas
        message.style.display = "block";
    });
});

    </script>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>
