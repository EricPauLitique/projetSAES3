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
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion</title>
    <link href="../images/logoVC.ico" rel="shortcut icon" type="image/x-icon" />
    <link rel="stylesheet" href="../styles/connexionCompte.css">
    <script>
        function togglePasswordVisibility(id) {
            var passwordField = document.getElementById(id);
            var eyeIcon = document.getElementById(id + '-eye');
            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                eyeIcon.src = '../images/eye-open.png';
            } else {
                passwordField.type = 'password';
                eyeIcon.src = '../images/eye-closed.png';
            }
        }

        async function handleSubmit(event) {
            event.preventDefault();
            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;

            const response = await fetch('../api.php?endpoint=connexion', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ email, password })
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
       <h2><b>Connexion</b></h2>

        <?php if ($message): ?>
            <div id="error-message" style="color: red; font-weight: bold;"><?php echo $message; ?></div>
        <?php endif; ?>

        <div id="error-message" style="color: red; font-weight: bold;"></div>
               
        <form onsubmit="handleSubmit(event)">
            <div class="form-group">
                <input type="email" id="email" name="email" placeholder="Adresse e-mail" required>
            </div>
            <div class="form-group password-container">
                <input type="password" id="password" name="password" placeholder="Mot de passe" required>
                <img src="../images/eye-closed.png" alt="Afficher le mot de passe" id="password-eye" onclick="togglePasswordVisibility('password')">
            </div>
            <button type="submit">Connexion</button>
        </form>
        <p class="register-link">Vous n'avez pas de compte ? <a href="./creacompte.php"><b>Créez-en un</b></a></p>
    </main>

    <footer>
        <p>© 2024 Voix Citoyenne. Tous droits réservés.</p>
    </footer>

    <!-- Hidden element to store the redirect URL -->
    <div id="redirectUrl" data-url="<?php echo htmlspecialchars($redirectUrl); ?>" style="display: none;"></div>
</body>
</html>