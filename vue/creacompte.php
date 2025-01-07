<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$message = '';
if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    unset($_SESSION['message']); // Supprime le message après l'affichage
}

$redirectUrl = $_SESSION['redirect_after_login'] ?? "../vue/accueil.php";

require_once(__DIR__ . "/../config/connexion.php");

// Utiliser les valeurs POST si elles existent
$prenom = isset($_POST['prenom']) ? htmlspecialchars($_POST['prenom']) : '';
$nom = isset($_POST['nom']) ? htmlspecialchars($_POST['nom']) : '';
$email = isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '';
$codePostal = isset($_POST['code_postal']) ? htmlspecialchars($_POST['code_postal']) : '';
$ville = isset($_POST['ville']) ? htmlspecialchars($_POST['ville']) : '';
$numeroRue = isset($_POST['numero_rue']) ? htmlspecialchars($_POST['numero_rue']) : '';
$nomRue = isset($_POST['nom_rue']) ? htmlspecialchars($_POST['nom_rue']) : '';
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Créer votre compte</title>
    <link href="../images/logoVC.ico" rel="shortcut icon" type="image/x-icon" />
    <link rel="stylesheet" href="../styles/creacompte.css">
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

        async function handleSubmit(event) {
            event.preventDefault();
            const prenom = document.getElementById('prenom').value;
            const nom = document.getElementById('nom').value;
            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;
            const confirm_password = document.getElementById('confirm_password').value;
            const code_postal = document.getElementById('code_postal').value;
            const ville = document.getElementById('ville').value;
            const numero_rue = document.getElementById('numero_rue').value;
            const nom_rue = document.getElementById('nom_rue').value;

            const response = await fetch('../api.php?endpoint=creacompte', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ prenom, nom, email, password, confirm_password, code_postal, ville, numero_rue, nom_rue })
            });

            const result = await response.json();
            if (result.status === 'success') {
                const redirectUrl = document.getElementById('redirectUrl').dataset.url;
                window.location.href = redirectUrl;
            } else {
                document.getElementById('error-message').innerText = result.message;
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
        <h2><b>Création du compte</b></h2>
        <?php if ($message): ?>
            <div id="success-message" style="color: green; font-weight: bold;"><?php echo $message; ?></div>
        <?php endif; ?>
        <div id="error-message" style="color: red; font-weight: bold;"></div>
        <form onsubmit="handleSubmit(event)">
            <div class="form-group">
                <input type="text" id="prenom" name="prenom" placeholder="Prénom" required>
            </div>
            <div class="form-group">
                <input type="text" id="nom" name="nom" placeholder="Nom" required>
            </div>
            <div class="form-group">
                <input type="email" id="email" name="email" placeholder="Adresse e-mail" required>
            </div>
            <div class="form-group password-container">
                <input type="password" id="password" name="password" placeholder="Mot de passe" required>
                <img src="../images/eye-closed.png" alt="Afficher le mot de passe" id="password-eye" onclick="togglePasswordVisibility('password')">
            </div>
            <div class="form-group password-container">
                <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirmer le mot de passe" required>
                <img src="../images/eye-closed.png" alt="Afficher le mot de passe" id="confirm_password-eye" onclick="togglePasswordVisibility('confirm_password')">
            </div>
            <div class="form-group">
                <input type="number" id="code_postal" name="code_postal" placeholder="Code postal" required pattern="\d{5}" title="En France, le code postal doit contenir exactement 5 chiffres">
            </div>
            <div class="form-group">
                <input type="text" id="ville" name="ville" placeholder="Ville" required>
            </div>
            <div class="form-group">
                <input type="number" id="numero_rue" name="numero_rue" placeholder="Numéro de rue" required>
            </div>
            <div class="form-group">
                <input type="text" id="nom_rue" name="nom_rue" placeholder="Nom de la rue" required>
            </div>
            <button type="submit">Créer un compte</button>
            <p>Vous avez un compte ? <a href="connexion.php" class="purple-link"><b>Connectez-vous</b></a></p>
        </form>
    </main>

    <footer>
        <p>© 2024 Voix Citoyenne. Tous droits réservés.</p>
    </footer>

    <!-- Hidden element to store the redirect URL -->
    <div id="redirectUrl" data-url="<?php echo htmlspecialchars($redirectUrl); ?>" style="display: none;"></div>
</body>
</html>