<?php
require("ConnectionDB.php");

session_start();
$id = $_GET["id"];
$prenom = $_SESSION['prenom'];

if (isset($_POST["submit"])) {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $tel = $_POST['Tel'];
    $date = $_POST['date'];
    $CIN = $_POST['CIN'];
    $gender = $_POST['gender'];
    $password = $_POST['password'];
    $adress = $_POST['adress'];
    $prefix= $_POST['prefix'];
   
   
     
            // Email n'existe pas, procéder à l'insertion ou à la mise à jour
            $stmt = $conn->prepare("UPDATE client SET `nom`=:first_name, `prenom`=:last_name, `email`=:email, `Tel`=:Tel, `prefix`=:prefix , `date`=:date, `CIN`=:CIN, `gender`=:gender,`password`=:password , `adresse`=:adress WHERE id = :id");
            $stmt->bindParam(':first_name', $first_name);
            $stmt->bindParam(':last_name', $last_name);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':Tel', $tel);
            $stmt->bindParam(':date', $date);
            $stmt->bindParam(':CIN', $CIN);
            $stmt->bindParam(':gender', $gender);
            $stmt->bindParam(':prefix', $prefix);
            $stmt->bindParam(':adress', $adress);
            $stmt->bindParam(':password', $password);
            $stmt->bindParam(':id', $id);
            $stmt->execute();

            header("Location: AllClients.php?msg=Data updated successfully");
            exit();
       
    
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

        .btn-save {
            background-color:#659931; /* Orange */
            border-color::#659931 ;
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

        .btn-save:hover {
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
    margin-left: 300px; /* Ou la valeur de votre choix */
    margin-right: 10px;
    color: #2DDCE8; /* Couleur du texte */
    text-decoration: none; /* Suppression du soulignement par défaut des liens */
    transition: color 0.3s ease; /* Animation de transition pour la couleur du texte */
    font-weight: bold;
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
                <li class="nav-item">
                    <a class="nav-link" href="AccueilAdmin.php">Dashbord</a>
                </li>
                <li class="nav-item  active">
                    <a class="nav-link" style="color: #2DDCE8;"  href="AllClients.php">Clients</a>
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


 
    <div class="container">
        <h3>Edit User Information</h3>
        <p class="text">Remember to click 'Update' after making any changes</p>
        
        <?php
    try {
        $stmt = $conn->prepare("SELECT * FROM `client` WHERE id = :id LIMIT 1");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $row = $stmt->fetch();
    } catch(PDOException $e) {
        echo "Failed: " . $e->getMessage();
    }
    ?>



        <div class="row justify-content-center">
            <div class="col-md-7">
           
            <form action="" method="post" style="width:50vw; min-width:300px;">
    <div class="row mb-3">
        <div class="col">
            <input type="text" class="form-control" name="first_name" placeholder="First Name" value="<?= $row['nom'] ?>" required>
        </div>
        <div class="col">
            <input type="text" class="form-control" name="last_name" placeholder="Last Name" value="<?= $row['prenom'] ?>" required>
        </div>
    </div>
     <div class="row mb-3">
    <div class="col">
        <input type="email" class="form-control" name="email" placeholder="name@example.com" value="<?= $row['email'] ?>" required>
    </div>
    <div class="col">
        <input type="text" class="form-control" name="adress" placeholder="Enter Your Adress" value="<?= $row['adresse'] ?>" required>
    </div>
    </div>

    <div class="row mb-3">
        <div class="col">
            <select class="form-select" name="prefix" required>
        <option value="+212" <?= ($row['prefix'] == '+212') ? 'selected' : '' ?>>+212 (Morocco)</option>
        <option value="+1" <?= ($row['prefix'] == '+1') ? 'selected' : '' ?>>+1 (USA)</option>
        <option value="+44" <?= ($row['prefix'] == '+44') ? 'selected' : '' ?>>+44 (UK)</option>
        <option value="+33" <?= ($row['prefix'] == '+33') ? 'selected' : '' ?>>+33 (France)</option>
               
            </select>
        </div>
        <div class="col">
            <input type="tel" class="form-control" name="Tel" placeholder="Your phone number" pattern="[0-9]{1,15}" maxlength="15" value="<?= $row['Tel'] ?>"  required>
        </div>
    </div>

    <div class="mb-3">
        <input type="date" class="form-control" name="date" id="date1" value="<?= $row['date'] ?>" required>
    </div>

    <div class="mb-3">
        <input type="text" class="form-control" name="CIN" placeholder="Enter Your CIN" value="<?= $row['CIN'] ?>" required>
    </div>

    <div class="mb-3">
        <input type="password" class="form-control" name="password" placeholder="Enter Your Password" maxlength="260"  value="<?= $row['password'] ?>"  required>
    </div>

    <div class="form-group mb-3">
        <div class="d-flex flex-wrap">
            <div class="form-check me-4">
            <input type="radio" class="form-check-input" name="gender" id="male" value="male" <?= ($row["gender"] == 'male') ? "checked" : "" ?>>
                <label for="male" style="color:#F2A42D">Male</label>
            </div>
            <div class="form-check">
            <input type="radio" class="form-check-input" name="gender" id="female" value="female" <?= ($row["gender"] == 'female') ? "checked" : "" ?>>
                <label for="female" style="color:#F2A42D">Female</label>
            </div>
        </div>
    </div>

    <div>
        <button type="submit" class="btn btn-save" name="submit">Save</button>
        <button type="reset" class="btn btn-danger" name="submit">Cancel</button>
    </div>
</form>


      </div>

               
        </div>
    </div>

    <script>
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

    <!-- Bootstrap JS -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4"
        crossorigin="anonymous"></script>
   

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>
