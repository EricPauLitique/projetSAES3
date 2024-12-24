<?php
// creagroupe.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

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
    <link rel="stylesheet" href="../styles/creagroup.css">

</head>
<body>
<header>
    <div class="accueil" onclick="window.location.href='accueil.php';" style="cursor: pointer;">
        <img src="../images/logoVC.jpg" alt="Logo Voix Citoyenne"/>
        <h1>Voix Citoyenne</h1>
    </div>
    
    <!-- Menu des paramètres -->
        <div class="menu-parametres">
            <?php echo '<p class="username">' . 'Vous êtes connecté sous ' . '<b>' . $prenom . ' ' . $nom . '</b> </p> ' ?>
            <img src="../images/parametres.png" alt="Paramètres" class="parametres-icon"/>
            <ul class="menu-options">
                <li><a href="../controllers/logout.php">Se déconnecter</a></li>
                <li><a href="supprimer-compte.php">Supprimer mon compte</a></li>
                <li><a href="modifier-parametres.php">Modifier mes paramètres</a></li>
            </ul>
        </div>
    </header>
    
    <main>
        <section>
        <div class="retour">    
            <img src="../images/retour.png" alt="retour" class="retour-icon"/>
            <a href="accueil.php">Retour</a>
        </div>

        <h1 id="titre">Création du groupe</h1>


            <?php
                if (isset($_SESSION['messageC'])) {
                    echo $_SESSION['messageC'];
                    // Une fois le message affiché, vous pouvez supprimer la session pour éviter qu'il s'affiche plusieurs fois
                    unset($_SESSION['messageC']);
                }
            ?>
            <!-- Formulaire pour créer un thème -->
            <h2>Créer un thème : </h2>
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
                    <?php if (!empty($themes)) : ?>
                        <?php foreach ($themes as $index => $theme) : ?>
                            <tr>
                                <td><?= htmlspecialchars($theme['theme_nom']) ?></td>
                                <td><?= htmlspecialchars($theme['limite_theme']) ?></td>
                                <td>
                                    <form action="../controllers/controleurCreaTheme.php" method="POST" onsubmit="return confirm('Voulez-vous vraiment supprimer ce thème ?');">
                                        <input type="hidden" name="delete_theme" value="<?= $index ?>">
                                        <button type="submit">Supprimer</button>
                                    </form>
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
            <form action="../controllers/controleurCreaGroupe.php" method="POST" enctype="multipart/form-data">
            <label for="nom_du_groupe">Nom du groupe :</label>
            <input type="text" id="nom_du_groupe" name="nom_du_groupe" placeholder="Nom du groupe" 
                value="<?php echo isset($_SESSION['nomGroupe']) ? htmlspecialchars($_SESSION['nomGroupe']) : ''; ?>" required>
            <br>
            
            <div class="form-group couleur-container">
                <label for="color">Couleur : </label>
                <input type="color" id="color" name="color" 
                    value="<?php echo isset($_SESSION['couleur']) ? htmlspecialchars($_SESSION['couleur']) : ''; ?>"> 
            </div>
            <br>
            
            <label for="image">Image du groupe :</label>
            <input type="file" id="image" name="image" accept="image/png, image/jpeg">
            <br>
            
            <label for="limite_annuelle">Limite annuelle :</label>
            <input type="number" id="limite_annuelle" name="limite_annuelle" placeholder="Limite annuelle" 
                value="<?php echo isset($_SESSION['limiteAnnuelle']) ? htmlspecialchars($_SESSION['limiteAnnuelle']) : ''; ?>" required>
            <br>
            
            <button type="submit">Créer le groupe</button>
        </form>
        </section>
    </main>
    
    <footer>
        <p>© 2024 Voix Citoyenne. Tous droits réservés.</p>
    </footer>
    
    <script src="script.js"></script>
</body>
</html>