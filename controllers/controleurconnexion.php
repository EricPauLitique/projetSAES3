<?php
require_once("../config/connexion.php");
require_once("../modele/utilisateur.php");

Connexion::connect();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Rechercher l'utilisateur dans la base de données
    $stmt = $pdo->prepare("SELECT * FROM utilisateur WHERE user_email = :email");
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Vérification du mot de passe
    if ($user && password_verify($password, $user['password'])) {
        echo "Connexion réussie. Bienvenue, " . htmlspecialchars($user['email']) . "!";
    } else {
        echo "Adresse e-mail ou mot de passe incorrect.";
    }
}

?>