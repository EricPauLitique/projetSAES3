<?php
session_start();

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['prenom']) || !isset($_SESSION['nom'])) {
    header("Location: connexion.php");
    exit;
}

// Connexion à la base de données
require_once("../config/connexion.php");
require_once("../modele/groupe.php"); // inclure la classe Groupe
require_once("../modele/comporte.php"); // inclure la classe Comporte

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
    // Vérifier si le nom du groupe existe déjà pour un autre groupe
    if (Groupe::groupNameExists($nomGroupe, $groupId)) {
        $_SESSION['messageC'] = '<p style="color: red;"><b>Le nom du groupe du site est déjà présent dans le registre. <br> Veuillez choisir un autre nom.</b></p><br>';
        header("Location: ../vue/modifgroupe.php");
        exit;
    }

    // Récupérer les informations du groupe
    $group = Groupe::getGroupByIdUnique($groupId);

    // Calculer la somme des limites des thèmes
    $sommeMonetaire = Comporte::getSumLimiteThemeByGroupId($groupId);

    // Vérifie si la somme des thèmes dépasse la limite annuelle
    if ($sommeMonetaire > $limiteAnnuelle) {
        $messageC = '<p style="color: red;"><b>Vous êtes en train de dépasser les fonds monétaires du groupe. Les thèmes ont une somme totale de ' . $sommeMonetaire . '€ alors que le groupe que vous être entrain de modifier ne possède que ' . $limiteAnnuelle . '€. <br> Merci de modifier la limite annuelle pour qu\'elle ne dépasse pas la somme allouée aux thèmes !</b></p><br>';        
        $_SESSION['messageC'] = $messageC;
        header("Location: ../vue/modifgroupe.php");
        exit;
    }

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