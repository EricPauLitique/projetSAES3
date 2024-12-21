<?php
require_once '../config/connexion.php';
Connexion::connect();
$pdo = Connexion::PDO();

session_start();

$nomGroupe = filter_input(INPUT_POST, 'nom_du_groupe', FILTER_SANITIZE_STRING);
$themeId = filter_input(INPUT_POST, 'theme_id', FILTER_VALIDATE_INT);
$couleur = filter_input(INPUT_POST, 'couleur', FILTER_SANITIZE_STRING);
$limiteAnnuelle = filter_input(INPUT_POST, 'limite_annuelle', FILTER_VALIDATE_INT);
$idUtilisateur = htmlspecialchars($_SESSION['id']);
$sommeMonetaire = 0;

// Vérifier si des thèmes sont stockés dans la session
echo $idUtilisateur;

foreach ($_SESSION['themes'] as $theme) {
    $themeNom = $theme['theme_nom'];
}

if ($sommeMonetaire > $limiteAnnuelle ) {

}


if (isset($_SESSION['themes']) && !empty($_SESSION['themes'])) {
    try {

        // Vérifier si elle existe
        $stmt = $pdo->prepare("select COUNT(*) from theme Where theme_nom = :theme_nom");
        foreach ($_SESSION['themes'] as $theme) {
            $themeNom = $theme['theme_nom'];

            // Exécuter la requête pour insérer chaque thème
            $stmt->execute(['theme_nom' => $themeNom]);

            $id = $stmt->fetchColumn();

            if ($id==0) {
                $stmt = $pdo->prepare("select MAX(theme_id) from theme");
                $stmt->execute();
                $maxIdTheme= $stmt->fetchColumn();
                $resultIdTheme = $maxIdTheme + 1; 

                $stmt = $pdo->prepare("INSERT INTO theme (theme_id, theme_nom) VALUES (:theme_id, :theme_nom)");

                // Exécuter la requête pour insérer chaque thème
                $stmt->execute(['theme_id' => $resultIdTheme], ['theme_nom' => $themeNom]);

            }

            else {
                $stmt = $pdo->prepare("select theme_id from theme Where theme_nom = :theme_nom");
                $stmt->execute(['theme_nom' => $themeNom]);
                $idTheme= $stmt->fetchColumn();
                

            }

            



        } 

    } catch (Exception $e) {
        echo "Erreur lors de l'insertion des thèmes : " . $e->getMessage();
    }
}

        /*
        // Préparer la requête d'insertion des thèmes
        $stmt = $pdo->prepare("INSERT INTO theme (theme_nom) VALUES (:theme_nom)");

        // Parcourir chaque thème de la session et l'insérer dans la base de données
        foreach ($_SESSION['themes'] as $theme) {
            $themeNom = $theme['theme_nom'];

            // Exécuter la requête pour insérer chaque thème
            $stmt->execute(['theme_nom' => $themeNom]);
        }

        echo "Les thèmes ont été insérés avec succès dans la base de données.";

        // Vider la liste des thèmes en session après insertion
        unset($_SESSION['themes']);
        
    } catch (Exception $e) {
        echo "Erreur lors de l'insertion des thèmes : " . $e->getMessage();
    }
}

// Vérifier si des limites sont stockées dans la session
if (isset($_SESSION['limites']) && !empty($_SESSION['limites'])) {
    try {
        // Préparer la requête d'insertion des limites
        $stmt = $pdo->prepare("INSERT INTO limite (theme_id, limite_theme) VALUES (:theme_id, :limite_theme)");

        // Parcourir chaque limite de la session et l'insérer dans la base de données
        foreach ($_SESSION['limites'] as $limite) {
            $themeId = $limite['theme_id'];
            $limiteTheme = $limite['limite_theme'];

            // Exécuter la requête pour insérer chaque limite
            $stmt->execute(['theme_id' => $themeId, 'limite_theme' => $limiteTheme]);
        }

        echo "Les limites ont été insérées avec succès dans la base de données.";

        // Vider la liste des limites en session après insertion
        unset($_SESSION['limites']);
        
    } catch (Exception $e) {
        echo "Erreur lors de l'insertion des limites : " . $e->getMessage();
    }
}
?>

<?php
require_once '../config/connexion.php';
Connexion::connect();
$pdo = Connexion::PDO();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nomGroupe = filter_input(INPUT_POST, 'nom_du_groupe', FILTER_SANITIZE_STRING);
    $themeId = filter_input(INPUT_POST, 'theme_id', FILTER_VALIDATE_INT);
    $couleur = filter_input(INPUT_POST, 'couleur', FILTER_SANITIZE_STRING);
    $limiteAnnuelle = filter_input(INPUT_POST, 'limite_annuelle', FILTER_VALIDATE_INT);

    if (empty($nomGroupe) || !$themeId || !$limiteAnnuelle) {
        echo "Tous les champs requis doivent être remplis.";
        exit;
    }

    try {
        // Vérifie si le thème existe
        $stmt = $pdo->prepare("SELECT theme_id FROM theme WHERE theme_id = :theme_id");
        $stmt->execute(['theme_id' => $themeId]);
        if (!$stmt->fetch()) {
            echo "Le thème sélectionné n'existe pas.";
            exit;
        }

        // Gestion du fichier image
        $imagePath = null;
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $image = $_FILES['image'];
            $imageName = basename($image['name']);
            $imageTmpPath = $image['tmp_name'];
            $imageExtension = strtolower(pathinfo($imageName, PATHINFO_EXTENSION));

            // Valide l'extension de l'image
            $allowedExtensions = ['jpg', 'jpeg', 'png'];
            if (!in_array($imageExtension, $allowedExtensions)) {
                echo "Format d'image non valide. Formats acceptés : jpg, jpeg, png.";
                exit;
            }

            // Vérification de la taille de l'image
            $maxSize = 5 * 1024 * 1024; // 5 MB
            if ($image['size'] > $maxSize) {
                echo "L'image est trop grande. La taille maximale est de 5 Mo.";
                exit;
            }

            // Crée un dossier pour le groupe
            $groupFolder = '../images/groupes/' . preg_replace('/[^a-zA-Z0-9_]/', '_', $nomGroupe);
            if (!is_dir($groupFolder)) {
                mkdir($groupFolder, 0755, true);
            }

            // Déplace l'image dans le dossier du groupe
            $imagePath = $groupFolder . '/' . $imageName;
            if (file_exists($imagePath)) {
                echo "Un fichier avec ce nom existe déjà. Veuillez renommer l'image.";
                exit;
            }

            if (!move_uploaded_file($imageTmpPath, $imagePath)) {
                echo "Erreur lors du téléchargement de l'image.";
                exit;
            }
        }

        // Insère le groupe dans la base de données
        $stmt = $pdo->prepare("
            INSERT INTO groupe (groupe_nom, theme_id, couleur, image, limite_annuelle) 
            VALUES (:nom, :theme_id, :couleur, :image, :limite_annuelle)
        ");
        $stmt->execute([
            'nom' => $nomGroupe,
            'theme_id' => $themeId,
            'couleur' => $couleur,
            'image' => $imagePath,
            'limite_annuelle' => $limiteAnnuelle
        ]);

        echo "Le groupe a été créé avec succès.";
        header("Location: ../vue/accueil.php");
        exit;

    } catch (Exception $e) {
        echo "Erreur lors de la création du groupe : " . $e->getMessage();
    }
}*/
?>