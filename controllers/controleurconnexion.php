<?php
require_once("../config/connexion.php");
require_once("../modele/utilisateur.php");
include("../vue/debut.php");
session_start(); // Démarre la session
$titre = "Connexion";

Connexion::connect();


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    $password = $_POST['password'];

    if (!$email || empty($password)) {
        echo '<p style="color: red;"><b>Veuillez fournir une adresse e-mail valide et un mot de passe.</b></p>';
        include ("../vue/connexion.html");
        exit;
    }

    try {
        $pdo = Connexion::PDO();
        $stmt = $pdo->prepare("SELECT * FROM utilisateur WHERE user_mail = :email");
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            echo '<p style="color: red;"><b>Adresse e-mail incorrecte.</b></p>';
            include ("../vue/connexion.html");
            exit;

        } elseif ($password == $user['user_mdp']) {
            // Correct login
            echo "Connexion réussie. Bienvenue, " . htmlspecialchars($user['user_prenom']) . "!";
                   // Après vérification réussie des identifiants
            $_SESSION['prenom'] = $user['user_prenom']; // Stocke le prénom dans la session
            $_SESSION['nom'] = $user['user_nom'];       // Stocke le nom dans la session

        // Redirection vers la page d'accueil
        header("Location: ../vue/accueil.php");
        exit;
        } else {
            // Incorrect password
            echo '<p style="color: red;"><b>Mot de passe incorrect.</b></p>';

            include ("../vue/connexion.html");
        }



 
    } catch (Exception $e) {
        echo "Erreur : " . $e->getMessage();
    }
}
?>
