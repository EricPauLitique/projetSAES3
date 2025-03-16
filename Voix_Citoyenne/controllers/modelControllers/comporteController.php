<?php

require_once(__DIR__ . "/../../config/connexion.php");  
require_once(__DIR__ . "/../../modele/comporte.php");

Connexion::connect();

$requestMethod = $_SERVER["REQUEST_METHOD"];

switch ($requestMethod) {
    case 'GET':
        if (isset($_GET['grp_id']) && isset($_GET['theme_id'])) {
            $comporte = Comporte::getComporteById($_GET['grp_id'], $_GET['theme_id']);
            echo json_encode($comporte);
        } else {
            $comportes = Comporte::getAllComporte();
            echo json_encode($comportes);
        }
        break;
    case 'POST':
        $data = json_decode(file_get_contents("php://input"), true);
        Comporte::addThemeToGroup($data['grp_id'], $data['theme_id'], $data['lim_theme']);
        echo json_encode(["message" => "Thème ajouté au groupe avec succès"]);
        break;
    case 'PUT':
        $data = json_decode(file_get_contents("php://input"), true);
        Comporte::updateTheme($data['theme_id'], $data['grp_id'], $data['lim_theme']);
        echo json_encode(["message" => "Thème mis à jour avec succès"]);
        break;
    case 'DELETE':
        if (isset($_GET['grp_id']) && isset($_GET['theme_id'])) {
            Comporte::deleteThemeGrp($_GET['theme_id'], $_GET['grp_id']);
            echo json_encode(["message" => "Thème supprimé du groupe avec succès"]);
        }
        break;
    default:
        header("HTTP/1.1 405 Method Not Allowed");
        echo json_encode(["message" => "Méthode non autorisée"]);
        break;
}
?>