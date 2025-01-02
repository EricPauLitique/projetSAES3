<?php

require_once(__DIR__ . "/../../config/connexion.php");  
require_once(__DIR__ . "/../../modele/choixVote.php");

Connexion::connect();

$requestMethod = $_SERVER["REQUEST_METHOD"];

switch ($requestMethod) {
    case 'GET':
        if (isset($_GET['user_id']) && isset($_GET['vote_id'])) {
            $choixVote = ChoixVote::getChoixVoteById($_GET['user_id'], $_GET['vote_id']);
            echo json_encode($choixVote);
        } else {
            $choixVotes = ChoixVote::getAllChoixVote();
            echo json_encode($choixVotes);
        }
        break;
    case 'POST':
        $data = json_decode(file_get_contents("php://input"), true);
        ChoixVote::addChoixVote($data['user_id'], $data['vote_id'], $data['choix_user']);
        echo json_encode(["message" => "Choix de vote ajouté avec succès"]);
        break;
    case 'DELETE':
        if (isset($_GET['user_id']) && isset($_GET['vote_id'])) {
            ChoixVote::deleteChoixVote($_GET['user_id'], $_GET['vote_id']);
            echo json_encode(["message" => "Choix de vote supprimé avec succès"]);
        }
        break;
    default:
        header("HTTP/1.1 405 Method Not Allowed");
        echo json_encode(["message" => "Méthode non autorisée"]);
        break;
}
?>