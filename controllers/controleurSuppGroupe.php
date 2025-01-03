<?php
session_start();
require_once(__DIR__ . "/../config/connexion.php");
require_once(__DIR__ . "/../modele/groupe.php");

// Vérification si le groupe ID est passé dans la requête POST
$data = json_decode(file_get_contents("php://input"), true);
if (isset($data['group_id'])) {
    $groupId = $data['group_id'];

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
        $imagePath = __DIR__ . '/../' . $group['grp_img'];

        // Supprimer l'image si elle existe
        if (file_exists($imagePath)) {
            if ($imagePath !== __DIR__ . '/../images/groupes/groupe.png') {
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

        // Renvoyer une réponse JSON selon le succès ou l'échec de la suppression
        if ($result) {
            echo json_encode(['status' => 'success', 'message' => 'Le groupe "' . htmlspecialchars($group['grp_nom']) . '" a été supprimé avec succès.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Erreur lors de la suppression du groupe.']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Groupe introuvable.']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'ID du groupe manquant.']);
}
exit();
?>