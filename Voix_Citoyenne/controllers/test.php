<?php
function deleteDirectory($dir) {
    if (!is_dir($dir)) {
        return false;
    }

    $items = array_diff(scandir($dir), array('.', '..'));
    foreach ($items as $item) {
        $path = $dir . DIRECTORY_SEPARATOR . $item;
        is_dir($path) ? deleteDirectory($path) : unlink($path);
    }

    return rmdir($dir);
}

$directoryPath = '../images/groupes/Shields/';

// Vérifier si le répertoire existe
if (is_dir($directoryPath)) {
    if (deleteDirectory($directoryPath)) {
        echo 'Le répertoire a été supprimé avec succès.<br>';
    } else {
        echo 'Erreur lors de la suppression du répertoire.<br>';
    }
} else {
    echo 'Le répertoire n\'existe pas.<br>';
}
?>