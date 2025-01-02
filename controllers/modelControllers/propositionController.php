<?php
require_once(__DIR__ . "/../../config/connexion.php");
require_once(__DIR__ . "/../../modele/proposition.php");

Connexion::connect();

$requestMethod = $_SERVER["REQUEST_METHOD"];

switch ($requestMethod) {
    case 'GET':
        if (isset($_GET['id'])) {
            $proposition = Proposition::getPropositionById($_GET['id']);
            echo json_encode($proposition);
        } else {
            $propositions = Proposition::getAllPropositions();
            echo json_encode($propositions);
        }
        break;
    case 'POST':
        $data = json_decode(file_get_contents("php://input"), true);
        $proposition = new Proposition(null, $data['prop_titre'], $data['prop_desc'], $data['prop_date_min'], $data['user_id'], $data['theme_id'], $data['prop_cout']);
        Proposition::createProposition($proposition);
        echo json_encode(["message" => "Proposition créée avec succès"]);
        break;
    case 'PUT':
        $data = json_decode(file_get_contents("php://input"), true);
        $proposition = Proposition::getPropositionById($data['prop_id']);
        $proposition->set('prop_titre', $data['prop_titre']);
        $proposition->set('prop_desc', $data['prop_desc']);
        $proposition->set('prop_date_min', $data['prop_date_min']);
        $proposition->set('user_id', $data['user_id']);
        $proposition->set('theme_id', $data['theme_id']);
        $proposition->set('prop_cout', $data['prop_cout']);
        Proposition::updateProposition($proposition);
        echo json_encode(["message" => "Proposition mise à jour avec succès"]);
        break;
    case 'DELETE':
        if (isset($_GET['id'])) {
            Proposition::deleteProposition($_GET['id']);
            echo json_encode(["message" => "Proposition supprimée avec succès"]);
        }
        break;
    default:
        header("HTTP/1.1 405 Method Not Allowed");
        echo json_encode(["message" => "Méthode non autorisée"]);
        break;
}
?>