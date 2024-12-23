<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion</title>
    <!-- Lien vers une feuille de style externe -->
    <link rel="stylesheet" href="../styles/connexionCompte.css">
</head> 
<body>
    <header>
        <div class="connexion" onclick="window.location.href='connexion.php';" style="cursor: pointer;">
            <img src="../images/logoVC.jpg" alt="Logo Voix Citoyenne"/>
            <h1>Voix Citoyenne</h1>
        </div>
    </header>

    <main>
        <h4><i>Chaque voix compte</i></h4>
        <?php if (!empty($error)): ?>
            <p style="color: red;"><b><?php echo htmlspecialchars($error); ?></b></p>
        <?php endif; ?>
        <form action="../controllers/controleurconnexion.php" method="POST">
            <div class="form-group">
                <input type="email" id="email" name="email" placeholder="Adresse e-mail" value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>" required>
            </div>
            <div class="form-group">
                <input type="password" id="password" name="password" placeholder="Mot de passe" required>
            </div>
            <button type="submit">Connexion</button>
        </form>
        <p class="register-link">Vous n'avez pas de compte ? <a href="/saes3-ese/projetSAES3/vue/creacompte.html">Créez-en un</a></p>
    </main>

    <footer>
        <p>© 2024 Voix Citoyenne. Tous droits réservés.</p>
    </footer>

    <!-- Lien vers un fichier JavaScript -->
    <script src="script.js"></script>
</body>
</html>