<?php

// Démarrage de la session
session_start();

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['prenom']) || !isset($_SESSION['nom']) || !isset($_SESSION['id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Utilisateur non connecté.']);
    exit;
}

// Connexion à la base de données
require_once(__DIR__ . "/../config/connexion.php");
require_once(__DIR__ . "/../modele/groupe.php"); // inclure la classe Groupe

Connexion::connect();
// Afficher les données de session pour le débogage
error_log("Données de session : " . print_r($_SESSION, true));

// Vérifier si l'ID du groupe est envoyé via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Vérifier si l'ID du groupe est envoyé via JSON
    $data = json_decode(file_get_contents("php://input"), true);
    $groupId = isset($data['group_id']) ? $data['group_id'] : null;

    // Ajoutez un message de débogage pour vérifier l'ID du groupe
    error_log("ID du groupe reçu : " . $groupId);

    // Vérifier si l'ID du groupe existe
    if ($groupId) {
        // Récupérer les informations du groupe à partir de la base de données
        $group = Groupe::getGroupByIdUnique($groupId);

        if ($group) {
            // Sauvegarder ces données dans la session pour les utiliser dans la vue
            $_SESSION['group_id'] = $groupId;
            $_SESSION['nomGroupe'] = $group['grp_nom'];
            $_SESSION['couleur'] = $group['grp_couleur'];
            $_SESSION['limiteAnnuelle'] = $group['grp_lim_an'];

            // Extraire le nom du fichier de l'image
            if ($group['grp_img'] != '../images/groupes/groupe.png') {
                $_SESSION['image_name'] = basename($group['grp_img']);
            } else {
                $_SESSION['image_name'] = '';
            }

            // Renvoie une réponse JSON indiquant le succès
            echo json_encode(['status' => 'success', 'message' => 'Données du groupe récupérées avec succès.']);
            exit;
        } else {
            echo json_encode(['status' => 'error', 'message' => "Le groupe n'a pas été trouvé."]);
            exit;
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Aucun ID de groupe fourni.']);
        exit;
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Méthode non autorisée.']);
    exit;
}
?>