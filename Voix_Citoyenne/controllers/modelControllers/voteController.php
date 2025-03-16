<?php
require_once(__DIR__ . "/../../config/connexion.php");
require_once(__DIR__ . "/../../modele/vote.php");

Connexion::connect();

$requestMethod = $_SERVER["REQUEST_METHOD"];

switch ($requestMethod) {
    case 'GET':
        if (isset($_GET['id'])) {
            $vote = Vote::getVoteById($_GET['id']);
            echo json_encode($vote);
        } else {
            $votes = Vote::getAllVotes();
            echo json_encode($votes);
        }
        break;
    case 'POST':
        $data = json_decode(file_get_contents("php://input"), true);
        $vote = new Vote(null, $data['vote_type_scrutin'], $data['vote_duree'], $data['vote_valide'], $data['prop_id']);
        Vote::createVote($data['vote_type_scrutin'], $data['vote_duree'], $data['vote_valide'], $data['prop_id']);
        echo json_encode(["message" => "Vote créé avec succès"]);
        break;
    case 'PUT':
        $data = json_decode(file_get_contents("php://input"), true);
        $vote = Vote::getVoteById($data['vote_id']);
        $vote->set('vote_type_scrutin', $data['vote_type_scrutin']);
        $vote->set('vote_duree', $data['vote_duree']);
        $vote->set('vote_valide', $data['vote_valide']);
        $vote->set('prop_id', $data['prop_id']);
        Vote::updateVote($vote);
        echo json_encode(["message" => "Vote mis à jour avec succès"]);
        break;
    case 'DELETE':
        if (isset($_GET['id'])) {
            Vote::deleteVote($_GET['id']);
            echo json_encode(["message" => "Vote supprimé avec succès"]);
        }
        break;
    default:
        header("HTTP/1.1 405 Method Not Allowed");
        echo json_encode(["message" => "Méthode non autorisée"]);
        break;
}
?>