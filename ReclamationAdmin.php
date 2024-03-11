<?php
require("ConnectionDB.php");
session_start();
$id = $_SESSION['id'];
$prenom = $_SESSION['prenom'];


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
  <title>Reclamation</title>
  <style>
    body {
    background: linear-gradient(rgba(0, 0, 0, 0.9), #032E3E) fixed; /* Dégradé linéaire de noir à #032E3E */
    color: white; /* Couleur du texte */
}


   
    .container {
      margin-top: 50px; /* Marge en haut pour la carte */
    
     
      padding: 20px;
    
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
  width: 30px; /* Taille de l'image */
  height: 30px;
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
  margin-left: 300px;
  margin-top: 0px;
  margin-bottom: 40px;
}

#searchInput {
  width: 500px;
  padding: 12px;
  border: none;
  border-radius: 25px;
  font-size: 16px;
  background: linear-gradient(135deg, #2DB2F2, #DC8B10);
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
            <ul class="navbar-nav ml-auto ">
                <li class="nav-item ">
                <a class="nav-link" href="Acceuil.php">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="AccueilAdmin.php">Dashbord</a>
                </li>
                <li class="nav-item  ">
                    <a class="nav-link"   href="AllClients.php">Clients</a>
                </li>
                <li class="nav-item active">
                    <a class="nav-link" style="color: #2DDCE8;" href="ReclamationAdmin.php">Complaints</a>
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


  <h1>Handling of customer complaints</h1> 

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
   <div class="search-container">
   <div class="search-wrapper">
  <input type="text" id="searchInput" onkeyup="filterTable()" placeholder="Search for names...">
  <img src="images/recherche.png" class="search-image" alt="Search Image">
   </div>
    
  </div>

 

    <table class="table table-hover text-center" id="myTable">
      <thead class="table-header">
        <tr>
        <th scope="col">Complaint ID</th>
<th scope="col">Client ID</th>
<th scope="col">Type</th>
<th scope="col">Invoice Number</th>
<th scope="col">Date</th>
<th scope="col">Complaint</th>
<th scope="col">Action</th>
          
        </tr>
      </thead>
      <tbody>
      <?php
     $sql = "SELECT * FROM reclamation WHERE  etat='non traite' ";

      $stmt = $conn->prepare($sql);
      $stmt->execute();
      $clients = $stmt->fetchAll(PDO::FETCH_ASSOC);

      foreach ($clients as $row) {
      ?>
        <tr>
          <td><?php echo $row["id_rec"] ?></td>
          <td><?php echo $row["id_client"] ?></td>
          <td><?php echo $row["type"] ?></td>
          <td><?php echo  ( $row["numcontrat"] != "") ? $row["numcontrat"] : "-";?></td>
          <td><?php echo ($row["date"] != "0000-00-00") ? $row["date"] : "-"; ?></td>
          <td><?php echo $row["message"] ?></td>
          <td class="action-buttons">
           
          <a href="traiterReclamation.php?id=<?php echo $row["id_rec"] ?>" class="link-modifier"> <i class="fas fa-cogs fa-2x orange-icon"></i></a>
          </td>
        </tr>
      <?php
      }
      ?>
      </tbody>
    </table>
    
  </div>

  <!-- Bootstrap -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script>
  <script>
function removeMsgParam() {
    // Effacer le paramètre "msg" de l'URL
    var urlWithoutMsg = window.location.href.split("?")[0];
    window.history.replaceState({}, document.title, urlWithoutMsg);
}

function filterTable() {
  var input, filter, table, tr, td, th, i, j, txtValue, visibleRows;
  input = document.getElementById("searchInput");
  filter = input.value.toUpperCase();
  table = document.getElementById("myTable");
  tr = table.getElementsByTagName("tr");
  th = table.getElementsByTagName("th"); // Get the table header elements
  visibleRows = 0; // Initialize the counter for visible rows

  // Loop through all table rows, and hide those that don't match the search query
  for (i = 1; i < tr.length; i++) {
    td = tr[i].getElementsByTagName("td");
    var matchFound = false;
    for (j = 0; j < td.length; j++) {
      txtValue = td[j].textContent || td[j].innerText;
      if (txtValue.toUpperCase().indexOf(filter) > -1) {
        matchFound = true;
        break;
      }
    }

    if (matchFound) {
      // Increment the counter if a match is found
      visibleRows++;
      // Show the row
      tr[i].style.display = "";
    } else {
      // Hide the row if no match is found
      tr[i].style.display = "none";
    }
  }

  // Show or hide the header based on the number of visible rows
  if (visibleRows >= 1) {
    for (i = 0; i < th.length; i++) {
      th[i].style.display = "";
    }
  } else {
    for (i = 0; i < th.length; i++) {
      th[i].style.display = "none";
    }
  }

  // Show the table if there are visible rows, otherwise hide it
  table.style.display = (visibleRows > 0) ? "" : "none";
}

</script>
</body>

</html>
