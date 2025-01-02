<?php
require_once(__DIR__ . "/../../config/connexion.php");
require_once(__DIR__ . "/../../modele/theme.php");

Connexion::connect();

$requestMethod = $_SERVER["REQUEST_METHOD"];

switch ($requestMethod) {
    case 'GET':
        if (isset($_GET['id'])) {
            $theme = Theme::getThemeById($_GET['id']);
            echo json_encode($theme);
        } else {
            $themes = Theme::getAllThemes();
            echo json_encode($themes);
        }
        break;
    case 'POST':
        $data = json_decode(file_get_contents("php://input"), true);
        $theme = new Theme(null, $data['theme_nom']);
        Theme::createTheme($theme);
        echo json_encode(["message" => "Thème créé avec succès"]);
        break;
    case 'PUT':
        $data = json_decode(file_get_contents("php://input"), true);
        $theme = Theme::getThemeById($data['theme_id']);
        $theme->set('theme_nom', $data['theme_nom']);
        Theme::updateTheme($theme);
        echo json_encode(["message" => "Thème mis à jour avec succès"]);
        break;
    case 'DELETE':
        if (isset($_GET['id'])) {
            Theme::deleteTheme($_GET['id']);
            echo json_encode(["message" => "Thème supprimé avec succès"]);
        }
        break;
    default:
        header("HTTP/1.1 405 Method Not Allowed");
        echo json_encode(["message" => "Méthode non autorisée"]);
        break;
}
?>