<?php
ob_start(); // Démarre la temporisation de sortie

define('BASE_PATH', __DIR__);

require_once(BASE_PATH . "/config/connexion.php");
require_once(BASE_PATH . "/modele/commentaire.php");

Connexion::connect();

$requestMethod = $_SERVER["REQUEST_METHOD"];
$endpoint = $_GET['endpoint'] ?? '';

if ($endpoint === 'commentaires' && $requestMethod === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    error_log("Données reçues : " . print_r($input, true)); // Debugging line
    if (isset($input['com_txt'], $input['prop_id'], $input['user_id'])) {
        $com_txt = htmlspecialchars($input['com_txt']);
        $prop_id = intval($input['prop_id']);
        $user_id = intval($input['user_id']);
        try {
            $com_id = Commentaire::addCommentaire($com_txt, $user_id, $prop_id);
            echo json_encode(["status" => "success", "message" => "Commentaire créé avec succès", "com_id" => $com_id]);
        } catch (Exception $e) {
            echo json_encode(["status" => "error", "message" => "Erreur lors de la création du commentaire", "error" => $e->getMessage()]);
        }
    } else {
        echo json_encode(["status" => "error", "message" => "Données manquantes pour créer le commentaire"]);
    }
    exit;
}

switch ($endpoint) {
    case 'votes':
        require_once(BASE_PATH . "/controllers/modelControllers/voteController.php");
        break;
    case 'connexion':
        require_once(BASE_PATH . "/controllers/controleurconnexion.php");
        break;
    case 'creacompte':
        require_once(BASE_PATH . "/controllers/controleurCreaCompte.php");
        break;
    case 'supprimercompte':
        require_once(BASE_PATH . "/controllers/controleurSupprimerCompte.php");
        break;
    case 'modifcompte':
        require_once(BASE_PATH . "/controllers/controleurModifCompte.php");
        break;
    case 'logout':
        require_once(BASE_PATH . "/controllers/logout.php");
        break;
    case 'creagroupe':
        require_once(BASE_PATH . "/controllers/controleurCreaGroupe.php");
        break;
    case 'supprimergroupe':
        require_once(BASE_PATH . "/controllers/controleurSuppGroupe.php");
        break;
    case 'modifgroupe':
        require_once(BASE_PATH . "/controllers/controleurmodifGroupe.php");
        break;
    case 'modifgroupeModif':
        require_once(BASE_PATH . "/controllers/controleurmodifGroupeModif.php");
        break;
    case 'accueil':
        require_once(BASE_PATH . "/controllers/controleurAccueil.php");
        break;
    case 'creatheme':
        require_once(BASE_PATH . "/controllers/controleurCreaTheme.php");
        break;
    case 'groupes':
        require_once(BASE_PATH . "/controllers/controleurGroupes.php");
        break;
    case 'themes':
        require_once(BASE_PATH . "/controllers/controleurThemes.php");
        break;
    case 'membres':
        require_once(BASE_PATH . "/controllers/modelControllers/membreController.php");
        break;
    case 'utilisateurs':
        require_once(BASE_PATH . "/controllers/utilisateurController.php");
        break;    
    case 'inviteUser':
        require_once(BASE_PATH . "/controllers/controleurInviteUser.php");
        break;
    case 'propositions':
        require_once(BASE_PATH . "/controllers/modelControllers/propositionController.php");
        break;
    case 'quitterGroupe':
        require_once(BASE_PATH . "/controllers/controleurQuitterGroupe.php");
        break;
    case 'commentaires':
        require_once(BASE_PATH . "/controllers/modelControllers/commentaireController.php");
        break;
    default:
        header("HTTP/1.1 404 Not Found");
        echo json_encode(["message" => "Endpoint non trouvé"]);
        break;
}

ob_end_flush(); // Envoie la sortie tamponnée au navigateur
?>