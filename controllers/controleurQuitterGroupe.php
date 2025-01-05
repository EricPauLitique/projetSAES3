<?php
require_once(__DIR__ . "/../config/connexion.php");
require_once(__DIR__ . "/../modele/membre.php");
require_once(__DIR__ . "/../modele/groupe.php");

Connexion::connect();

$requestMethod = $_SERVER["REQUEST_METHOD"];

if ($requestMethod === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);

    // Ajoutez des messages de débogage
    error_log("Données reçues : " . print_r($data, true));

    if (isset($data['user_id']) && isset($data['grp_id'])) {
        $userId = intval($data['user_id']);
        $grpId = intval($data['grp_id']);

        // Récupérer le nom du groupe
        $groupe = Groupe::getGroupByIdUnique($grpId);
        $nomGroupe = $groupe['grp_nom'];

        // Suppression du membre de la base de données
        try {
            $result = Membre::deleteMembre($userId, $grpId);

            if ($result) {
                echo json_encode(['status' => 'success', 'message' => 'Vous avez quitté le groupe "' . htmlspecialchars($nomGroupe) . '" avec succès.']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Erreur lors de la tentative de quitter le groupe "' . htmlspecialchars($nomGroupe) . '".']);
            }
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => 'Erreur : ' . $e->getMessage()]);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'ID utilisateur ou groupe manquant.']);
    }
} else {
    header("HTTP/1.1 405 Method Not Allowed");
    echo json_encode(['message' => 'Méthode non autorisée']);
}
?>