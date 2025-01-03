<?php
require_once(__DIR__ . "/../config/connexion.php");

Connexion::connect();
$pdo = Connexion::PDO();

// Définir le chemin absolu pour les images des groupes
define('GROUP_IMAGES_PATH', __DIR__ . '/../images/groupes/');
define('DEFAULT_GROUP_IMAGE', '../images/groupes/groupe.png');

// Vérifie si la session est active
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
error_log("Session démarrée : " . print_r($_SESSION, true));

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['prenom']) || !isset($_SESSION['nom'])) {
    error_log("Utilisateur non connecté.");
    echo json_encode(['status' => 'error', 'message' => 'Utilisateur non connecté.']);
    exit;
}

// Récupération des données du formulaire avec validation/sécurisation
$nomGroupe = ucfirst(strtolower(filter_var($_POST['nom_du_groupe'], FILTER_SANITIZE_STRING)));
$couleur = filter_var($_POST['color'], FILTER_SANITIZE_STRING);
$limiteAnnuelle = filter_var($_POST['limite_annuelle'], FILTER_VALIDATE_INT);
$idUtilisateur = htmlspecialchars($_SESSION['id']);
$sommeMonetaire = 0;

$stmt = $pdo->prepare("SELECT count(*) FROM groupe WHERE grp_nom = :grp_nom");
$stmt->execute(['grp_nom' => $nomGroupe]);
$resultNameExists = $stmt->fetchColumn();

if ($resultNameExists > 0) {
    echo json_encode(['status' => 'error', 'message' => 'Le nom du groupe existe déjà.']);
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
            echo json_encode(['status' => 'error', 'message' => 'Vous dépassez les fonds monétaires du groupe.']);
            exit;
        }

        $imagePath = DEFAULT_GROUP_IMAGE;
        if (isset($_FILES['image']) && !empty($_FILES['image']['name'])) {
            $image = $_FILES['image'];
            $imageName = basename($image['name']);
            $imageTmpPath = $image['tmp_name'];
            $imageExtension = strtolower(pathinfo($imageName, PATHINFO_EXTENSION));

            // Valider l'extension de l'image
            $allowedExtensions = ['jpg', 'jpeg', 'png'];
            if (!in_array($imageExtension, $allowedExtensions)) {
                echo json_encode(['status' => 'error', 'message' => 'Format d\'image non valide.']);
                exit;
            }

            // Vérifier la taille de l'image
            $maxSize = 5 * 1024 * 1024; // 5 MB
            if ($image['size'] > $maxSize) {
                echo json_encode(['status' => 'error', 'message' => 'L\'image est trop grande.']);
                exit;
            }

            // Créer un dossier pour stocker les images des groupes
            $groupFolder = GROUP_IMAGES_PATH . preg_replace('/[^a-zA-Z0-9_]/', '_', $nomGroupe);
            if (!is_dir($groupFolder)) {
                if (!mkdir($groupFolder, 0775, true)) {
                    echo json_encode(['status' => 'error', 'message' => 'Erreur lors de la création du répertoire.']);
                    exit;
                }
            }

            // Générer un chemin unique pour l'image
            $imagePath = $groupFolder . '/' . uniqid() . '_' . $imageName;

            // Déplacer l'image téléchargée
            if (!move_uploaded_file($imageTmpPath, $imagePath)) {
                echo json_encode(['status' => 'error', 'message' => 'Erreur lors du téléchargement de l\'image.']);
                exit;
            }

            chmod($imagePath, 0664);

            // Convertir le chemin absolu en chemin relatif
            $imagePath = '../images/groupes/' . preg_replace('/[^a-zA-Z0-9_]/', '_', $nomGroupe) . '/' . basename($imagePath);
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
            ':grp_img' => $imagePath, // Utiliser le chemin relatif de l'image
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

        echo json_encode(['status' => 'success', 'message' => 'Groupe créé avec succès.']);
        exit;

    } catch (Exception $e) {
        error_log("Erreur : " . $e->getMessage());
        echo json_encode(['status' => 'error', 'message' => 'Erreur lors de la création du groupe : ' . $e->getMessage()]);
        exit;
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Merci de remplir le(s) thème(s) !']);
    exit;
}
?>