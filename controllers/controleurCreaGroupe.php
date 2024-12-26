<?php
// controleurCreaGroupe.php
require_once("../config/connexion.php");
$titre = "Création des groupes";
include("../vue/debut.php");


// Connexion à la base de données
Connexion::connect();
$pdo = Connexion::PDO();

// Vérifie si la session est active
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Récupération des données du formulaire avec validation/sécurisation
$nomGroupe = filter_input(INPUT_POST, 'nom_du_groupe', FILTER_SANITIZE_STRING);
$nomGroupe = ucfirst(strtolower($nomGroupe));
$couleur = filter_input(INPUT_POST, 'color', FILTER_SANITIZE_STRING);
$limiteAnnuelle = filter_input(INPUT_POST, 'limite_annuelle', FILTER_VALIDATE_INT);
$idUtilisateur = htmlspecialchars($_SESSION['id']);
$sommeMonetaire = 0;

$_SESSION['nomGroupe'] = $nomGroupe;
$_SESSION['couleur'] = $couleur;
$_SESSION['limiteAnnuelle'] = $limiteAnnuelle;



$stmt = $pdo->prepare("SELECT count(*) FROM groupe WHERE grp_nom = :grp_nom");
$stmt->execute(['grp_nom' => $nomGroupe]);
$resultNameExists = $stmt->fetchColumn();

if ($resultNameExists > 0) {
    $messageC = '<p style="color: red;"><b>Désoler le nom du groupe, existe ! <br> Merci de modifier : le nom du groupe.  </b></p>';
    $_SESSION['messageC'] = $messageC;
    header("Location: ../vue/creagroupe.php");
    exit;
}


// Vérification des thèmes en session
if (isset($_SESSION['themes']) && !empty($_SESSION['themes'])) {
    try {
        // Calculer la somme des limites des thèmes
        foreach ($_SESSION['themes'] as $theme) {
            $sommeMonetaire += $theme['limite_theme'];
        }

        // Vérifie si la somme des thèmes dépasse la limite annuelle
        if ($sommeMonetaire > $limiteAnnuelle) {
            $messageC = '<p style="color: red;"><b>Vous dépassez les fonds monétaires du groupe, les thèmes font une somme de ' . $sommeMonetaire . '€ alors que le groupe en possède que ' . $limiteAnnuelle . '€. Merci d\'en supprimer les fonds du thème !</b></p>';
            $_SESSION['messageC'] = $messageC;
            header("Location: ../vue/creagroupe.php");
            exit;
        }

        $imagePath = null;
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $image = $_FILES['image'];
            $imageName = basename($image['name']);
            $imageTmpPath = $image['tmp_name'];
            $imageExtension = strtolower(pathinfo($imageName, PATHINFO_EXTENSION));

            // Valider l'extension de l'image
            $allowedExtensions = ['jpg', 'jpeg', 'png'];
            if (!in_array($imageExtension, $allowedExtensions)) {
                $messageC =  "<p style='color: red;'>Format d'image non valide. Formats acceptés : jpg, jpeg, png.</p>";
                $_SESSION['messageC'] = $messageC;
                header("Location: ../vue/creagroupe.php");
                exit;
            }

            // Vérifier la taille de l'image
            $maxSize = 5 * 1024 * 1024; // 5 MB
            if ($image['size'] > $maxSize) {
                $messageC =  "<p style='color: red;'>L'image est trop grande. La taille maximale est de 5 Mo.</p>";
                $_SESSION['messageC'] = $messageC;
                header("Location: ../vue/creagroupe.php");
                exit;
            }

            // Créer un dossier pour stocker les images des groupes
            $groupFolder = '../images/groupes/' . preg_replace('/[^a-zA-Z0-9_]/', '_', $nomGroupe);
            if (!is_dir($groupFolder)) {
                // Créer le répertoire avec les permissions adéquates (rwx pour le groupe)
                mkdir($groupFolder, 0775, true);
            }

            // Générer un chemin unique pour l'image
            $imagePath = $groupFolder . '/' . uniqid() . '_' . $imageName;

            // Déplacer l'image téléchargée
            if (!move_uploaded_file($imageTmpPath, $imagePath)) {
                $messageC =  "<p style='color: red;'>Erreur lors du téléchargement de l'image.</p>";
                $_SESSION['messageC'] = $messageC;
                header("Location: ../vue/creagroupe.php");
                exit;
            }

            // Modifier les permissions du fichier pour que le groupe ait 'rw'
            chmod($imagePath, 0664);
        }

        else {
            $imagePath = '../images/groupes/groupe.png';
        }
        
        // Générer un nouvel ID pour le groupe
        $stmt = $pdo->query("SELECT MAX(grp_id) FROM groupe");
        $maxIdGrp = $stmt->fetchColumn();
        $resultIdGrp = $maxIdGrp + 1;

        // Préparer la requête d'insertion pour le groupe
        $stmt = $pdo->prepare("
            INSERT INTO groupe (grp_id, grp_nom, grp_couleur, grp_img, grp_lim_an, user_id)
            VALUES (:grp_id, :grp_nom, :grp_couleur, :grp_img, :grp_lim_an, :user_id)
        ");
        $stmt->execute([
            ':grp_id' => $resultIdGrp,
            ':grp_nom' => $nomGroupe,
            ':grp_couleur' => $couleur,
            ':grp_img' => $imagePath,
            ':grp_lim_an' => $limiteAnnuelle,
            ':user_id' => $idUtilisateur
        ]);

        // Gestion des thèmes associés au groupe
        foreach ($_SESSION['themes'] as $theme) {
            $themeNom = $theme['theme_nom'];
            $themeLim = $theme['limite_theme'];

            // Vérifie si le thème existe déjà
            $stmt = $pdo->prepare("SELECT theme_id FROM theme WHERE theme_nom = :theme_nom");
            $stmt->execute(['theme_nom' => $themeNom]);
            $resultIdTheme = $stmt->fetchColumn();

            // Si le thème n'existe pas, l'insérer
            if (!$resultIdTheme) {
                $stmt = $pdo->query("SELECT MAX(theme_id) FROM theme");
                $maxIdTheme = $stmt->fetchColumn();
                $resultIdTheme = $maxIdTheme + 1;

                $stmt = $pdo->prepare("INSERT INTO theme (theme_id, theme_nom) VALUES (:theme_id, :theme_nom)");
                $stmt->execute(['theme_id' => $resultIdTheme, 'theme_nom' => $themeNom]);
            }

            // Lier le thème au groupe
            $stmt = $pdo->prepare("
                INSERT INTO comporte (grp_id, theme_id, lim_theme)
                VALUES (:grp_id, :theme_id, :lim_theme)
            ");
            $stmt->execute([
                ':grp_id' => $resultIdGrp,
                ':theme_id' => $resultIdTheme,
                ':lim_theme' => $themeLim
            ]);
        }

        $message = "<p style='color: green;'>Le groupe " . $nomGroupe . " a été créé avec succès.</p>";
        $_SESSION['message'] = $message;
        $_SESSION['nomGroupe'] = null;
        $_SESSION['couleur'] = null;
        $_SESSION['limiteAnnuelle'] = null;
        $_SESSION['themes'] = null;
        header("Location: ../vue/accueil.php");
        exit;

    } catch (Exception $e) {
        $message = "<p style='color: red;'>Erreur lors de la création du groupe : " . $e->getMessage() . "</p>";
        $_SESSION['message'] = $message;
        header("Location: ../vue/accueil.php");
        exit;

    }
} else {
    $messageC = '<p style="color: red;"><b>Merci de remplir le(s) thème(s) !</b></p>';
    $_SESSION['messageC'] = $messageC;
    header("Location: ../vue/creagroupe.php");
    exit;
}
?>