<?php
require("ConnectionDB.php");

// Démarrer une session
session_start();


if (isset($_SESSION["id"])) {
    $id = $_SESSION["id"];
    $prenom = $_SESSION['prenom'];

    // Récupérer les données de session
    $first_name = isset($_SESSION['nom']) ? $_SESSION['nom'] : '';
    $last_name = isset($_SESSION['prenom']) ? $_SESSION['prenom'] : '';
    $email = isset($_SESSION['email']) ? $_SESSION['email'] : '';
    $tel = isset($_SESSION['Tel']) ? $_SESSION['Tel'] : '';
    $date = isset($_SESSION['date']) ? $_SESSION['date'] : '';
    $CIN = isset($_SESSION['CIN']) ? $_SESSION['CIN'] : '';
    $gender = isset($_SESSION['gender']) ? $_SESSION['gender'] : '';
    $adresse = isset($_SESSION['adresse']) ? $_SESSION['adresse'] : '';
    $prefix = isset($_SESSION['prefix']) ? $_SESSION['prefix'] : '';
} else {
    
    exit(); // Arrêter l'exécution si l'ID n'est pas trouvé
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Electricity</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <link rel="stylesheet" href="style.css">
    <style>

body {
    background: linear-gradient(rgba(0, 0, 0, 0.9), #032E3E) fixed; /* Dégradé linéaire de noir à #032E3E */
    color: white; /* Couleur du texte */
}

        .custom-card {
    background: linear-gradient(rgba(0, 0, 0, 0.9), #F2A42D); /* Dégradé linéaire de noir à F2A42D */
    border-radius: 30px; /* Supprimer la bordure */
    color: white; /* Couleur du texte */
}

.custom-card .card-body {
    padding: 20px; /* Espacement à l'intérieur de la carte */
}

.custom-card:hover {
        transform: translateY(-5px); /* Translation de 5 pixels vers le haut lors du survol */
        transition: transform 0.3s ease; /* Ajouter une transition fluide */
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


    </style>
  
</head>
<body >
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
                    <a class="nav-link active" style="color: #2DDCE8;" href="AcceuilClient.php">Profil</a>
                </li>
                <li class="nav-item ">
                    <a class="nav-link" href="ReclamationClient.php">Complaints</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="consomation.php">Consumption</a>
                </li>
                <li class="nav-item ">
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


    <div class=" justify-content-center align-items-center">
       <div class=" text-center mb-4">
            <h1 style="color: #F2A42D;">Welcome back!</h1>
            <p>Trust in us for dependable and eco-friendly energy solutions, dedicated to powering your life sustainably and efficiently.</p>
        </div>
        </div>

    <div class="container-fluid d-flex justify-content-center align-items-center vh-90">

    <div class="content-container" style="width:900px; height: auto;">
        <div class="row justify-content-center mb-4"> <!-- Ajoutez la classe mb-4 ici -->
            <div class="col-md-4">
                <div class="card mb-3 custom-card border-0">
                    <div class="card-body">
                        <h5 class="card-title" >First Name</h5>
                        <p class="card-text"><?php echo $first_name; ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card mb-3 custom-card border-0">
                    <div class="card-body">
                        <h5 class="card-title">Last Name</h5>
                        <p class="card-text"><?php echo $last_name; ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card mb-3 custom-card border-0">
                    <div class="card-body">
                        <h5 class="card-title">Cin</h5>
                        <p class="card-text"><?php echo $CIN; ?></p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row justify-content-center">
            <div class="col-md-4">
                <div class="card mb-3 custom-card border-0">
                    <div class="card-body">
                        <h5 class="card-title">Birthday</h5>
                        <p class="card-text"><?php echo $date; ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card mb-3 custom-card border-0">
                    <div class="card-body">
                        <h5 class="card-title">Gender</h5>
                        <p class="card-text"><?php echo $gender; ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card mb-3 custom-card border-0">
                    <div class="card-body">
                        <h5 class="card-title">Email</h5>
                        <p class="card-text"><?php echo $email; ?></p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row justify-content-center">
            <div class="col-md-4">
                <div class="card mb-3 custom-card border-0">
                    <div class="card-body">
                        <h5 class="card-title">Adress</h5>
                        <p class="card-text"><?php echo $adresse; ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card mb-3 custom-card border-0">
                    <div class="card-body">
                        <h5 class="card-title">Prefix</h5>
                        <p class="card-text"><?php echo $prefix; ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card mb-3 custom-card border-0">
                    <div class="card-body">
                        <h5 class="card-title">Tel</h5>
                        <p class="card-text"><?php echo $tel; ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>




    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
