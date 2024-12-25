<?php
session_start();

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['prenom']) || !isset($_SESSION['nom'])) {
    header("Location: connexion.php");
    exit;
}

// Récupérer les informations de l'utilisateur depuis la session
$prenom = htmlspecialchars($_SESSION['prenom']);
$nom = htmlspecialchars($_SESSION['nom']);

// Variables du groupe pour préremplir le formulaire
$nomGroupe = isset($_SESSION['nomGroupe']) ? $_SESSION['nomGroupe'] : '';
$couleur = isset($_SESSION['couleur']) ? $_SESSION['couleur'] : '#000000';
$limiteAnnuelle = isset($_SESSION['limiteAnnuelle']) ? $_SESSION['limiteAnnuelle'] : '';
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modification du groupe</title>
    <link rel="stylesheet" href="../styles/modifgroup.css">
</head>
<body>
    <?php include 'header.php'; ?>

    <main>
        <div class="retour">    
            <img src="../images/retour.png" alt="retour" class="retour-icon"/>
            <a href="accueil.php">Retour</a>
        </div>

        <h1 id="titre">Modifier le groupe</h1>
        <br>

        <!-- Afficher le message d'erreur s'il existe -->
        <?php if (isset($_SESSION['messageC'])): ?>
            <div style="color: red;">
                <b><?php echo $_SESSION['messageC']; ?></b>
            </div>
            <?php unset($_SESSION['messageC']); ?>
        <?php endif; ?>

        <!-- Formulaire de modification du groupe -->
        <form action="../controllers/controleurmodifGroupeModif.php" method="POST" enctype="multipart/form-data">
            <!-- Champ pour le nom du groupe -->
            <label for="nom_du_groupe">Nom du groupe :</label>
            <input type="text" id="nom_du_groupe" name="nom_du_groupe" value="<?php echo $nomGroupe; ?>" required><br>

            <!-- Champ pour la couleur du groupe -->
            <label for="color">Couleur :</label>
            <input type="color" id="color" name="color" value="<?php echo $couleur; ?>"><br>

            <!-- Champ pour la limite annuelle -->
            <label for="limite_annuelle">Limite annuelle :</label>
            <input type="number" id="limite_annuelle" name="limite_annuelle" value="<?php echo $limiteAnnuelle; ?>" required><br>

            <!-- Champ pour uploader une nouvelle image -->
            <div class="image-upload-container">
                <div class="image-upload-row">
                    <label for="image">Image :</label>
                    <input type="file" id="image" name="image" accept="image/*">
                </div>
                <br>
                <label for="image">Actuellement, votre image dans le groupe est :</label>
                <div class="image-upload-row">
                    
                    <br>
                    <span>
                        <?php
                        if (isset($_SESSION['image_name']) && !empty($_SESSION['image_name'])) {
                            echo htmlspecialchars($_SESSION['image_name']);
                        } else {
                            echo 'Aucun fichier choisi';
                        }
                        ?>
                    </span>
                    <?php
                        if (isset($_SESSION['image_name']) && !empty($_SESSION['image_name'])) {
                            $imageSrc = isset($_SESSION['image_name']) && !empty($_SESSION['image_name']) ? '../images/groupes/' . $_SESSION['image_name'] : '../images/groupes/default.png';
                            echo '<img id="image-preview" src="' . htmlspecialchars($imageSrc) . '" alt="Aperçu de l\'image" style="max-width: 100px; display: block;">';
                            echo '<button type="submit" name="remove_image" value="1" class="btn-remove">Supprimer l\'image</button>';
                        }
                        ?>
                </div>
            </div><br>

            <button type="submit">Valider la modification</button>
        </form>
    </main>

    <?php include 'footer.php'; ?>
</body>
</html>