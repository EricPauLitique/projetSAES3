<?php
require_once(__DIR__ . "/../config/connexion.php");
require_once(__DIR__ . "/../modele/utilisateur.php");

session_start(); // Démarre la session

Connexion::connect();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupération et validation des données
    $data = json_decode(file_get_contents("php://input"), true);
    $email = filter_var($data['email'], FILTER_VALIDATE_EMAIL);
    $password = $data['password'];

    if (!$email || empty($password)) {
        echo json_encode(['status' => 'error', 'message' => 'Veuillez fournir une adresse e-mail valide et un mot de passe.']);
        exit;
    }

    try {
        $pdo = Connexion::PDO();

        // Recherche de l'utilisateur par e-mail
        $stmt = $pdo->prepare("SELECT * FROM utilisateur WHERE user_mail = :email");
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && (password_verify($password, $user['user_mdp']) || $password === $user['user_mdp'])) {
            // Connexion réussie
            $_SESSION['prenom'] = $user['user_prenom'];
            $_SESSION['nom'] = $user['user_nom'];
            $_SESSION['id'] = $user['user_id'];
            
            // Réponse JSON pour succès
            echo json_encode(['status' => 'success', 'message' => 'Connexion réussie.']);
        } else {
            // Email ou mot de passe incorrect
            echo json_encode(['status' => 'error', 'message' => 'Email ou mot de passe incorrect.']);
        }
    } catch (Exception $e) {
        error_log("Erreur de connexion : " . $e->getMessage());
        echo json_encode(['status' => 'error', 'message' => 'Une erreur est survenue. Veuillez réessayer plus tard.']);
    }
}
?>
