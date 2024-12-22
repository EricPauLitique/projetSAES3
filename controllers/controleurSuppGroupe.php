<?php
// Assurez-vous d'avoir bien connecté votre base de données
require_once("../config/connexion.php");
require_once("../modele/groupe.php");

Connexion::connect();
// Vérifier si la demande de suppression a été soumise
if (isset($_POST['group_id'])) {
    $groupId = $_POST['group_id'];

    echo $groupId ;

    // Récupérer les informations du groupe, y compris le chemin de l'image
    $db = Connexion::pdo();

    // Récupérer les informations du groupe, y compris le chemin de l'image
    $requete = "SELECT * FROM groupe WHERE grp_id = :grp_id";
    $stmt = $db->prepare($requete);
    $stmt->execute(['grp_id' => $groupId]);

    // Vérifier si un groupe a été trouvé
    $group = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($group) {
        // Récupérer le chemin de l'image
        $imagePath = $group['grp_img'];

        // Vérifier si l'image existe et la supprimer
        if (file_exists($imagePath)) {
            // Supprimer le fichier
            if (unlink($imagePath)) {
                echo 'L\'image a été supprimée avec succès.<br>';
        
                // Récupérer le chemin du répertoire parent
                $directoryPath = dirname($imagePath);
        
                // Vérifier si le répertoire est vide
                if (is_dir($directoryPath) && count(scandir($directoryPath)) == 2) { // `.` et `..` uniquement
                    // Supprimer le répertoire
                    if (rmdir($directoryPath)) {
                        echo 'Le répertoire a été supprimé avec succès.<br>';
                    } else {
                        echo 'Erreur lors de la suppression du répertoire.<br>';
                    }
                } else {
                    echo 'Le répertoire n\'est pas vide ou n\'existe pas.<br>';
                }
            } else {
                echo 'Erreur lors de la suppression de l\'image.<br>';
            }
        } else {
            echo 'Le fichier n\'existe pas.<br>';
        }
        }

        // Appeler une méthode pour supprimer le groupe de la base de données
        $result = Groupe::deleteGroupById($groupId);

        if ($result) {
            echo 'Le groupe a été supprimé avec succès.';
            // Redirection après la suppression (optionnel)
            header('Location: ../vue/accueil.php');
            exit();
        } else {
            echo 'Erreur lors de la suppression du groupe.';
        }
        
    } else {
        echo 'Groupe introuvable.';
}

?>