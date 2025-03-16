<?php
session_start();
require_once(__DIR__ . "/../config/connexion.php");
require_once(__DIR__ . "/../modele/theme.php");
require_once(__DIR__ . "/../modele/comporte.php");
require_once(__DIR__ . "/../modele/groupe.php");

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

        // Vérifier si le groupe existe
        $groupe = Groupe::getGroupByIdUnique2($group_id);
        if (!$groupe) {
            echo json_encode(['status' => 'error', 'message' => 'Groupe non trouvé.']);
            exit;
        }
        $limite_grp = $groupe->get('grp_lim_an');

        // Vérifier si le thème existe déjà dans la base de données
        $existingTheme = Theme::getThemeByName($nom_du_theme);
        $incrementID = Theme::getMaxTheme() + 1;

        $currentSumLimiteTheme = Comporte::getSumLimiteThemeByGroupId($group_id);
        if ($limite_theme + intval($currentSumLimiteTheme) > $limite_grp) {
            echo json_encode(['status' => 'error', 'message' => 'La limite annuelle des thèmes ne doit pas dépasser la limite annuelle du groupe. Limite actuelle des thèmes : ' . $currentSumLimiteTheme . '€, Limite du groupe : ' . $limite_grp . '€']);
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
        $group_id = intval($data['group_id']);

        // Vérifier si le groupe existe
        $groupe = Groupe::getGroupByIdUnique($group_id);
        if (!$groupe) {
            echo json_encode(['status' => 'error', 'message' => 'Groupe non trouvé.']);
            exit;
        }

        $groupe2 = Groupe::getGroupByIdUnique2($group_id);
        if (!$groupe) {
            echo json_encode(['status' => 'error', 'message' => 'Groupe non trouvé.']);
            exit;
        }
        
        $limite_grp = $groupe2->get('grp_lim_an');
        // Vérifier si le nom du thème existe déjà
        $existingTheme = Theme::getThemeByName($data['theme_nom']);
        if ($existingTheme) {
            $themeId = $existingTheme->get('theme_id');
        }

        $prixTheme = Comporte::getComporteById($group_id, $themeId);
        $theme = Theme::getThemeById($themeId);
        if ($theme) {
            $updateSuccess = true;

            if (isset($data['theme_nom'])) {
                $theme->set('theme_nom', $data['theme_nom']);
                $result = Theme::updateTheme($themeId, $data['theme_nom']);
                if (!$result) {
                    $updateSuccess = false;
                }
            }

            if (isset($data['limite_theme'])) {
                // Vérifier si la nouvelle limite ne dépasse pas la limite annuelle du groupe
                $currentSumLimiteTheme = Comporte::getSumLimiteThemeByGroupId($group_id);
                $newSumLimiteTheme = $currentSumLimiteTheme - $prixTheme->get('lim_theme') + intval($data['limite_theme']);
                if ($newSumLimiteTheme > $limite_grp) {
                    echo json_encode(['status' => 'error', 'message' => 'La limite annuelle des thèmes ne doit pas dépasser la limite annuelle du groupe. Limite actuelle des thèmes : ' . $currentSumLimiteTheme . '€, Limite du groupe : ' . $limite_grp . '€']);
                    exit;
                }
                $updateComporte = Comporte::updateThemeLimitOnly($group_id, $themeId, intval($data['limite_theme']));
                if (!$updateComporte) {
                    $updateSuccess = false;
                }
 
            }
        
        if (!$result||!$updateComporte) {
            $updateSuccess = true;
        }

            if ($updateSuccess) {
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