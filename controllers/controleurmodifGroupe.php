<?php

// Démarrage de la session
session_start();

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['prenom']) || !isset($_SESSION['nom'])) {
    header("Location: ../vue/connexion.html");
    exit;
}

// Connexion à la base de données
require_once("../config/connexion.php");
require_once("../modele/groupe.php"); // inclure la classe Groupe


Connexion::connect();

// Vérifier si l'ID du groupe est envoyé via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $groupId = isset($_POST['group_id']) ? $_POST['group_id'] : null;

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

            // Rediriger vers la vue de modification
            header("Location: ../vue/modifgroupe.php");
            exit;
        } else {
            $_SESSION['message'] = "Le groupe n'a pas été trouvé.";
            header("Location: ../vue/accueil.php");
            exit;
        }
        
    } else {
        $_SESSION['message'] = "Aucun ID de groupe fourni.";
        header("Location: ../vue/accueil.php");
        exit;
    }
} else {
    // Si la méthode n'est pas POST, rediriger vers l'accueil
    header("Location: ../vue/accueil.php");
    exit;
}

?>