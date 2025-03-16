<?php
require_once(__DIR__ . "/../../config/connexion.php");
require_once(__DIR__ . "/../../modele/reaction.php");

Connexion::connect();

$requestMethod = $_SERVER["REQUEST_METHOD"];

switch ($requestMethod) {
    case 'GET':
        if (isset($_GET['id'])) {
            $reaction = Reaction::getReactionById($_GET['id']);
            echo json_encode($reaction);
        } else {
            $reactions = Reaction::getAllReactions();
            echo json_encode($reactions);
        }
        break;
    case 'POST':
        $data = json_decode(file_get_contents("php://input"), true);
        if (isset($data['reac_type'], $data['user_id'])) {
            $reaction = new Reaction(null, $data['reac_type'], $data['reac_img'] ?? null, $data['prop_id'] ?? null, $data['com_id'] ?? null, $data['user_id']);
            Reaction::createReaction($reaction);
            echo json_encode(["message" => "Reaction créée avec succès"]);
        } else {
            echo json_encode(["message" => "Données manquantes"]);
        }
        break;
    case 'PUT':
        $data = json_decode(file_get_contents("php://input"), true);
        if (isset($data['reac_id'], $data['reac_type'], $data['user_id'])) {
            $reaction = Reaction::getReactionById($data['reac_id']);
            $reaction->set('reac_type', $data['reac_type']);
            $reaction->set('reac_img', $data['reac_img'] ?? null);
            $reaction->set('prop_id', $data['prop_id'] ?? null);
            $reaction->set('com_id', $data['com_id'] ?? null);
            $reaction->set('user_id', $data['user_id']);
            Reaction::updateReaction($reaction);
            echo json_encode(["message" => "Reaction mise à jour avec succès"]);
        } else {
            echo json_encode(["message" => "Données manquantes"]);
        }
        break;
    case 'DELETE':
        if (isset($_GET['id'])) {
            Reaction::deleteReaction($_GET['id']);
            echo json_encode(["message" => "Reaction supprimée avec succès"]);
        } else {
            echo json_encode(["message" => "ID reaction manquant"]);
        }
        break;
    default:
        header("HTTP/1.1 405 Method Not Allowed");
        echo json_encode(["message" => "Méthode non autorisée"]);
        break;
}
?>