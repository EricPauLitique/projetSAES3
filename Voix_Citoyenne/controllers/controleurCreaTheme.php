<?php
//controleurCreaTheme.php
session_start();

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['prenom']) || !isset($_SESSION['nom'])) {
    echo json_encode(['status' => 'error', 'message' => 'Utilisateur non connecté.']);
    exit;
}

if (!isset($_SESSION['themes'])) {
    $_SESSION['themes'] = [];
}

// Ajouter un thème à la session si le formulaire est soumis
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);
    if (isset($data['nom_du_theme']) && isset($data['limite_theme'])) {
        $nom_du_theme = htmlspecialchars($data['nom_du_theme']);
        $nom_du_theme = ucfirst(strtolower($nom_du_theme));
        $limite_theme = (int)$data['limite_theme'];

        $_SESSION['themes'][] = [
            'theme_nom' => $nom_du_theme,
            'limite_theme' => $limite_theme
        ];

        echo json_encode(['status' => 'success', 'message' => 'Thème ajouté avec succès.']);
        exit;
    } elseif (isset($data['delete_theme'])) {
        $theme_index = (int)$data['delete_theme'];

        if (isset($_SESSION['themes'][$theme_index])) {
            unset($_SESSION['themes'][$theme_index]);
            $_SESSION['themes'] = array_values($_SESSION['themes']);
            echo json_encode(['status' => 'success', 'message' => 'Thème supprimé avec succès.']);
            exit;
        }
    }
}

echo json_encode(['status' => 'error', 'message' => 'Données invalides.']);
exit;
?>