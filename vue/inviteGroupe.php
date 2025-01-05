<?php
// filepath: /Users/ericse/ProjetS3/projetSAES3/vue/inviteGroupe.php

require_once(__DIR__ . "/../config/connexion.php");
require_once(__DIR__ . "/../modele/utilisateur.php");
require_once(__DIR__ . "/../modele/groupe.php");
require_once(__DIR__ . "/../modele/membre.php");

Connexion::connect();

$token = $_GET['token'] ?? '';
$groupId = $_GET['groupId'] ?? '';
$email = $_GET['email'] ?? '';

if ($token && $groupId && $email) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $action = $_POST['action'];

        if ($action === 'accept') {
            // Vérifier si l'utilisateur est connecté
            if (!isset($_SESSION['user_id'])) {
                $_SESSION['invite_token'] = $token;
                $_SESSION['invite_groupId'] = $groupId;
                $_SESSION['invite_email'] = $email;
                header("Location: connexion.php");
                exit;
            }

            // Ajouter l'utilisateur au groupe avec les cases à cocher
            $userId = $_SESSION['user_id'];
            $cocheReac = isset($_POST['coche_reac']) ? 1 : 0;
            $cocheNewProp = isset($_POST['coche_new_prop']) ? 1 : 0;
            $cocheResVote = isset($_POST['coche_res_vote']) ? 1 : 0;

            Membre::addMembre($userId, $groupId, $cocheReac, $cocheNewProp, $cocheResVote);
            echo "Vous avez accepté l'invitation.";
        } elseif ($action === 'decline') {
            echo "Vous avez refusé l'invitation.";
        }

        exit;
    }
} else {
    echo "Informations d'invitation manquantes.";
    exit;
}

// Inclure le fichier header.php
include 'header.php';
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="../images/logoVC.ico" rel="shortcut icon" type="image/x-icon" />
    <title>Invitation à rejoindre un groupe</title>
</head>
<body>
    <h1>Invitation à rejoindre le groupe</h1>
    <p>Vous avez été invité à rejoindre le groupe. Voulez-vous accepter ou refuser l'invitation ?</p>
    <form method="POST">
        <input type="hidden" name="action" value="accept">
        <div>
            <input type="checkbox" id="coche_reac" name="coche_reac">
            <label for="coche_reac">Recevoir des notifications de nouvelles réactions</label>
        </div>
        <div>
            <input type="checkbox" id="coche_new_prop" name="coche_new_prop">
            <label for="coche_new_prop">Recevoir des notifications de nouvelles propositions</label>
        </div>
        <div>
            <input type="checkbox" id="coche_res_vote" name="coche_res_vote">
            <label for="coche_res_vote">Recevoir des notifications de résultats de vote</label>
        </div>
        <button type="submit">Accepter</button>
        <button type="submit" name="action" value="decline">Refuser</button>
    </form>

    <?php
    // Inclure le fichier footer.php
    include 'footer.php';
    ?>
</body>
</html>