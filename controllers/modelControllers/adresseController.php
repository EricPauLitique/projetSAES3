<?php
require_once(__DIR__ . "/../../config/connexion.php");  
require_once(__DIR__ . "/../../modele/adresse.php");
Connexion::connect();

$requestMethod = $_SERVER["REQUEST_METHOD"];

switch ($requestMethod) {
    case 'GET':
        if (isset($_GET['id'])) {
            $adresse = Adresse::getAdresseById($_GET['id']);
            echo json_encode($adresse);
        } else {
            $adresses = Adresse::getAllAdresse();
            echo json_encode($adresses);
        }
        break;
    case 'POST':
        $data = json_decode(file_get_contents("php://input"), true);
        $adresse = new Adresse(null, $data['adr_cp'], $data['adr_ville'], $data['adr_rue'], $data['adr_num']);
        Adresse::createAdresse($adresse);
        echo json_encode(["message" => "Adresse créée avec succès"]);
        break;
    case 'PUT':
        $data = json_decode(file_get_contents("php://input"), true);
        $adresse = Adresse::getAdresseById($data['adr_id']);
        $adresse->set('adr_cp', $data['adr_cp']);
        $adresse->set('adr_ville', $data['adr_ville']);
        $adresse->set('adr_rue', $data['adr_rue']);
        $adresse->set('adr_num', $data['adr_num']);
        Adresse::updateAdresse($adresse);
        echo json_encode(["message" => "Adresse mise à jour avec succès"]);
        break;
    case 'DELETE':
        if (isset($_GET['id'])) {
            Adresse::deleteAdresse($_GET['id']);
            echo json_encode(["message" => "Adresse supprimée avec succès"]);
        }
        break;
    default:
        header("HTTP/1.1 405 Method Not Allowed");
        echo json_encode(["message" => "Méthode non autorisée"]);
        break;
}
?>