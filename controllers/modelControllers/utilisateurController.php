<?php

require_once(__DIR__ . "/../../config/connexion.php");  
require_once(__DIR__ . "/../../modele/utilisateur.php");
Connexion::connect();

$requestMethod = $_SERVER["REQUEST_METHOD"];

switch ($requestMethod) {
    case 'GET':
        if (isset($_GET['id'])) {
            $utilisateur = Utilisateur::getUtilisateurByLogin($_GET['id']);
            echo json_encode($utilisateur);
        } else {
            $utilisateurs = Utilisateur::getAllUtilisateur();
            echo json_encode($utilisateurs);
        }
        break;
    case 'POST':
        $data = json_decode(file_get_contents("php://input"), true);
        if (isset($data['user_mail'], $data['user_mdp'], $data['user_prenom'], $data['user_nom'], $data['adr_id'])) {
            $utilisateur = new Utilisateur(null, $data['user_mail'], password_hash($data['user_mdp'], PASSWORD_DEFAULT), $data['user_prenom'], $data['user_nom'], $data['adr_id']);
            Utilisateur::createUtilisateur($utilisateur);
            echo json_encode(["message" => "Utilisateur créé avec succès"]);
        } else {
            echo json_encode(["message" => "Données manquantes"]);
        }
        break;
    case 'PUT':
        $data = json_decode(file_get_contents("php://input"), true);
        if (isset($data['user_id'], $data['user_mail'], $data['user_prenom'], $data['user_nom'])) {
            $utilisateur = Utilisateur::getUtilisateurByLogin($data['user_id']);
            $utilisateur->set('user_mail', $data['user_mail']);
            $utilisateur->set('user_prenom', $data['user_prenom']);
            $utilisateur->set('user_nom', $data['user_nom']);
            Utilisateur::updateUtilisateur($utilisateur);
            echo json_encode(["message" => "Utilisateur mis à jour avec succès"]);
        } else {
            echo json_encode(["message" => "Données manquantes"]);
        }
        break;
    case 'DELETE':
        if (isset($_GET['id'])) {
            Utilisateur::deleteUtilisateur($_GET['id']);
            echo json_encode(["message" => "Utilisateur supprimé avec succès"]);
        } else {
            echo json_encode(["message" => "ID utilisateur manquant"]);
        }
        break;
    default:
        header("HTTP/1.1 405 Method Not Allowed");
        echo json_encode(["message" => "Méthode non autorisée"]);
        break;
}
?>