<?php
require_once(__DIR__ . "/../../config/connexion.php");
require_once(__DIR__ . "/../../modele/proposition.php");

Connexion::connect();

$requestMethod = $_SERVER["REQUEST_METHOD"];

switch ($requestMethod) {
    case 'GET':
        if (isset($_GET['id'])) {
            $proposition = Proposition::getPropositionById($_GET['id']);
            if ($proposition) {
                echo json_encode($proposition);
            } else {
                echo json_encode(["message" => "Proposition non trouvée"]);
            }
        } else {
            $propositions = Proposition::getAllPropositions();
            echo json_encode($propositions);
        }
        break;
    case 'POST':
        $data = json_decode(file_get_contents("php://input"), true);
        if (isset($data['prop_titre'], $data['prop_desc'], $data['user_id'], $data['theme_id'])) {
            $prop_date_min = $data['prop_date_min'] ?? null;
            $prop_cout = $data['prop_cout'] ?? null;
            $proposition = new Proposition(null, $data['prop_titre'], $data['prop_desc'], $prop_date_min, $data['user_id'], $data['theme_id'], $prop_cout);
            try {
                $prop_id = Proposition::addProposition($data['prop_titre'], $data['prop_desc'], $prop_date_min, $data['user_id'], $data['theme_id'], $prop_cout);
                echo json_encode(["message" => "Proposition créée avec succès", "prop_id" => $prop_id]);
            } catch (Exception $e) {
                echo json_encode(["message" => "Erreur lors de la création de la proposition", "error" => $e->getMessage()]);
            }
        } else {
            echo json_encode(["message" => "Données manquantes pour créer la proposition"]);
        }
        break;
    case 'PUT':
        $data = json_decode(file_get_contents("php://input"), true);
        if (isset($data['prop_id'], $data['prop_titre'], $data['prop_desc'], $data['user_id'], $data['theme_id'])) {
            $proposition = Proposition::getPropositionById($data['prop_id']);
            if ($proposition) {
                $proposition->set('prop_titre', $data['prop_titre']);
                $proposition->set('prop_desc', $data['prop_desc']);
                $proposition->set('prop_date_min', $data['prop_date_min'] ?? null);
                $proposition->set('user_id', $data['user_id']);
                $proposition->set('theme_id', $data['theme_id']);
                $proposition->set('prop_cout', $data['prop_cout'] ?? null);
                try {
                    Proposition::updateProposition($proposition);
                    echo json_encode(["message" => "Proposition mise à jour avec succès"]);
                } catch (Exception $e) {
                    echo json_encode(["message" => "Erreur lors de la mise à jour de la proposition", "error" => $e->getMessage()]);
                }
            } else {
                echo json_encode(["message" => "Proposition non trouvée"]);
            }
        } else {
            echo json_encode(["message" => "Données manquantes pour mettre à jour la proposition"]);
        }
        break;
    case 'DELETE':
        if (isset($_GET['id'])) {
            try {
                Proposition::deleteProposition($_GET['id']);
                echo json_encode(["message" => "Proposition supprimée avec succès"]);
            } catch (Exception $e) {
                echo json_encode(["message" => "Erreur lors de la suppression de la proposition", "error" => $e->getMessage()]);
            }
        } else {
            echo json_encode(["message" => "ID de la proposition manquant"]);
        }
        break;
    default:
        header("HTTP/1.1 405 Method Not Allowed");
        echo json_encode(["message" => "Méthode non autorisée"]);
        break;
}
?>