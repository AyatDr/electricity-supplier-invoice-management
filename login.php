<?php
require("ConnectionDB.php");
// Démarrer une session
session_start();

// Vérifier si une session est active
if (session_status() == PHP_SESSION_ACTIVE) {
    // Destruction de la session existante
    session_destroy();
}


if (isset($_POST['username'], $_POST['password'])) {

    function validate($data){
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }

    $uname = validate($_POST['username']);
    $pass = validate($_POST['password']);

    if (empty($uname)) {
        echo "Username is required";
        exit();
    } else if (empty($pass)) {
        echo "Password is required";
        exit();
    } else {

        // Vérifier si c'est un admin
        $sql_admin = "SELECT * FROM admin WHERE email=:uname AND password=:pass";
        $stmt_admin = $conn->prepare($sql_admin);
        $stmt_admin->bindParam(':uname', $uname);
        $stmt_admin->bindParam(':pass', $pass);
        $stmt_admin->execute();
        $admin_row = $stmt_admin->fetch(PDO::FETCH_ASSOC);

        // Vérifier si c'est un client
        $sql_client = "SELECT * FROM client WHERE email=:uname";
        $stmt_client = $conn->prepare($sql_client);
        $stmt_client->bindParam(':uname', $uname);
        $stmt_client->execute();
        $client_row = $stmt_client->fetch(PDO::FETCH_ASSOC);

        if ($admin_row) {
            // Démarrer une nouvelle session pour l'admin
            session_start();
            $_SESSION['user_type'] = 'admin';
            $_SESSION['id'] = $admin_row['id'];
            $_SESSION['email'] = $admin_row['email'];
            $_SESSION['password'] = $admin_row['password'];
            $_SESSION['nom'] = $admin_row['nom'];
            $_SESSION['prenom'] = $admin_row['prenom'];
            header("Location: AccueilAdmin.php");
            exit();
        } elseif ($client_row) {
            // Récupérer le mot de passe hashé de la base de données
            $hashed_password = $client_row['password'];

            if (password_verify($pass, $hashed_password)) {
                // Démarrer une nouvelle session pour le client
                session_start();
                $_SESSION['user_type'] = 'client';
                $_SESSION['id'] = $client_row['id'];
                $_SESSION['email'] = $client_row['email'];
                $_SESSION['nom'] = $client_row['nom'];
                $_SESSION['prenom'] = $client_row['prenom'];
                $_SESSION['Tel'] = $client_row['Tel'];
                $_SESSION['CIN'] = $client_row['CIN'];
                $_SESSION['date'] = $client_row['date'];
                $_SESSION['gender'] = $client_row['gender'];
                $_SESSION['adresse'] = $client_row['adresse'];
                $_SESSION['prefix'] = $client_row['prefix'];
                header("Location: AcceuilClient.php");
                exit();
            } else {
                echo "Incorrect password";
                exit();
            }
        } else {
            echo "Incorrect username or password";
            exit();
        }
    }


} else {
    echo "Form not submitted";
    exit();
}
?>
