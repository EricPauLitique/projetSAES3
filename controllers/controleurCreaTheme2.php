<?php
session_start();
require_once("../config/connexion.php");
require_once("../modele/theme.php");
require_once("../modele/comporte.php");

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['prenom']) || !isset($_SESSION['nom'])) {
    header("Location: ../vue/connexion.php");
    exit;
}

Connexion::connect();

// Ajouter un thème à la base de données si le formulaire est soumis
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['nom_du_theme']) && isset($_POST['limite_theme']) && isset($_POST['group_id']) && $_POST['limite_grp']) {
    $nom_du_theme = htmlspecialchars($_POST['nom_du_theme']);
    $nom_du_theme = ucfirst(strtolower($nom_du_theme));
    $limite_theme = (int)$_POST['limite_theme'];
    $group_id = (int)$_POST['group_id'];
    $limite_grp = (int)$_POST['limite_grp'];

    // Vérifier si le thème existe déjà dans la base de données
    $existingTheme = Theme::getThemeByName($nom_du_theme);
    $incrementID = Theme::getMaxTheme() + 1;


    if ($limite_theme+Comporte::getSumLimiteThemeByGroupId($group_id) > $limite_grp) {
        $_SESSION['messageC'] = '<p style="color: red; font-weight: bold;"><b>La limite annuelle des thèmes ne doit pas dépasser la limite annuelle du groupe.</b></p>';
        header("Location: ../vue/ajoutTheme.php?id=" . $group_id);
        exit;
    }

    if ($existingTheme === false) {
        // Ajouter le nouveau thème
        $newThemeId = Theme::createTheme($incrementID, $nom_du_theme);
        $themeId = $incrementID;
    } else {
        $themeId = $existingTheme->get('theme_id');
    }

    // Vérifier si l'association existe déjà
    if (!Comporte::existsThemeInGroup($group_id, $themeId)) {
        // Ajouter le thème au groupe dans la table comporte
        $addSuccess = Comporte::addThemeToGroup($group_id, $themeId, $limite_theme);

        if ($addSuccess) {
            $_SESSION['message'] = '<p style="color: green; font-weight: bold;"><b>Le thème a été ajouté avec succès.</b></p>';
        } else {
            $_SESSION['messageC'] = '<p style="color: red; font-weight: bold;"><b>Erreur lors de l\'ajout du thème.</b></p>';
            header("Location: ../vue/ajoutTheme.php?id=" . $group_id);
            exit;
        }
    } else {
        // Informer l'utilisateur que le thème existe déjà et qu'il doit modifier le prix
        $_SESSION['messageC'] = '<p style="color: red; font-weight: bold;"><b>Le thème existe déjà dans ce groupe. Veuillez modifier le prix.</b></p>';
    }

    header("Location: ../vue/groupe.php?id=" . $group_id);
    exit;
}

header("Location: ../vue/groupe.php");
exit;
?>