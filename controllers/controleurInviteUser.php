<?php
session_start();
require_once(__DIR__ . "/../vendor/autoload.php");
require_once(__DIR__ . "/../config/connexion.php");
require_once(__DIR__ . "/../modele/utilisateur.php");
require_once(__DIR__ . "/../modele/groupe.php");

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

Connexion::connect();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);
    $email = htmlspecialchars($data['email']);
    $groupId = intval($data['groupId']);

    // Si Utilisateur existe déjà dans le mail de la base de données
    $mailExist = Utilisateur::siMailExisteGrp($email, $groupId); // Coté Membre
    $getUser = Utilisateur::getNameUser($email);

    // Si l'utilisateur est déjà membre du groupe
    if ($mailExist) {
        echo json_encode(['status' => 'error', 'message' => 'Cet utilisateur ' . $getUser->get('user_prenom') . ' ' . $getUser->get('user_nom') . ' est déjà membre du groupe.']);
        exit;
    }


    // Si l'utilisateur est déjà propriétaire du groupe
    if ($getUser) {
        $proprio = Groupe::siProprioInconnu($getUser->get('user_id'), $groupId); // Coté propriétaire
        if ($proprio) {
            echo json_encode(['status' => 'error', 'message' => 'Vous ne pouvez pas, vous invitez vous-même en tant que propriétaire du groupe.']);
            exit;
        }
    }
    // Vérifiez si le groupe existe
    $groupe = Groupe::getGroupByIdUnique2($groupId);
    if (!$groupe) {
        echo json_encode(['status' => 'error', 'message' => 'Groupe non trouvé.']);
        exit;
    }

    // Générer un lien d'invitation unique
    $token = bin2hex(random_bytes(16));
    $createdAt = time();

    // Stocker le token dans un fichier
    $tokenData = [
        'token' => $token,
        'group_id' => $groupId,
        'email' => $email,
        'created_at' => $createdAt
    ];
    file_put_contents(__DIR__ . "/../tokens/$token.json", json_encode($tokenData));

    $inviteLink = "https://projets.iut-orsay.fr/saes3-ese/projetSAES3/vue/inviteGroupe.php?token=$token&groupId=$groupId";

    // Envoyer l'email d'invitation avec PHPMailer
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
        $mail->Subject = "Invitation à rejoindre le groupe " . htmlspecialchars($groupe->get('grp_nom'));
        $mail->Body = "Bonjour,<br><br>Vous avez été invité à rejoindre le groupe <b>" . htmlspecialchars($groupe->get('grp_nom')) . "</b>.<br><br>Cliquez sur le lien suivant pour accepter ou refuser l'invitation : <a href='$inviteLink'>$inviteLink</a><br><br>Cordialement,<br>L'équipe Voix Citoyenne<br><br><img src='cid:logo_voix_citoyenne'>";

        // Ajouter l'image en pièce jointe
        $mail->addEmbeddedImage(__DIR__ . '/../images/logo_mail.png', 'logo_voix_citoyenne');
        
        $mail->send();
        echo json_encode(['status' => 'success', 'message' => 'Invitation envoyée avec succès.']);
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => "Erreur lors de l'envoi de l'invitation : {$mail->ErrorInfo}"]);
    }
}
?>