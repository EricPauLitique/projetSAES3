<?php
session_start();

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['prenom']) || !isset($_SESSION['nom'])) {
    header("Location: connexion.html");
    exit;
}

// Connexion à la base de données
require_once("../config/connexion.php");
require_once("../modele/groupe.php"); // inclure la classe Groupe

Connexion::connect();

// Récupérer les données du formulaire
$groupId = isset($_SESSION['group_id']) ? $_SESSION['group_id'] : null;
$nomGroupe = $_POST['nom_du_groupe'] ?? null;
$couleur = $_POST['color'] ?? null;
$limiteAnnuelle = $_POST['limite_annuelle'] ?? null;
$image = $_FILES['image'] ?? null;
$removeImage = isset($_POST['remove_image']) ? $_POST['remove_image'] : 0;

// Vérifier si l'ID du groupe existe
if ($groupId) {
    // Récupérer les informations du groupe
    $group = Groupe::getGroupByIdUnique($groupId);

    // Traiter l'image téléchargée
    if ($removeImage == '1') {
        $newImagePath = '../images/groupes/groupe.png'; // Set to default image
        if ($group['grp_img'] != '../images/groupes/groupe.png' && file_exists($group['grp_img'])) {
            unlink($group['grp_img']); // Remove the old image
        }
        $_SESSION['image_name'] = ''; // Clear the image name in session
        header("Location: ../vue/modifgroupe.php"); // Redirection vers la même page
    } else {
        $newImagePath = Groupe::handleImageUpload($group, $image);
        // Sauvegarder le nom du fichier dans la session
        if (isset($image['name']) && !empty($image['name'])) {
            $_SESSION['image_name'] = $image['name'];
        } else {
            $_SESSION['image_name'] = 'Aucun fichier choisi';
        }
    }

    // Mettre à jour le groupe
    $updateSuccess = Groupe::updateGroup($groupId, $nomGroupe, $couleur, $limiteAnnuelle, $newImagePath);

    if ($updateSuccess) {
        $_SESSION['message'] = "Le groupe a été modifié avec succès.";
        header("Location: ../vue/accueil.php"); // Redirection vers la page d'accueil
        exit;
    } else {
        $_SESSION['messageC'] = "Erreur lors de la mise à jour du groupe.";
        header("Location: ../vue/modifgroupe.php"); // Retourner à la vue de modification
        exit;
    }
} else {
    $_SESSION['messageC'] = "Erreur : ID du groupe non valide.";
    $_SESSION['message'] = "Erreur : ID du groupe non valide.";
    header("Location: ../vue/accueil.php");
    exit;
}

?>