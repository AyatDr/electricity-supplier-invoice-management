<?php
require("ConnectionDB.php");
session_start();
$id = $_SESSION['id'];
$prenom = $_SESSION['prenom'];

// Requête SQL pour récupérer la moyenne de consommation pour chaque mois de l'année 2024
$sql = "SELECT MONTH(date) AS month, AVG(quantite) AS avg_consumption FROM consomation WHERE YEAR(date) = 2024 GROUP BY MONTH(date)";
$stmt = $conn->prepare($sql);
$stmt->execute();
$monthlyData = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Création d'un tableau pour stocker les valeurs de consommation par mois
$consumptionData = array_fill(1, 12, 0); // Initialise toutes les valeurs à 0 pour chaque mois

// Remplissage du tableau avec les données de consommation récupérées de la base de données
foreach ($monthlyData as $data) {
    $month = intval($data['month']);
    $avg_consumption = floatval($data['avg_consumption']);
    $consumptionData[$month] = $avg_consumption;
}

// Récupérer le nombre de consommations annuelles avec et sans anomalie
try {
  $sql_annomalie = "SELECT COUNT(*) AS nb_annomalies FROM consommationannuelle WHERE difference = 'oui'";
  $stmt_annomalie = $conn->prepare($sql_annomalie);
  $stmt_annomalie->execute();
  $result_annomalie = $stmt_annomalie->fetch(PDO::FETCH_ASSOC);
  $nb_annomalies = $result_annomalie['nb_annomalies'];

  $sql_no_annomalie = "SELECT COUNT(*) AS nb_no_annomalies FROM consommationannuelle WHERE difference = 'non'";
  $stmt_no_annomalie = $conn->prepare($sql_no_annomalie);
  $stmt_no_annomalie->execute();
  $result_no_annomalie = $stmt_no_annomalie->fetch(PDO::FETCH_ASSOC);
  $nb_no_annomalies = $result_no_annomalie['nb_no_annomalies'];
} catch (PDOException $e) {
  echo "Erreur de connexion à la base de données : " . $e->getMessage();
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
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

        <link rel="stylesheet" href="style.css">
  <title>Dashbord</title>
  <style>
    body {
    background: linear-gradient(rgba(0, 0, 0, 0.9), #032E3E) fixed; /* Dégradé linéaire de noir à #032E3E */
   
}
#reclamationsChart {
            width: 300px; /* Largeur souhaitée */
            height: 300px; /* Hauteur souhaitée */
        }

   
    .container {
      margin-top: 50px; /* Marge en haut pour la carte */
    
     
      padding: 10px;
    
    }
    

    .btn-add {
  background-color: #ffac4b;
  border-color: #ffac4b;
  color: #fff;
  padding: 10px 20px;
  border-radius: 25px;
}


    .btn-add:hover {
      background-color: #ffac4b; /* Orange */
      border-color: #ffac4b; /* Orange */
      transform: scale(1.05); /* Augmentation de l'échelle au survol */
      box-shadow: 0px 0px 20px rgba(0, 0, 0, 0.5); /* Ombre plus prononcée */
     
    }

    .btn-edit {
      color: #28a745; /* Vert */
    }

    .btn-delete {
      color:  #060B32; /* Rouge */
    }

    .search-container {
  margin-top: 20px; /* Espace en haut */
  margin-bottom: 20px; /* Espace en bas */
  position: relative; /* Position relative pour que le bouton puisse être positionné par rapport à ce conteneur */
  text-align: center; /* Centrer le contenu */
  justify-content: space-between;

}



#searchInput:focus {
  background-color: #fafafa; /* Couleur de fond lorsqu'elle est en focus */
}

/* Style du bouton de recherche */
#searchButton {
  background-color: #007bff; /* Couleur de fond du bouton */
  color: white; /* Couleur du texte du bouton */
  border: none; /* Suppression de la bordure */
  padding: 12px 20px; /* Espace à l'intérieur du bouton */
  border-radius: 25px; /* Coins arrondis du bouton */
  cursor: pointer; /* Curseur pointeur */
}

/* Style du bouton de recherche au survol */
#searchButton:hover {
  background-color: #0056b3; /* Couleur de fond du bouton au survol */
}

.search-wrapper {
  position: relative;
}

  .hidden {
  display: none;
}

.table-header {
    background-color: black;
    color: white; /* Ajoutez d'autres styles selon vos besoins */
  }
#mytable{
  display: none;
}


.search-image {
  position: absolute;
  right: 40px; /* Positionnement à droite de l'input */
  top: 50%;
  transform: translateY(-50%);
  width: 20px; /* Taille de l'image */
  height: 20px;
}

  
    
    h1 {
        color:  #F2A42D; /* Noir */
      margin-top:30px;
      margin-bottom: 20px;
      font-size: 36px; /* Taille de la police */
      text-align: center; /* Centrer le texte */
      text-transform: uppercase; /* Mettre en majuscules */
      text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3); /* Ombre portée */
    }
  
    td {
      background-color: white !important;
}



.search-container {
  display: flex;
  align-items: center;
  justify-content: space-between;
}

#searchInput {
  width: 300px;
  padding: 12px;
  border: none;
  border-radius: 25px;
  font-size: 16px;
  background: linear-gradient(rgba(0, 0, 0, 0.9), #1ebbd7) ;
  box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
  outline: none;
  margin-right: 10px; /* Espacement entre l'input et l'image */
  
}

.search-wrapper {
  position: relative;
}

#searchInput::placeholder {
  color: black;
}

.btn-add {
  background-color: #ffac4b;
  border-color: #ffac4b;
  color: #fff;
  padding: 10px 20px;
  border-radius: 25px;
  text-decoration: none;
}


.table-bordered {
  border-radius: 20px; /* Ajout du border radius */
}


    .card {
      background: linear-gradient(135deg, #F2A42D 0%, #000000 100%);
     
      border-radius: 20px;
      transition: transform 0.3s ease, box-shadow 0.3s ease;
      box-shadow: 10px 35px 10px rgba(0, 0, 0, 0.7);

    }
  
    .card-text {
      color: #2DDCE8;
      font-weight: bold;
      font-size: 24px;
    }
    /* Centrer le texte des paragraphes */
    .card-body {
      text-align: center;
    }

    @keyframes zoom {
      0% {
        transform: scale(1);
      }
      100% {
        transform: scale(1.05);
      }
    }

    /* Ajouter une animation de zoom au survol */
    .card:hover {
      animation: zoom 0.3s ease forwards; /* Utiliser l'animation de zoom */
    }
    .mr-custom {
    margin-right: 270px; /* Ou la valeur de votre choix */
}
.mr-custom2 {
  margin-left: 300px; /* Ou la valeur de votre choix */
    margin-right: 10px;
    color: #2DDCE8;
    text-decoration: none; /* Suppression du soulignement par défaut des liens */
    transition: color 0.3s ease; /* Animation de transition pour la couleur du texte */
    font-weight: bold;
}

.card-lg {
    width: 280px;
    
}

.image{
  width: 110px;
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
            <ul class="navbar-nav ml-auto ">
                <li class="nav-item ">
                    <a class="nav-link" href="Acceuil.php">Home</a>
                </li>
                <li class="nav-item active">
                    <a class="nav-link" style="color: #2DDCE8;" href="AccueilAdmin.php">Dashbord</a>
                </li>
                <li class="nav-item  ">
                    <a class="nav-link"   href="AllClients.php">Clients</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="ReclamationAdmin.php">Complaints</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link"  href="anomalies.php">Anomalie</a>
                </li>
                <li class="nav-item ">
                    <a class="nav-link" href="ConsommationAnnuelle.php">Annual consumption</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="login.html">Logout</a>
                </li>
            </ul>
            <span class="user-avatar mr-custom2" ><?php echo $prenom ?></span>
            <img src="images/rectangle 11.png" class="rounded-circle" alt="User Avatar" style="width: 32px; height: 32px;">
        </div>
    </nav>

   
    
    <div class="container mt-3">
    <div class="row">
        <div class="col-md-3">
            <div class="card">
                <div class="card-body d-flex align-items-center">
                    <img src="images/annuler.png" class="img-fluid mr-3 image" alt="...">
                    <div>
                        <?php 
                        // Requête SQL pour compter le nombre de réclamations non traitées
                        try {
                            $sql = "SELECT COUNT(*) AS total FROM consomation WHERE etat = 'en attente'";
                            $stmt = $conn->prepare($sql);
                            $stmt->execute();
                            $result = $stmt->fetch(PDO::FETCH_ASSOC);
                            $complaints_not_processed = $result['total'];
                        } catch (PDOException $e) {
                            echo "Erreur de connexion à la base de données : " . $e->getMessage();
                        }
                        ?>
                        <p class="card-text"><?php echo $complaints_not_processed; ?></p>
                        <h5 class="card-title">Number Of Anomalies</h5>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body d-flex align-items-center">
                    <img src="images/client.png" class="img-fluid mr-3 image" alt="...">
                    <div>
                        <?php 
                        // Requête SQL pour compter le nombre de clients
                        try {
                            $sql = "SELECT COUNT(*) AS total FROM client";
                            $stmt = $conn->prepare($sql);
                            $stmt->execute();
                            $result = $stmt->fetch(PDO::FETCH_ASSOC);
                            $clients_total = $result['total'];
                        } catch (PDOException $e) {
                            echo "Erreur de connexion à la base de données : " . $e->getMessage();
                        }
                        ?>
                        <p class="card-text"><?php echo $clients_total; ?></p>
                        <h5 class="card-title">Number Of Clients</h5>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body d-flex align-items-center">
                    <img src="images/prise.png" class="img-fluid mr-3 image" alt="...">
                    <div>
                        <?php 
                        // Requête SQL pour calculer la moyenne de consommation
                        try {
                            $sql = "SELECT AVG(quantite) AS moyenne_consommation FROM consomation";
                            $stmt = $conn->prepare($sql);
                            $stmt->execute();
                            $result = $stmt->fetch(PDO::FETCH_ASSOC);
                            $average_consumption = $result['moyenne_consommation'];
                            $formatted_average_consumption = number_format($average_consumption, 2);
                        } catch (PDOException $e) {
                            echo "Erreur de connexion à la base de données : " . $e->getMessage();
                        }
                        ?>
                        <p class="card-text"><?php echo $formatted_average_consumption; ?></p>
                        <h5 class="card-title">Monthly Consumption</h5>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body d-flex align-items-center">
                    <img src="images/prix.png" class="img-fluid mr-3 image" alt="...">
                    <div>
                        <?php 
                        // Requête SQL pour calculer le total des prix
                        try {
                            $sql = "SELECT SUM(prix_TTC) AS total FROM facture";
                            $stmt = $conn->prepare($sql);
                            $stmt->execute();
                            $result = $stmt->fetch(PDO::FETCH_ASSOC);
                            $total_price = $result['total'];
                        } catch (PDOException $e) {
                            echo "Erreur de connexion à la base de données : " . $e->getMessage();
                        }
                        ?>
                        <p class="card-text"><?php echo $total_price; ?></p>
                        <h5 class="card-title">Sum of Prices</h5>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php 
    try {
      $sql = "SELECT COUNT(*) AS total FROM reclamation WHERE etat = 'traite'";
      $stmt = $conn->prepare($sql);
      $stmt->execute();
      $result = $stmt->fetch(PDO::FETCH_ASSOC);
      $complaints_processed = $result['total'];
  } catch (PDOException $e) {
      echo "Erreur de connexion à la base de données : " . $e->getMessage();
  }
  
  // Récupérer le nombre de réclamations non traitées
  try {
      $sql = "SELECT COUNT(*) AS total FROM reclamation WHERE etat = 'non traite'";
      $stmt = $conn->prepare($sql);
      $stmt->execute();
      $result = $stmt->fetch(PDO::FETCH_ASSOC);
      $complaints_not_processed = $result['total'];
  } catch (PDOException $e) {
      echo "Erreur de connexion à la base de données : " . $e->getMessage();
  }
    ?>
     <!-- #region -->
    <div class="row mt-5">
        <div class="col-md-4 ">
            <canvas id="reclamationsChart" width="50" height="50" ></canvas>
        </div>

        <div class="col-md-4 ">
         <canvas id="monthlyConsumptionChart" width="50" height="50"></canvas>
        </div>
       
    <div class="col-md-4">
        <canvas id="consommationChart" width="50" height="50"></canvas>
    </div>

    
</div>
<script>
        // Données à partir de PHP
        var reclamationsData = {
            labels: ["Processed complaint", "Unprocessed complaint"],
            datasets: [{
                data: [<?php echo $complaints_processed; ?>, <?php echo $complaints_not_processed; ?>],
                backgroundColor: [
                    'rgba(45,220,232,0.5)', // Couleur pour les réclamations traitées
                    'rgba(0, 0, 0, 0.5)'  // Couleur pour les réclamations non traitées
                ],
                borderWidth: 1
                
            }]
        };

        // Configuration du diagramme
        var options = {
            plugins: {
                labels: {
                    font: {
                        size: 14, // Taille de la police
                        family: 'Arial', // Type de police
                        color: 'black' // Couleur de la police
                    },
                    position: 'inside', 
                   
                }
            },
            responsive: true,
            maintainAspectRatio: false
        };

        // Création du diagramme
        var ctx = document.getElementById('reclamationsChart').getContext('2d');
        var myPieChart = new Chart(ctx, {
            type: 'pie',
            data: reclamationsData,
            options: options
        });
    </script>

<script>
        // Données pour le graphique
        var monthlyData = {
            labels: ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"],
            datasets: [{
                label: 'Average Consumption 2024',
                data: <?php echo json_encode(array_values($consumptionData)); ?>, // Remplacez ces valeurs par vos données
                backgroundColor: 'rgba(0, 0, 0, 0.5)', // Couleur des barres
                borderColor: 'rgba(242, 164, 45, 0.5)', // Couleur de la bordure des barres
                borderWidth: 1
            }]
        };

        // Configuration du graphique
        var options = {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Average Consumption'
                    }
                }
            }
        };

        // Création du graphique en barres
        var ctx = document.getElementById('monthlyConsumptionChart').getContext('2d');
        var myBarChart = new Chart(ctx, {
            type: 'bar',
            data: monthlyData,
            options: options
        });
    </script>
    
      <script>
        // Données à partir de PHP ou une autre source de données
        var data = {
            labels: ['With Anomalie', 'Without Anomalie'],
            datasets: [{
                label: 'Annual Consumption 2023',
                data: [<?php echo $nb_annomalies?>, <?php echo $nb_no_annomalies?>], // Remplacez ces valeurs par vos données
                backgroundColor: [
                    'rgba(242, 164, 45, 0.5)', // Couleur pour les consommations avec anomalie
                    'rgba(45,220,232,0.5)'  // Couleur pour les consommations sans anomalie
                ],
                borderColor: [
                    'rgba(255, 99, 132, 1)', // Couleur de bordure pour les consommations avec anomalie
                    'rgba(54, 162, 235, 1)'  // Couleur de bordure pour les consommations sans anomalie
                ],
                borderWidth: 1
            }]
        };

        var options = {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        };

        var ctx = document.getElementById('consommationChart').getContext('2d');
        var myChart = new Chart(ctx, {
            type: 'bar',
            data: data,
            options: options
        });
    </script>
<!-- Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
 
</body>

</html>
