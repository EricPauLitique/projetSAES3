<?php
session_start();

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['prenom']) || !isset($_SESSION['nom'])) {
    // Redirige vers la page de connexion si non connecté
    header("Location: connexion.html");
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
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <a href="../controllers/logout.php">Se déconnecter</a>

        <img src="../images/logoVC.jpg" alt="Logo Voix Citoyenne" />
        <h1>Voix Citoyenne</h1>
        <img src="../images/parametres.png" alt="Paramètres" />
    </header>
    
    <main>
        <section>
            <!-- Formulaire pour créer un thème -->
            <form action="../controllers/controleurCreaTheme.php" method="POST">
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
            <?php
                    if (!empty($_SESSION['themes'])) {
                        foreach ($_SESSION['themes'] as $index => $theme) {
                            echo "<tr>";
                            echo "<td>" . htmlspecialchars($theme['theme_nom']) . "</td>";
                            echo "<td>" . htmlspecialchars($theme['limite_theme']) . "</td>";
                            // Formulaire de suppression
                            echo "<td>
                                <form action='../controllers/controleurCreaTheme.php' method='POST' onsubmit='return confirm(\"Voulez-vous vraiment supprimer ce thème ?\")'>
                                    <input type='hidden' name='delete_theme' value='$index'>
                                    <button type='submit'>Supprimer</button>
                                </form>
                            </td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='3'>Aucun thème créé pour le moment.</td></tr>";
                    }
                    ?>
</tbody>
        </table>
        </section>

        <section>
            <p>Créez votre groupe :</p>
            <form action="../controllers/controleurCreaGroupe.php" method="POST" enctype="multipart/form-data">
                <label for="nom_du_groupe">Nom du groupe :</label>
                <input type="text" id="nom_du_groupe" name="nom_du_groupe" placeholder="Nom du groupe" required>
                <br>
                <label for="theme_id">Sélectionnez un thème :</label>
                <select id="theme_id" name="theme_id" required>
                    <option value="" disabled selected>-- Choisir un thème --</option>
                    <?php foreach ($themes as $theme) : ?>
                        <option value="<?= htmlspecialchars($theme['theme_nom']) ?>">
                            <?= htmlspecialchars($theme['theme_nom']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <br>
                <label for="couleur">Couleur du groupe :</label>
                <input type="color" id="couleur" name="couleur" value="#ffffff" />
                <br>
                <label for="image">Image du groupe :</label>
                <input type="file" id="image" name="image" accept="image/png, image/jpeg" />
                <br>
                <label for="limite_annuelle">Limite annuelle :</label>
                <input type="number" id="limite_annuelle" name="limite_annuelle" placeholder="Limite annuelle" required>
                <br>
                <button type="submit">Créer le groupe</button>
            </form>
        </section>
    </main>
    
    <footer>
        <p>© 2024 MonSite. Tous droits réservés.</p>
    </footer>
    
    <script src="script.js"></script>
</body>
</html>