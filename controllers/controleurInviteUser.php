<?php
require_once(__DIR__ . "/../config/connexion.php");
require_once(__DIR__ . "/../modele/utilisateur.php");
require_once(__DIR__ . "/../modele/groupe.php");
require_once(__DIR__ . "/../vendor/autoload.php");

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

Connexion::connect();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);
    $email = htmlspecialchars($data['email']);
    $groupId = intval($data['groupId']);

    // Vérifiez si le groupe existe
    $groupe = Groupe::getGroupByIdUnique2($groupId);
    if (!$groupe) {
        echo json_encode(['status' => 'error', 'message' => 'Groupe non trouvé.']);
        exit;
    }

    // Générer un lien d'invitation unique
    $token = bin2hex(random_bytes(16));
    $inviteLink = "https://beloved-accepted-squid.ngrok-free.app/projetSAES3/vue/inviteGroupe.php?token=$token&groupId=$groupId&email=$email";

    // Envoyer l'email d'invitation avec PHPMailer
    $mail = new PHPMailer(true);
    try {
        // Activer le débogage de PHPMailer
        $mail->SMTPDebug = 0; // Désactiver le débogage pour éviter les sorties non JSON
        $mail->Debugoutput = 'html'; // Format de sortie du débogage

        // Paramètres du serveur
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com'; // Spécifiez le serveur SMTP
        $mail->SMTPAuth = true;
        $mail->Username = 'voixcitoyenne1@gmail.com'; // Votre adresse email SMTP
        $mail->Password = 'tdym vlta yiio fbnv'; // Votre mot de passe SMTP
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Destinataires
        $mail->setFrom('no-reply@yourdomain.com', 'Voix Citoyenne');
        $mail->addAddress($email);

        // Contenu de l'email
        $mail->isHTML(true);
        $mail->Subject = "Invitation à rejoindre le groupe <b>" . $groupe->get('grp_nom'). "</b>";
        $mail->Body = "Bonjour,<br><br>Vous avez été invité à rejoindre le groupe <b>" . $groupe->get('grp_nom') . "</b>.<br><br>Cliquez sur le lien suivant pour accepter ou refuser l'invitation : <a href='$inviteLink'>$inviteLink</a><br><br>Cordialement,<br>L'équipe Voix Citoyenne";

        $mail->send();
        echo json_encode(['status' => 'success', 'message' => 'Invitation envoyée avec succès.']);
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => "Erreur lors de l'envoi de l'invitation : {$mail->ErrorInfo}"]);
    }
}
?>