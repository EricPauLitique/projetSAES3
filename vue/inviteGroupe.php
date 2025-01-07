<?php
session_start();
require_once(__DIR__ . "/../config/connexion.php");
require_once(__DIR__ . "/../modele/utilisateur.php");
require_once(__DIR__ . "/../modele/groupe.php");
require_once(__DIR__ . "/../modele/membre.php");

Connexion::connect();

$token = $_GET['token'] ?? '';
$groupId = $_GET['groupId'] ?? '';

// Le nom du groupe 
$groupe = Groupe::getGroupByIdUnique2($groupId);

if ($token && $groupId) {
    $tokenFile = __DIR__ . "/../tokens/$token.json";
    if (!file_exists($tokenFile)) {
        $_SESSION['message'] = 'Invitation invalide ou déjà utilisée.';
        $_SESSION['redirect_after_login'] = null;
        header("Location: connexion.php");
        exit;
    }

    $tokenData = json_decode(file_get_contents($tokenFile), true);
    if ($tokenData['group_id'] !== intval($groupId)) {
        $_SESSION['message'] = 'Invitation invalide.';
        $_SESSION['redirect_after_login'] = null;
        header("Location: connexion.php");
        exit;
    }

    $tokenTime = $tokenData['created_at'];
    $currentTime = time();
    $tokenExpiry = 86400; // 1 jour (86400 secondes)

    if (($currentTime - $tokenTime) > $tokenExpiry) {
        $_SESSION['message'] = 'Invitation expirée.';
        $_SESSION['redirect_after_login'] = null;
        header("Location: connexion.php");
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $action = $_POST['action'];

        if ($action === 'accept') {
            // Vérifier si l'utilisateur est connecté
            if (!isset($_SESSION['id'])) {
                $_SESSION['invite_token'] = $token;
                $_SESSION['invite_groupId'] = $groupId;
                $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
                $_SESSION['message'] = 'Vous devez vous connecter pour accepter l\'invitation.';
                header("Location: connexion.php");
                exit;
            }

            // Ajouter l'utilisateur au groupe avec les cases à cocher
            $userId = $_SESSION['id'];
            $cocheReac = isset($_POST['coche_reac']) ? 1 : 0;
            $cocheNewProp = isset($_POST['coche_new_prop']) ? 1 : 0;
            $cocheResVote = isset($_POST['coche_res_vote']) ? 1 : 0;

            Membre::addMembre($userId, $groupId, $cocheReac, $cocheNewProp, $cocheResVote, 'Membre');

            // Supprimer le token du fichier
            unlink($tokenFile);

            $_SESSION['message'] = 'Vous avez accepté l\'invitation.';
            header("Location: groupe.php?id=$groupId");
            exit;
        } elseif ($action === 'decline') {
            // Supprimer le token du fichier
            unlink($tokenFile);

            $_SESSION['messageC'] = 'Vous avez refusé l\'invitation. Vous devez redemandez au créateur du groupe qu\'il vous refasse la demande d\'invitation.';
            header("Location: accueil.php");
            exit;
        }

        exit;
    }
} else {
    $_SESSION['message'] = 'Informations d\'invitation manquantes.';
    header("Location: connexion.php");
    exit;
}

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['id'])) {
    $_SESSION['invite_token'] = $token;
    $_SESSION['invite_groupId'] = $groupId;
    $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
    $_SESSION['message'] = 'Vous devez vous connecter pour accepter l\'invitation.';
    header("Location: connexion.php");
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
    <link rel="stylesheet" href="../styles/inviteGroupe.css">
    <title>Invitation à rejoindre un groupe</title>
</head>
    <body>
        <main>
            <h1>Invitation à rejoindre le groupe de <?php echo htmlspecialchars($groupe->get('grp_nom')); ?></h1>
            <p>Vous avez été invité à rejoindre le groupe de <?php echo htmlspecialchars($groupe->get('grp_nom')); ?>. Voulez-vous accepter ou refuser l'invitation ?</p>
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
        </main>
        <?php
        // Inclure le fichier footer.php
        include 'footer.php';
        ?>
    </body>
</html>