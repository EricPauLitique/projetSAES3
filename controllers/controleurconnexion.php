<?php
require_once("../config/connexion.php");
require_once("../modele/utilisateur.php");
$titre = "Connexion"; 
include("../vue/debut.php");

session_start(); // Démarre la session

Connexion::connect();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupération et validation des données
    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    $password = $_POST['password'];

    if (!$email || empty($password)) {
        echo '<p style="color: red;"><b>Veuillez fournir une adresse e-mail valide et un mot de passe.</b></p>';
        include("../vue/connexion.html");
        exit;
    }

    try {
        $pdo = Connexion::PDO();

        // Recherche de l'utilisateur par e-mail
        $stmt = $pdo->prepare("SELECT * FROM utilisateur WHERE user_mail = :email");
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['user_mdp'])) {
            // Connexion réussie
            $_SESSION['prenom'] = $user['user_prenom'];
            $_SESSION['nom'] = $user['user_nom'];
            
            // Redirection vers la page d'accueil
            header("Location: ../vue/accueil.php");
            exit;
        } else {
            // Email ou mot de passe incorrect
            echo '<p style="color: red;"><b>Email ou mot de passe incorrect.</b></p>';
            include("../vue/connexion.html");
            exit;
        }
    } catch (Exception $e) {
        error_log("Erreur de connexion : " . $e->getMessage());
        echo '<p style="color: red;"><b>Une erreur est survenue. Veuillez réessayer plus tard.</b></p>';
    }
}
?>
