<?php
require_once(__DIR__ . "/../config/connexion.php");
require_once(__DIR__ . "/../modele/utilisateur.php");
require_once(__DIR__ . "/../modele/adresse.php");

Connexion::connect();
$pdo = Connexion::PDO();

session_start();
error_log("Session démarrée : " . print_r($_SESSION, true));

// Vérification si l'utilisateur est connecté
if (!isset($_SESSION['id'])) {
    error_log("Utilisateur non connecté.");
    echo json_encode(['status' => 'error', 'message' => 'Utilisateur non connecté.']);
    exit;
}

$idUtilisateur = htmlspecialchars($_SESSION['id']);

try {
    // Récupérer les données du formulaire
    $data = json_decode(file_get_contents('php://input'), true);
    $prenom = htmlspecialchars($data['prenom']);
    $nom = htmlspecialchars($data['nom']);
    $email = htmlspecialchars($data['email']);
    $ancienPassword = $data['ancien_password'];
    $nouveauPassword = $data['nouveau_password'];
    $confirmerPassword = $data['confirmer_password'];
    $codePostal = htmlspecialchars($data['code_postal']);
    $ville = htmlspecialchars($data['ville']);
    $numeroRue = htmlspecialchars($data['numero_rue']);
    $nomRue = htmlspecialchars($data['nom_rue']);

    // Vérification de l'existence de l'email
    if (Utilisateur::emailExists($email, $idUtilisateur)) {
        echo json_encode(['status' => 'error', 'message' => "L'email existe déjà dans notre système."]);
        exit;
    }

    // Vérification de l'existence de prénom + nom
    if (Utilisateur::prenomNomExists($prenom, $nom, $idUtilisateur)) {
        echo json_encode(['status' => 'error', 'message' => "Un utilisateur avec le même prénom et nom existe déjà."]);
        exit;
    }

    // Vérification de l'ancien mot de passe
    $utilisateur = Utilisateur::getUtilisateurById($idUtilisateur);
    if (!password_verify($ancienPassword, $utilisateur->get('user_mdp'))) {
        echo json_encode(['status' => 'error', 'message' => "L'ancien mot de passe est incorrect."]);
        exit;
    }

    // Mettre à jour les informations de l'utilisateur
    $utilisateur->set('user_prenom', $prenom);
    $utilisateur->set('user_nom', $nom);
    $utilisateur->set('user_mail', $email);

    // Mettre à jour le mot de passe si un nouveau mot de passe est fourni
    if (!empty($nouveauPassword)) {
        if ($nouveauPassword !== $confirmerPassword) {
            echo json_encode(['status' => 'error', 'message' => "Le nouveau mot de passe et la confirmation ne correspondent pas."]);
            exit;
        }
        $passwordHashed = password_hash($nouveauPassword, PASSWORD_DEFAULT);
        $utilisateur->set('user_mdp', $passwordHashed);
    }

    Utilisateur::updateUtilisateur($utilisateur);

    // Mettre à jour l'adresse de l'utilisateur
    $adresse = Adresse::getAdresseById($utilisateur->get('adr_id'));
    $adresse->set('adr_cp', $codePostal);
    $adresse->set('adr_ville', $ville);
    $adresse->set('adr_num', $numeroRue);
    $adresse->set('adr_rue', $nomRue);
    Adresse::updateAdresse($adresse);

    echo json_encode(['status' => 'success', 'message' => 'Votre compte a été modifié avec succès.']);
    exit();

} catch (Exception $e) {
    error_log("Erreur : " . $e->getMessage());
    echo json_encode(['status' => 'error', 'message' => 'Erreur : ' . $e->getMessage()]);
    exit();
}
?>