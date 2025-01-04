<?php
session_start();
require_once(__DIR__ . "/../config/connexion.php");
require_once(__DIR__ . "/../modele/theme.php");
require_once(__DIR__ . "/../modele/comporte.php");

Connexion::connect();

$requestMethod = $_SERVER['REQUEST_METHOD'];

switch ($requestMethod) {
    case 'GET':
        if (isset($_GET['id'])) {
            $themeId = intval($_GET['id']);
            $theme = Theme::getThemeById($themeId);
            if ($theme) {
                echo json_encode(['status' => 'success', 'data' => $theme]);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Thème non trouvé.']);
            }
        } else {
            $themes = Theme::getAllThemes();
            echo json_encode(['status' => 'success', 'data' => $themes]);
        }
        break;

    case 'POST':
        $data = json_decode(file_get_contents("php://input"), true);
        $nom_du_theme = ucfirst(strtolower(htmlspecialchars($data['theme_nom'])));
        $limite_theme = intval($data['limite_theme']);
        $group_id = intval($data['group_id']);
        $limite_grp = intval($data['limite_grp']);

        // Vérifier si le thème existe déjà dans la base de données
        $existingTheme = Theme::getThemeByName($nom_du_theme);
        $incrementID = Theme::getMaxTheme() + 1;

        if ($limite_theme + Comporte::getSumLimiteThemeByGroupId($group_id) > $limite_grp) {
            echo json_encode(['status' => 'error', 'message' => 'La limite annuelle des thèmes ne doit pas dépasser la limite annuelle du groupe.']);
            exit;
        }

        if ($existingTheme === false) {
            // Ajouter le nouveau thème
            $newThemeId = Theme::createTheme($incrementID, $nom_du_theme);
            $themeId = $incrementID;
        } else {
            $themeId = $existingTheme->get('theme_id');
        }

        // Vérifier si l'association existe déjà
        if (!Comporte::existsThemeInGroup($group_id, $themeId)) {
            // Ajouter le thème au groupe dans la table comporte
            $addSuccess = Comporte::addThemeToGroup($group_id, $themeId, $limite_theme);

            if ($addSuccess) {
                echo json_encode(['status' => 'success', 'message' => 'Le thème a été ajouté avec succès.']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Erreur lors de l\'ajout du thème.']);
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Le thème existe déjà dans ce groupe. Veuillez modifier le prix.']);
        }
        break;

    case 'PUT':
        $data = json_decode(file_get_contents("php://input"), true);
        $themeId = intval($data['theme_id']);
        $theme = Theme::getThemeById($themeId);
        if ($theme) {
            $theme->set('theme_nom', $data['theme_nom']);
            $result = Theme::updateTheme($themeId, $data['theme_nom']);
            if ($result) {
                echo json_encode(['status' => 'success', 'message' => 'Thème mis à jour avec succès.']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Erreur lors de la mise à jour du thème.']);
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Thème non trouvé.']);
        }
        break;

    case 'DELETE':
        if (isset($_GET['id'])) {
            $themeId = intval($_GET['id']);
            $result = Theme::deleteThemeById($themeId);
            if ($result) {
                echo json_encode(['status' => 'success', 'message' => 'Thème supprimé avec succès.']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Erreur lors de la suppression du thème.']);
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'ID du thème manquant.']);
        }
        break;

    default:
        header("HTTP/1.1 405 Method Not Allowed");
        echo json_encode(['status' => 'error', 'message' => 'Méthode non autorisée.']);
        break;
}
?>