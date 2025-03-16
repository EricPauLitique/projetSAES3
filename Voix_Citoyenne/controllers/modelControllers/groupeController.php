<?php
require_once(__DIR__ . "/../../config/connexion.php");
require_once(__DIR__ . "/../../modele/groupe.php");

Connexion::connect();

$requestMethod = $_SERVER["REQUEST_METHOD"];

switch ($requestMethod) {
    case 'GET':
        if (isset($_GET['id'])) {
            $groupe = Groupe::getGroupByIdUnique($_GET['id']);
            echo json_encode($groupe);
        } else {
            $groupes = Groupe::getAllGroupes();
            echo json_encode($groupes);
        }
        break;
    case 'POST':
        $data = json_decode(file_get_contents("php://input"), true);
        $groupe = new Groupe(null, $data['grp_nom'], $data['grp_couleur'], $data['grp_img'], $data['grp_lim_an'], $data['user_id']);
        Groupe::createGroupe($groupe);
        echo json_encode(["message" => "Groupe créé avec succès"]);
        break;
    case 'PUT':
        $data = json_decode(file_get_contents("php://input"), true);
        $groupe = Groupe::getGroupByIdUnique($data['grp_id']);
        $groupe->set('grp_nom', $data['grp_nom']);
        $groupe->set('grp_couleur', $data['grp_couleur']);
        $groupe->set('grp_img', $data['grp_img']);
        $groupe->set('grp_lim_an', $data['grp_lim_an']);
        Groupe::updateGroupe($groupe);
        echo json_encode(["message" => "Groupe mis à jour avec succès"]);
        break;
    case 'DELETE':
        if (isset($_GET['id'])) {
            Groupe::deleteGroupe($_GET['id']);
            echo json_encode(["message" => "Groupe supprimé avec succès"]);
        }
        break;
    default:
        header("HTTP/1.1 405 Method Not Allowed");
        echo json_encode(["message" => "Méthode non autorisée"]);
        break;
}
?>