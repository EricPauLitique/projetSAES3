<?php
session_start();
require_once(__DIR__ . "/../config/connexion.php");
require_once(__DIR__ . "/../modele/groupe.php");

Connexion::connect();

$requestMethod = $_SERVER['REQUEST_METHOD'];

switch ($requestMethod) {
    case 'GET':
        if (isset($_GET['id'])) {
            $groupId = intval($_GET['id']);
            $group = Groupe::getGroupByIdUnique($groupId);
            if ($group) {
                echo json_encode(['status' => 'success', 'data' => $group]);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Groupe non trouvé.']);
            }
        } else {
            $groups = Groupe::getAllGroupes();
            echo json_encode(['status' => 'success', 'data' => $groups]);
        }
        break;

    case 'POST':
        $data = json_decode(file_get_contents("php://input"), true);
        $group = new Groupe(
            null,
            $data['grp_nom'],
            $data['grp_couleur'],
            $data['grp_img'],
            $data['grp_lim_an'],
            $data['user_id']
        );
        $result = Groupe::addGroupe($group);
        if ($result) {
            echo json_encode(['status' => 'success', 'message' => 'Groupe créé avec succès.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Erreur lors de la création du groupe.']);
        }
        break;

    case 'PUT':
        $data = json_decode(file_get_contents("php://input"), true);
        $groupId = intval($data['grp_id']);
        $group = Groupe::getGroupByIdUnique2($groupId);
        if ($group) {
            $group->set('grp_nom', $data['grp_nom']);
            $group->set('grp_couleur', $data['grp_couleur']);
            $group->set('grp_img', $data['grp_img']);
            $group->set('grp_lim_an', $data['grp_lim_an']);
            $result = Groupe::updateGroup($groupId, $data['grp_nom'], $data['grp_couleur'], $data['grp_lim_an'], $data['grp_img']);
            if ($result) {
                echo json_encode(['status' => 'success', 'message' => 'Groupe mis à jour avec succès.']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Erreur lors de la mise à jour du groupe.']);
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Groupe non trouvé.']);
        }
        break;

    case 'DELETE':
        if (isset($_GET['id'])) {
            $groupId = intval($_GET['id']);
            $result = Groupe::deleteGroupById($groupId);
            if ($result) {
                echo json_encode(['status' => 'success', 'message' => 'Groupe supprimé avec succès.']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Erreur lors de la suppression du groupe.']);
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'ID du groupe manquant.']);
        }
        break;

    default:
        header("HTTP/1.1 405 Method Not Allowed");
        echo json_encode(['status' => 'error', 'message' => 'Méthode non autorisée.']);
        break;
}
?>