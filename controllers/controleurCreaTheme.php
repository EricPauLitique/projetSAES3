<?php
session_start();

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['prenom']) || !isset($_SESSION['nom'])) {
    header("Location: connexion.html");
    exit;
}

// Ajouter un thème à la session si le formulaire est soumis
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['nom_du_theme']) && isset($_POST['limite_theme'])) {
    $nom_du_theme = htmlspecialchars($_POST['nom_du_theme']);
    $nom_du_theme = ucfirst($nom_du_theme);
    $limite_theme = (int)$_POST['limite_theme'];

    $_SESSION['themes'][] = [
        'theme_nom' => $nom_du_theme,
        'limite_theme' => $limite_theme
    ];

    header("Location: ../vue/creagroupe.php");
    exit;
}

// Vérifier si un thème doit être supprimé
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_theme'])) {
    $theme_index = (int)$_POST['delete_theme'];

    if (isset($_SESSION['themes'][$theme_index])) {
        unset($_SESSION['themes'][$theme_index]);
        $_SESSION['themes'] = array_values($_SESSION['themes']);
    }

    header("Location: ../vue/creagroupe.php");
    exit;
}
?>