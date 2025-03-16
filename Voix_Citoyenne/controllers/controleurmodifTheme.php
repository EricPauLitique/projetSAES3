<?php
session_start();
require_once(__DIR__ . "/../config/connexion.php");
require_once("../modele/theme.php");
require_once("../modele/comporte.php");

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['prenom']) || !isset($_SESSION['nom'])) {
    header("Location: ../vue/connexion.php");
    exit;
}

Connexion::connect();

// Modifier un thème si le formulaire est soumis
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['theme_id']) && isset($_POST['nom_du_theme']) && isset($_POST['limite_theme']) && isset($_POST['group_id'])) {
    $theme_id = (int)$_POST['theme_id'];
    $nom_du_theme = htmlspecialchars($_POST['nom_du_theme']);
    $nom_du_theme = ucfirst(strtolower($nom_du_theme));
    $limite_theme = (int)$_POST['limite_theme'];
    $group_id = (int)$_POST['group_id'];

    // Vérifier si le nom du thème existe déjà
    $existingTheme = Theme::getThemeByName($nom_du_theme);
    if ($existingTheme) {
        $theme_id = $existingTheme->get('theme_id');
    }

    $data = [
        'theme_id' => $theme_id,
        'theme_nom' => $nom_du_theme,
        'limite_theme' => $limite_theme,
        'group_id' => $group_id
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "../api.php?endpoint=themes");
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));

    $response = curl_exec($ch);
    curl_close($ch);

    $result = json_decode($response, true);

    if ($result['status'] == 'success') {
        $_SESSION['message'] = '<p style="color: green; font-weight: bold;"><b>Le thème a été modifié avec succès.</b></p>';
    } else {
        $_SESSION['messageC'] = '<p style="color: red; font-weight: bold;"><b>' . $result['message'] . '</b></p>';
    }

    header("Location: ../vue/groupe.php?id=" . $group_id);
    exit;
}

header("Location: ../vue/groupe.php");
exit;
?>