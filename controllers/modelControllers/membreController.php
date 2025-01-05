<?php
require_once(__DIR__ . "/../../config/connexion.php");  
require_once(__DIR__ . "/../../modele/membre.php");
Connexion::connect();

$requestMethod = $_SERVER["REQUEST_METHOD"];

switch ($requestMethod) {
    case 'GET':
        if (isset($_GET['user_id']) && isset($_GET['grp_id'])) {
            $membre = Membre::getMembreByIds($_GET['user_id'], $_GET['grp_id']);
            echo json_encode($membre);
        } else {
            $membres = Membre::getAllMembres();
            echo json_encode($membres);
        }
        break;
    case 'POST':
        $data = json_decode(file_get_contents("php://input"), true);
        Membre::addMembre($data);
        echo json_encode(["message" => "Membre ajouté avec succès"]);
        break;
    case 'PUT':
        $data = json_decode(file_get_contents("php://input"), true);
        Membre::updateMembre($data);
        echo json_encode(["message" => "Membre mis à jour avec succès"]);
        break;
    case 'DELETE':
        if (isset($_GET['user_id']) && isset($_GET['grp_id'])) {
            Membre::deleteMembre($_GET['user_id'], $_GET['grp_id']);
            echo json_encode(["message" => "Membre supprimé avec succès"]);
        }
        break;
    default:
        header("HTTP/1.1 405 Method Not Allowed");
        echo json_encode(["message" => "Méthode non autorisée"]);
        break;
}
?>