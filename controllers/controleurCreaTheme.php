<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $theme = filter_input(INPUT_POST, 'nom_du_theme', FILTER_SANITIZE_STRING);
    $lim_theme = filter_input(INPUT_POST, 'limite_theme', FILTER_SANITIZE_STRING);
    $prenom = ucfirst(strtolower(string: $prenom));

    $cptIdTheme = $pdo->prepare("SELECT distinct theme_id FROM theme WHERE theme_nom = :tNom");
    $cptIdTheme->execute([
        'tNom' => $theme
    ]);
    $resultIdTheme = $cptIdTheme->fetchColumn();
        
    if ($resultIdTheme == 0) {
        // Si le nom du theme n'existe pas, calculer un nouvel ID pour l'adresse
        $stmt = $pdo->prepare("SELECT MAX(adr_id) FROM theme");
        $stmt->execute();
        $maxIdTheme = $stmt->fetchColumn();
        $resultIdTheme = $maxIdTheme + 1; 
    }

    

    }
?>