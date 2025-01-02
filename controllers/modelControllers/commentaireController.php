<?php
require_once(__DIR__ . "/../../config/connexion.php");  
require_once(__DIR__ . "/../../modele/commentaire.php");

Connexion::connect();

$requestMethod = $_SERVER["REQUEST_METHOD"];

switch ($requestMethod) {
    case 'GET':
        if (isset($_GET['id'])) {
            $commentaire = Commentaire::getCommentaireById($_GET['id']);
            echo json_encode($commentaire);
        } else {
            $commentaires = Commentaire::getAllCommentaires();
            echo json_encode($commentaires);
        }
        break;
    case 'POST':
        $data = json_decode(file_get_contents("php://input"), true);
        $commentaire = new Commentaire(null, $data['com_txt']);
        Commentaire::createCommentaire($commentaire);
        echo json_encode(["message" => "Commentaire créé avec succès"]);
        break;
    case 'PUT':
        $data = json_decode(file_get_contents("php://input"), true);
        $commentaire = Commentaire::getCommentaireById($data['com_id']);
        $commentaire->set('com_txt', $data['com_txt']);
        Commentaire::updateCommentaire($commentaire);
        echo json_encode(["message" => "Commentaire mis à jour avec succès"]);
        break;
    case 'DELETE':
        if (isset($_GET['id'])) {
            Commentaire::deleteCommentaire($_GET['id']);
            echo json_encode(["message" => "Commentaire supprimé avec succès"]);
        }
        break;
    default:
        header("HTTP/1.1 405 Method Not Allowed");
        echo json_encode(["message" => "Méthode non autorisée"]);
        break;
}
?>