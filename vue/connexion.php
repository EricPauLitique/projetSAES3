<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion</title>
    <!-- Lien vers une feuille de style externe -->
    <link rel="stylesheet" href="../styles/connexionCompte.css">
    <script>
        function togglePasswordVisibility(id) {
            var passwordField = document.getElementById(id);
            var eyeIcon = document.getElementById(id + '-eye');
            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                eyeIcon.src = '../images/eye-open.png'; // Chemin vers l'icône d'œil ouvert
            } else {
                passwordField.type = 'password';
                eyeIcon.src = '../images/eye-closed.png'; // Chemin vers l'icône d'œil fermé
            }
        }
    </script>
        <style>
        .password-container {
            position: relative;
            display: flex;
            align-items: center;
        }
        .password-container input {
            flex: 1;
        }
        .password-container img {
            position: absolute;
            right: 10px;
            cursor: pointer;
            width: 20px;
            vertical-align: middle;
        }
    </style>
</head> 
<body>
    <header>
        <div class="connexion" onclick="window.location.href='connexion.php';" style="cursor: pointer;">
            <img src="../images/logoVC.jpg" alt="Logo Voix Citoyenne"/>
            <h1>Voix Citoyenne</h1>
        </div>
    </header>

    <main>
       <h2><b>Connexion</b></h2>

        <?php if (isset($_SESSION['messageC'])): ?>
            <div style="color: red; font-weight: bold;">
                <?php echo $_SESSION['messageC']; ?>
            </div>
            <?php unset($_SESSION['messageC']); ?>
        <?php endif; ?>
               
        <?php if (isset($_SESSION['message'])): ?>
            <div style="color: green; font-weight: bold;">
                <?php echo $_SESSION['message']; ?>
            </div>
            <?php unset($_SESSION['message']); ?>
        <?php endif; ?>
        
        <form action="../controllers/controleurconnexion.php" method="POST">
            <div class="form-group">
                <input type="email" id="email" name="email" placeholder="Adresse e-mail" value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>" required>
            </div>
            <div class="form-group password-container">
                <input type="password" id="password" name="password" placeholder="Mot de passe" required>
                <img src="../images/eye-closed.png" alt="Afficher le mot de passe" id="password-eye" onclick="togglePasswordVisibility('password')">
            </div>
            <button type="submit">Connexion</button>
        </form>
        <p class="register-link">Vous n'avez pas de compte ? <a href="/saes3-ese/projetSAES3/vue/creacompte.php"><b>Créez-en un</b></a></p>
    </main>

    <footer>
        <p>© 2024 Voix Citoyenne. Tous droits réservés.</p>
    </footer>

    <!-- Lien vers un fichier JavaScript -->
    <script src="script.js"></script>
</body>
</html>