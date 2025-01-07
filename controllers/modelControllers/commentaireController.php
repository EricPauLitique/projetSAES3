<?php
require_once(__DIR__ . "/../../config/connexion.php");  
require_once(__DIR__ . "/../../modele/commentaire.php");
require_once(__DIR__ . "/../../modele/utilisateur.php");
require_once(__DIR__ . "/../../vendor/autoload.php");

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

Connexion::connect();

$requestMethod = $_SERVER["REQUEST_METHOD"];

function sendDeletionEmail($email, $commentText) {
    $mail = new PHPMailer(true);
    try {
        // Paramètres du serveur
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'voixcitoyenne1@gmail.com';
        $mail->Password = 'tdym vlta yiio fbnv';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Définir l'encodage de l'email
        $mail->CharSet = 'UTF-8';

        // Destinataires
        $mail->setFrom('no-reply@yourdomain.com', 'Voix Citoyenne');
        $mail->addAddress($email);

        // Contenu de l'email
        $mail->isHTML(true);
        $mail->Subject = "Votre commentaire a été supprimé";
        $mail->Body = "Bonjour,<br><br>Votre commentaire suivant a été supprimé en raison d'une violation des règles :<br><br><i>$commentText</i><br><br>Cordialement,<br>L'équipe Voix Citoyenne";

        $mail->send();
    } catch (Exception $e) {
        // Log the error or handle it as needed
    }
}

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
        if (isset($data['com_txt'], $data['user_id'], $data['prop_id'])) {
            $com_txt = htmlspecialchars($data['com_txt']);
            $user_id = intval($data['user_id']);
            $prop_id = intval($data['prop_id']);
            try {
                $com_id = Commentaire::addCommentaire($com_txt, $user_id, $prop_id);
                echo json_encode(["status" => "success", "message" => "Commentaire créé avec succès", "com_id" => $com_id]);
            } catch (Exception $e) {
                echo json_encode(["status" => "error", "message" => "Erreur lors de la création du commentaire", "error" => $e->getMessage()]);
            }
        } else {
            echo json_encode(["status" => "error", "message" => "Données manquantes pour créer le commentaire"]);
        }
        break;
    case 'PUT':
        $data = json_decode(file_get_contents("php://input"), true);
        if (isset($data['com_id'], $data['com_txt'])) {
            $commentaire = Commentaire::getCommentaireById($data['com_id']);
            if ($commentaire) {
                $commentaire->set('com_txt', htmlspecialchars($data['com_txt']));
                try {
                    Commentaire::updateCommentaire($commentaire);
                    echo json_encode(["status" => "success", "message" => "Commentaire mis à jour avec succès"]);
                } catch (Exception $e) {
                    echo json_encode(["status" => "error", "message" => "Erreur lors de la mise à jour du commentaire", "error" => $e->getMessage()]);
                }
            } else {
                echo json_encode(["status" => "error", "message" => "Commentaire non trouvé"]);
            }
        } else {
            echo json_encode(["status" => "error", "message" => "Données manquantes pour mettre à jour le commentaire"]);
        }
        break;
    case 'DELETE':
        if (isset($_GET['id'])) {
            try {
                $commentaire = Commentaire::getCommentaireById($_GET['id']);
                if ($commentaire) {
                    $user = Utilisateur::getUtilisateurById($commentaire->get('user_id'));
                    Commentaire::deleteCommentaire($_GET['id']);
                    sendDeletionEmail($user->get('user_mail'), $commentaire->get('com_txt'));
                    echo json_encode(["status" => "success", "message" => "Commentaire supprimé avec succès"]);
                } else {
                    echo json_encode(["status" => "error", "message" => "Commentaire non trouvé"]);
                }
            } catch (Exception $e) {
                echo json_encode(["status" => "error", "message" => "Erreur lors de la suppression du commentaire", "error" => $e->getMessage()]);
            }
        } else {
            echo json_encode(["status" => "error", "message" => "ID du commentaire manquant"]);
        }
        break;
    default:
        header("HTTP/1.1 405 Method Not Allowed");
        echo json_encode(["status" => "error", "message" => "Méthode non autorisée"]);
        break;
}
?>