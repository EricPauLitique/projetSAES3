<?php
session_start();
require_once("../config/connexion.php");
require_once("../modele/groupe.php");

// Vérification si le groupe ID est passé dans la requête POST
if (isset($_POST['group_id'])) {
    $groupId = $_POST['group_id'];

    // Connexion à la base de données
    Connexion::connect();
    $db = Connexion::pdo();
 
    // Récupérer les informations du groupe
    $requete = "SELECT * FROM groupe WHERE grp_id = :grp_id";
    $stmt = $db->prepare($requete);
    $stmt->execute(['grp_id' => $groupId]);
    $group = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($group) {
        // Récupérer le chemin de l'image
        $imagePath = $group['grp_img'];

        // Supprimer l'image si elle existe
        if (file_exists($imagePath)) {
            if (!$imagePath == '../images/groupes/groupe.png') {
                if (unlink($imagePath)) {
                    // Optionnel: Supprimer le répertoire parent si vide
                    $directoryPath = dirname($imagePath);
                    if (is_dir($directoryPath) && count(scandir($directoryPath)) == 2) { // Seuls '.' et '..' dans le dossier
                        rmdir($directoryPath);
                    }
                }
            }
        } 
        

        // Suppression du groupe de la base de données
        $result = Groupe::deleteGroupById($groupId);

        // Afficher un message selon le succès ou l'échec de la suppression
        if ($result) {
            $_SESSION['message'] = '<span style="color: green; font-weight: bold;">Le groupe "' . htmlspecialchars($group['grp_nom']) . '" a été supprimé avec succès.</span>';
        } else {
            $_SESSION['message'] = '<b><i style="color: red;">Erreur lors de la suppression du groupe.</i></b>';
        }
    } else {
        $_SESSION['message'] = '<b><i style="color: red;">Groupe introuvable.</i></b>';
    }

    // Redirection vers la page d'accueil après suppression
    header("Location: ../vue/accueil.php");
    exit();
} else {
    $_SESSION['message'] = '<b><i style="color: red;">ID du groupe manquant.</i></b>';
    header("Location: ../vue/accueil.php");
    exit();
}