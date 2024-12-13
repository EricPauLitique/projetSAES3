<?php
require_once("../config/connexion.php");
require_once("../modele/utilisateur.php");

Connexion::connect();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    $password = $_POST['password'];

    if (!$email || empty($password)) {
        echo "Veuillez fournir une adresse e-mail valide et un mot de passe.";
        exit;
    }

    try {
        $pdo = Connexion::PDO();
        $stmt = $pdo->prepare("SELECT * FROM utilisateur WHERE user_mail = :email");
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            echo "Adresse e-mail incorrecte.";
        } elseif ($password == $user['user_mdp']) {
            // Correct login
            echo "Connexion réussie. Bienvenue, " . htmlspecialchars($user['user_prenom']) . "!";
        } else {
            // Incorrect password
            echo "Mot de passe incorrect.";
        }
    } catch (Exception $e) {
        echo "Erreur : " . $e->getMessage();
    }
}
?>
