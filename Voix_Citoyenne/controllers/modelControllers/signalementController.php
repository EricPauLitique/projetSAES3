<?php

require_once(__DIR__ . "/../../config/connexion.php");  
require_once(__DIR__ . "/../../modele/signalement.php");
Connexion::connect();

$requestMethod = $_SERVER["REQUEST_METHOD"];

switch ($requestMethod) {
    case 'GET':
        if (isset($_GET['id'])) {
            $signalement = Signalement::getSignalementById($_GET['id']);
            echo json_encode($signalement);
        } else {
            $signalements = Signalement::getAllSignalement();
            echo json_encode($signalements);
        }
        break;
    case 'POST':
        $data = json_decode(file_get_contents("php://input"), true);
        $signalement = new Signalement(null, $data['sig_nature'], $data['prop_id'], $data['com_id'], $data['user_id']);
        Signalement::createSignalement($signalement);
        echo json_encode(["message" => "Signalement créé avec succès"]);
        break;
    case 'PUT':
        $data = json_decode(file_get_contents("php://input"), true);
        $signalement = Signalement::getSignalementById($data['sig_id']);
        $signalement->set('sig_nature', $data['sig_nature']);
        $signalement->set('prop_id', $data['prop_id']);
        $signalement->set('com_id', $data['com_id']);
        $signalement->set('user_id', $data['user_id']);
        Signalement::updateSignalement($signalement);
        echo json_encode(["message" => "Signalement mis à jour avec succès"]);
        break;
    case 'DELETE':
        if (isset($_GET['id'])) {
            Signalement::deleteSignalement($_GET['id']);
            echo json_encode(["message" => "Signalement supprimé avec succès"]);
        }
        break;
    default:
        header("HTTP/1.1 405 Method Not Allowed");
        echo json_encode(["message" => "Méthode non autorisée"]);
        break;

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $com_id = $_POST['com_id'];
    $sig_nature = $_POST['sig_nature'];
    $user_id = $_SESSION['user_id']; // Assurez-vous que l'utilisateur est connecté et que son ID est stocké dans la session

    $signalement = new Signalement(null, $sig_nature, null, $com_id, $user_id);
    $signalement->save();

    header('Location: ../vue/discussion.php');
    exit();
}
}
?>