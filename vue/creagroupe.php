<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['prenom']) || !isset($_SESSION['nom'])) {
    // Redirige vers la page de connexion si non connecté
    header("Location: connexion.php");
    exit;
}

$prenom = htmlspecialchars($_SESSION['prenom']);
$nom = htmlspecialchars($_SESSION['nom']);

// Récupérer les thèmes depuis la session
$themes = isset($_SESSION['themes']) ? $_SESSION['themes'] : [];
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Création du groupe</title>
    <link href="../images/logoVC.ico" rel="shortcut icon" type="image/x-icon" />
    <link rel="stylesheet" href="../styles/creagroup.css">
    <script>
        async function handleThemeSubmit(event) {
            event.preventDefault();
            const nom_du_theme = document.getElementById('nom_du_theme').value;
            const limite_theme = document.getElementById('limite_theme').value;

            const response = await fetch('../api.php?endpoint=creatheme', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ nom_du_theme, limite_theme })
            });

            const result = await response.json();
            if (result.status === 'success') {
                window.location.reload();
            } else {
                document.getElementById('error-message').innerText = result.message;
            }
        }

        async function handleThemeDelete(index) {
            const response = await fetch('../api.php?endpoint=creatheme', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ delete_theme: index })
            });

            const result = await response.json();
            if (result.status === 'success') {
                window.location.reload();
            } else {
                document.getElementById('error-message').innerText = result.message;
            }
        }

        async function handleGroupSubmit(event) {
            event.preventDefault();
            const nom_du_groupe = document.getElementById('nom_du_groupe').value;
            const color = document.getElementById('color').value;
            const limite_annuelle = document.getElementById('limite_annuelle').value;
            const image = document.getElementById('image').files[0];

            const formData = new FormData();
            formData.append('nom_du_groupe', nom_du_groupe);
            formData.append('color', color);
            formData.append('limite_annuelle', limite_annuelle);
            formData.append('image', image);

            const response = await fetch('../api.php?endpoint=creagroupe', {
                method: 'POST',
                body: formData
            });

            const result = await response.json();
            if (result.status === 'success') {
                window.location.href = 'accueil.php';
            } else {
                document.getElementById('error-message').innerText = result.message;
            }
        }
    </script>
</head>
<body>
    <?php include 'header.php'; ?>

    <main>
        <section>
            <div class="retour">    
                <img src="../images/retour.png" alt="retour" class="retour-icon"/>
                <a href="accueil.php">Retour</a>
            </div>

            <br>

            <h1 id="titre">Création du groupe</h1>

            <div id="error-message" style="color: red; font-weight: bold;"></div>

            <?php
                if (isset($_SESSION['messageC'])) {
                    echo $_SESSION['messageC'];
                    unset($_SESSION['messageC']);
                }
            ?>
            <!-- Formulaire pour créer un thème -->
            <h2>Créer un thème : </h2>
            <form onsubmit="handleThemeSubmit(event)">
                <label for="nom_du_theme">Nom du thème :</label>
                <input type="text" id="nom_du_theme" name="nom_du_theme" placeholder="Nom du thème" required>
                <br>
                <label for="limite_theme">Limite des propositions :</label>
                <input type="number" id="limite_theme" name="limite_theme" placeholder="Limite pour le thème" required>
                <br>
                <button type="submit">Créer le thème</button>
            </form>

            <!-- Affichage des thèmes ajoutés -->
            <h3>Liste des thèmes créés :</h3>
            <table>
                <thead>
                    <tr>
                        <th>Nom du thème</th>
                        <th>Limite</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($themes)) : ?>
                        <?php foreach ($themes as $index => $theme) : ?>
                            <tr>
                                <td><?= htmlspecialchars($theme['theme_nom']) ?></td>
                                <td><?= htmlspecialchars($theme['limite_theme']) ?></td>
                                <td>
                                    <button type="button" onclick="handleThemeDelete(<?= $index ?>)">Supprimer</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <tr><td colspan="3">Aucun thème créé pour le moment.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </section>

        <section>
            <h2>Créer un groupe : </h2>
            <form onsubmit="handleGroupSubmit(event)" enctype="multipart/form-data">
                <label for="nom_du_groupe">Nom du groupe :</label>
                <input type="text" id="nom_du_groupe" name="nom_du_groupe" placeholder="Nom du groupe" required>
                <br>
                
                <div class="form-group couleur-container">
                    <label for="color">Couleur : </label>
                    <input type="color" id="color" name="color" required> 
                </div>
                <br>
                
                <label for="image">Image du groupe :</label>
                <input type="file" id="image" name="image" accept="image/png, image/jpeg">
                <br>
                
                <label for="limite_annuelle">Limite annuelle :</label>
                <input type="number" id="limite_annuelle" name="limite_annuelle" placeholder="Limite annuelle" required>
                <br>
                
                <button type="submit">Créer le groupe</button>
            </form>
        </section>
    </main>
    
    <?php include 'footer.php'; ?>
</body>
</html>