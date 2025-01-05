<?php
ob_start(); // Démarre la temporisation de sortie

define('BASE_PATH', __DIR__);

require_once(BASE_PATH . "/config/connexion.php");

Connexion::connect();

$requestMethod = $_SERVER["REQUEST_METHOD"];
$endpoint = $_GET['endpoint'] ?? '';

switch ($endpoint) {
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
    default:
        header("HTTP/1.1 404 Not Found");
        echo json_encode(["message" => "Endpoint non trouvé"]);
        break;
}

ob_end_flush(); // Envoie la sortie tamponnée au navigateur
?>