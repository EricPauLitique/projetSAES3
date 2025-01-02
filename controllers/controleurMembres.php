<?php
session_start();
require_once(__DIR__ . "/../config/connexion.php");
require_once(__DIR__ . "/../modele/membre.php");

Connexion::connect();

$requestMethod = $_SERVER['REQUEST_METHOD'];

switch ($requestMethod) {
    case 'GET':
        if (isset($_GET['user_id']) && isset($_GET['grp_id'])) {
            $userId = intval($_GET['user_id']);
            $grpId = intval($_GET['grp_id']);
            $membre = Membre::getMembreByIds($userId, $grpId);
            if ($membre) {
                echo json_encode(['status' => 'success', 'data' => $membre]);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Membre non trouvé.']);
            }
        } else {
            $membres = Membre::getAllMembres();
            echo json_encode(['status' => 'success', 'data' => $membres]);
        }
        break;

    case 'POST':
        $data = json_decode(file_get_contents("php://input"), true);
        $result = Membre::addMembre($data);
        if ($result) {
            echo json_encode(['status' => 'success', 'message' => 'Membre ajouté avec succès.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Erreur lors de l\'ajout du membre.']);
        }
        break;

    case 'DELETE':
        if (isset($_GET['user_id']) && isset($_GET['grp_id'])) {
            $userId = intval($_GET['user_id']);
            $grpId = intval($_GET['grp_id']);
            $result = Membre::deleteMembre($userId, $grpId);
            if ($result) {
                echo json_encode(['status' => 'success', 'message' => 'Membre supprimé avec succès.']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Erreur lors de la suppression du membre.']);
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'ID utilisateur ou groupe manquant.']);
        }
        break;

    default:
        header("HTTP/1.1 405 Method Not Allowed");
        echo json_encode(['status' => 'error', 'message' => 'Méthode non autorisée.']);
        break;
}
?>