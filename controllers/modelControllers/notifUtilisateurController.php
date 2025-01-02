<?php
require_once(__DIR__ . "/../../config/connexion.php");  
require_once(__DIR__ . "/../../modele/notifUtilisateur.php");

Connexion::connect();

$requestMethod = $_SERVER["REQUEST_METHOD"];

switch ($requestMethod) {
    case 'GET':
        if (isset($_GET['id'])) {
            $notifUtilisateur = NotifUtilisateur::getNotificationsByUserId($_GET['id']);
            echo json_encode($notifUtilisateur);
        } else {
            $notifUtilisateurs = NotifUtilisateur::getAllNotifUtilisateurs();
            echo json_encode($notifUtilisateurs);
        }
        break;
    case 'POST':
        $data = json_decode(file_get_contents("php://input"), true);
        NotifUtilisateur::addNotifUtilisateur($data['user_id'], $data['notif_id']);
        echo json_encode(["message" => "Notification utilisateur ajoutée avec succès"]);
        break;
    case 'DELETE':
        if (isset($_GET['user_id']) && isset($_GET['notif_id'])) {
            NotifUtilisateur::deleteNotifUtilisateur($_GET['user_id'], $_GET['notif_id']);
            echo json_encode(["message" => "Notification utilisateur supprimée avec succès"]);
        }
        break;
    default:
        header("HTTP/1.1 405 Method Not Allowed");
        echo json_encode(["message" => "Méthode non autorisée"]);
        break;
}
?>