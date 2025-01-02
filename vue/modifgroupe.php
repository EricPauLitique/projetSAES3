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
    <title>Modifier le groupe</title>
    <link href="../images/logoVC.ico" rel="shortcut icon" type="image/x-icon" />
    <link rel="stylesheet" href="../styles/modifgroupe.css">
    <script>
        async function handleSubmit(event) {
            event.preventDefault();

            const groupId = document.getElementById('group_id').value;
            const nomGroupe = document.getElementById('nom_du_groupe').value;
            const couleur = document.getElementById('color').value;
            const limiteAnnuelle = document.getElementById('limite_annuelle').value;
            const image = document.getElementById('image').files[0];
            const removeImage = document.getElementById('remove_image').checked ? 1 : 0;

            const formData = new FormData();
            formData.append('group_id', groupId);
            formData.append('nom_du_groupe', nomGroupe);
            formData.append('color', couleur);
            formData.append('limite_annuelle', limiteAnnuelle);
            formData.append('image', image);
            formData.append('remove_image', removeImage);

            const response = await fetch('../api.php?endpoint=modifgroupeModif', {
                method: 'POST',
                body: formData
            });

            const result = await response.json();
            if (result.status === 'success') {
                window.location.href = '../vue/accueil.php';
            } else {
                document.getElementById('error-message').innerText = result.message;
            }
        }
    </script>
</head>
<body>
    <header>
        <div class="connexion" onclick="window.location.href='connexion.php';" style="cursor: pointer;">
            <img src="../images/logoVC.jpg" alt="Logo Voix Citoyenne"/>
            <h1>Voix Citoyenne</h1>
        </div>
    </header>

    <main>
        <h2><b>Modifier le groupe</b></h2>
        <div id="error-message" style="color: red; font-weight: bold;"></div>
        <form onsubmit="handleSubmit(event)">
            <input type="hidden" id="group_id" name="group_id" value="<?php echo $_SESSION['group_id']; ?>">
            <div class="form-group">
                <label for="nom_du_groupe">Nom du groupe :</label>
                <input type="text" id="nom_du_groupe" name="nom_du_groupe" value="<?php echo $_SESSION['nomGroupe']; ?>" required>
            </div>
            <div class="form-group">
                <label for="color">Couleur :</label>
                <input type="color" id="color" name="color" value="<?php echo $_SESSION['couleur']; ?>" required>
            </div>
            <div class="form-group">
                <label for="limite_annuelle">Limite annuelle :</label>
                <input type="number" id="limite_annuelle" name="limite_annuelle" value="<?php echo $_SESSION['limiteAnnuelle']; ?>" required>
            </div>
            <div class="form-group">
                <label for="image">Image :</label>
                <input type="file" id="image" name="image">
                <?php if (!empty($_SESSION['image_name'])): ?>
                    <p>Image actuelle : <?php echo $_SESSION['image_name']; ?></p>
                    <label for="remove_image">Supprimer l'image actuelle</label>
                    <input type="checkbox" id="remove_image" name="remove_image" value="1">
                <?php endif; ?>
            </div>
            <button type="submit">Modifier le groupe</button>
        </form>
    </main>

    <footer>
        <p>© 2024 Voix Citoyenne. Tous droits réservés.</p>
    </footer>
</body>
</html>