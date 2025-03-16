<?php
require_once(__DIR__ . "/../../config/connexion.php");  
require_once(__DIR__ . "/../../modele/notification.php");
Connexion::connect();

$requestMethod = $_SERVER["REQUEST_METHOD"];

switch ($requestMethod) {
    case 'GET':
        if (isset($_GET['id'])) {
            $notification = Notification::getNotificationById($_GET['id']);
            echo json_encode($notification);
        } else {
            $notifications = Notification::getAllNotifications();
            echo json_encode($notifications);
        }
        break;
    case 'POST':
        $data = json_decode(file_get_contents("php://input"), true);
        Notification::addNotification($data['notif_contenu']);
        echo json_encode(["message" => "Notification créée avec succès"]);
        break;
    case 'DELETE':
        if (isset($_GET['id'])) {
            Notification::deleteNotification($_GET['id']);
            echo json_encode(["message" => "Notification supprimée avec succès"]);
        }
        break;
    default:
        header("HTTP/1.1 405 Method Not Allowed");
        echo json_encode(["message" => "Méthode non autorisée"]);
        break;
}
?>