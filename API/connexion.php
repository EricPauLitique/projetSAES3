<?php
require_once("../config/connexion.php");
require_once("../modele/utilisateur.php");
session_start();

Connexion::connect();

header('Content-Type: application/json'); // Définit le format JSON

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    $password = $_POST['password'];

    if (!$email || empty($password)) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Veuillez fournir une adresse e-mail valide et un mot de passe.',
        ]);
        exit;
    }

    try {
        $pdo = Connexion::PDO();
        $stmt = $pdo->prepare("SELECT * FROM utilisateur WHERE user_mail = :email");
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && (password_verify($password, $user['user_mdp']) || $password == $user['user_mdp'])) {
            $_SESSION['prenom'] = $user['user_prenom'];
            $_SESSION['nom'] = $user['user_nom'];
            $_SESSION['id'] = $user['user_id'];

            echo json_encode([
                'status' => 'success',
                'message' => 'Connexion réussie.',
                'data' => [
                    'prenom' => $user['user_prenom'],
                    'nom' => $user['user_nom'],
                    'id' => $user['user_id'],
                ],
            ]);
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => 'Email ou mot de passe incorrect.',
            ]);
        }
    } catch (Exception $e) {
        error_log("Erreur de connexion : " . $e->getMessage());
        echo json_encode([
            'status' => 'error',
            'message' => 'Une erreur est survenue. Veuillez réessayer plus tard.',
        ]);
    }
    exit;
}