<?php
session_start();

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['prenom']) || !isset($_SESSION['nom'])) {
    echo json_encode(['status' => 'error', 'message' => 'Utilisateur non connecté.']);
    exit;
}

// Connexion à la base de données
require_once(__DIR__ . "/../config/connexion.php");
require_once(__DIR__ . "/../modele/groupe.php"); // inclure la classe Groupe
require_once(__DIR__ . "/../modele/comporte.php"); // inclure la classe Comporte

Connexion::connect();

// Récupérer les données du formulaire
$data = $_POST;
$groupId = isset($_SESSION['group_id']) ? $_SESSION['group_id'] : null;
$nomGroupe = $data['nom_du_groupe'] ?? null;
$couleur = $data['color'] ?? null;
$limiteAnnuelle = $data['limite_annuelle'] ?? null;
$image = isset($_FILES['image']) ? $_FILES['image'] : null;
$removeImage = isset($data['remove_image']) ? $data['remove_image'] : 0;

// Ajoutez un message de débogage pour vérifier l'ID du groupe
error_log("ID du groupe reçu : " . $groupId);

// Ajoutez un message de débogage pour vérifier la limite annuelle
error_log("Limite annuelle reçue : " . $limiteAnnuelle);

// Vérifier si l'ID du groupe existe
if ($groupId) {
    // Récupérer les informations du groupe
    $group = Groupe::getGroupByIdUnique($groupId);
    $ancienNomGroupe = $group['grp_nom'];
    $ancienRepertoire = __DIR__ . "/../images/groupes/" . $groupId;

    // Vérifier si le nom du groupe existe déjà pour un autre groupe
    if (Groupe::groupNameExists($nomGroupe, $groupId)) {
        echo json_encode(['status' => 'error', 'message' => 'Le nom du groupe du site est déjà présent dans le registre. Veuillez choisir un autre nom.']);
        exit;
    }

    // Calculer la somme des limites des thèmes
    $sommeMonetaire = Comporte::getSumLimiteThemeByGroupId($groupId);

    // Ajoutez un message de débogage pour vérifier la somme monétaire
    error_log("Somme monétaire des thèmes : " . $sommeMonetaire);

    // Vérifie si la somme des thèmes dépasse la limite annuelle
    if ($sommeMonetaire > $limiteAnnuelle) {
        echo json_encode(['status' => 'error', 'message' => 'Vous êtes en train de dépasser les fonds monétaires du groupe. Les thèmes ont une somme totale de ' . $sommeMonetaire . '€ alors que le groupe que vous être entrain de modifier ne possède que ' . $limiteAnnuelle . '€. Merci de modifier la limite annuelle pour qu\'elle ne dépasse pas la somme allouée aux thèmes !']);
        exit;
    }

    // Renommer le répertoire si le nom du groupe a changé
    $nouveauRepertoire = __DIR__ . "/../images/groupes/" . $groupId;
    if ($ancienRepertoire !== $nouveauRepertoire && is_dir($ancienRepertoire)) {
        rename($ancienRepertoire, $nouveauRepertoire);
    }

    // Traiter l'image téléchargée
    if ($removeImage == '1') {
        $newImagePath = '../images/groupes/groupe.png'; // Set to default image
        if ($group['grp_img'] != '../images/groupes/groupe.png' && file_exists(__DIR__ . '/../' . $group['grp_img'])) {
            unlink(__DIR__ . '/../' . $group['grp_img']); // Remove the old image
        }
        $_SESSION['image_name'] = ''; // Clear the image name in session
    } else {
        // Utiliser l'ancien chemin de l'image si l'image n'est pas modifiée
        if ($image && $image['error'] == 0) {
            $newImagePath = Groupe::handleImageUpload($group, $image, $nouveauRepertoire);
            // Sauvegarder le nom du fichier dans la session
            if (isset($image['name']) && !empty($image['name'])) {
                $_SESSION['image_name'] = $image['name'];
            } else {
                $_SESSION['image_name'] = basename($newImagePath);
            }
        } else {
            // Si l'image n'est pas modifiée, mettre à jour le chemin de l'image avec le nouveau répertoire
            $relativeImagePath = str_replace(__DIR__ . '/../', '../', $group['grp_img']);
            $newImagePath = str_replace($ancienRepertoire, $nouveauRepertoire, $relativeImagePath);
        }
    }

    // Convertir le chemin absolu en chemin relatif
    $newImagePath = str_replace(__DIR__ . '/../', '../', $newImagePath);

    // Mettre à jour le groupe
    $updateSuccess = Groupe::updateGroup($groupId, $nomGroupe, $couleur, $limiteAnnuelle, $newImagePath);

    if ($updateSuccess) {
        echo json_encode(['status' => 'success', 'message' => 'Le groupe a été modifié avec succès.']);
        exit;
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Erreur lors de la mise à jour du groupe.']);
        exit;
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Erreur : ID du groupe non valide.']);
    exit;
}
?>