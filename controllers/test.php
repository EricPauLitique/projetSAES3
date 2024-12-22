<?php
$directoryPath = '../images/groupes/Campagne_asso/';

// Vérifier si le répertoire existe
if (is_dir($directoryPath)) {
    // Vérifier si le répertoire est vide
    if (count(scandir($directoryPath)) == 2) { // `.` et `..` uniquement
        if (rmdir($directoryPath)) {
            echo 'Le répertoire a été supprimé avec succès.<br>';
        } else {
            echo 'Erreur lors de la suppression du répertoire.<br>';
        }
    } else {
        echo 'Le répertoire n\'est pas vide.<br>';
    }
} else {
    echo 'Le répertoire n\'existe pas.<br>';
}
?>