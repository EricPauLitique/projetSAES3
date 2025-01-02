<?php
session_start();
require_once("../config/connexion.php");
require_once("../modele/utilisateur.php");
require_once("../modele/adresse.php");

// Vérifie si l'utilisateur est connecté
if (!isset($_SESSION['id'])) {
    header("Location: connexion.php");
    exit;
}

$idUtilisateur = htmlspecialchars($_SESSION['id']);

// Connexion à la base de données
Connexion::connect();

// Récupérer les informations de l'utilisateur
$utilisateur = Utilisateur::getUtilisateurByLogin($idUtilisateur);
$adresse = Adresse::getAdresseById($utilisateur->get('adr_id'));

// Utiliser les valeurs POST si elles existent
$prenom = htmlspecialchars($utilisateur->get('user_prenom'));
$nom = htmlspecialchars($utilisateur->get('user_nom'));
$email = htmlspecialchars($utilisateur->get('user_mail'));
$codePostal = htmlspecialchars($adresse->get('adr_cp'));
$ville = htmlspecialchars($adresse->get('adr_ville'));
$numeroRue = htmlspecialchars($adresse->get('adr_num'));
$nomRue = htmlspecialchars($adresse->get('adr_rue'));
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier votre compte</title>
    <link rel="stylesheet" href="../styles/modifCompte.css">
    <script>
        function togglePasswordSection() {
            var passwordSection = document.getElementById('password-section');
            if (passwordSection.style.display === 'none') {
                passwordSection.style.display = 'block';
            } else {
                passwordSection.style.display = 'none';
            }
        }

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
            const ancien_password = document.getElementById('ancien_password').value;
            const nouveau_password = document.getElementById('nouveau_password').value;
            const confirmer_password = document.getElementById('confirmer_password').value;
            const code_postal = document.getElementById('code_postal').value;
            const ville = document.getElementById('ville').value;
            const numero_rue = document.getElementById('numero_rue').value;
            const nom_rue = document.getElementById('nom_rue').value;

            const response = await fetch('../api.php?endpoint=modifcompte', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    prenom, nom, email, ancien_password, nouveau_password, confirmer_password,
                    code_postal, ville, numero_rue, nom_rue
                })
            });

            const result = await response.json();
            if (result.status === 'success') {
                window.location.href = 'accueil.php';
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
    <?php include 'header.php'; ?>

    <main>
        <div class="retour">    
            <img src="../images/retour.png" alt="retour" class="retour-icon"/>
            <a href="accueil.php">Retour</a>
        </div>
        <?php if (isset($_SESSION['messageC'])): ?>
            <div style="color: red;">
                <b>
                <?php echo $_SESSION['messageC']; ?>
                </b>
            </div>
        <?php unset($_SESSION['messageC']); endif; ?>
        
        <h2><b>Modification du compte</b></h2>
        <div id="error-message" style="color: red; font-weight: bold;"></div>
        <form onsubmit="handleSubmit(event)">
            <div class="form-group">
                <input type="text" id="prenom" name="prenom" placeholder="Prénom" value="<?php echo $prenom; ?>" required>
            </div>
            <div class="form-group">
                <input type="text" id="nom" name="nom" placeholder="Nom" value="<?php echo $nom; ?>" required>
            </div>
            <div class="form-group">
                <input type="email" id="email" name="email" placeholder="Adresse e-mail" value="<?php echo $email; ?>" required>
            </div>
            <div class="form-group password-container">
                <input type="password" id="ancien_password" name="ancien_password" placeholder="Ancien mot de passe" required>
                <img src="../images/eye-closed.png" alt="Afficher le mot de passe" id="ancien_password-eye" onclick="togglePasswordVisibility('ancien_password')">
            </div>
            <div class="form-group no-padding">
                <button type="button" class="no-padding" onclick="togglePasswordSection()">Voulez-vous modifier le mot de passe ?</button>
            </div>
            <div id="password-section" style="display: none;">
                <div class="form-group password-container">
                    <input type="password" id="nouveau_password" name="nouveau_password" placeholder="Nouveau mot de passe">
                    <img src="../images/eye-closed.png" alt="Afficher le mot de passe" id="nouveau_password-eye" onclick="togglePasswordVisibility('nouveau_password')">
                </div>
                <div class="form-group password-container">
                    <input type="password" id="confirmer_password" name="confirmer_password" placeholder="Confirmer le nouveau mot de passe">
                    <img src="../images/eye-closed.png" alt="Afficher le mot de passe" id="confirmer_password-eye" onclick="togglePasswordVisibility('confirmer_password')">
                </div>
            </div>
            <div class="form-group">
                <input type="number" id="code_postal" name="code_postal" placeholder="Code postal" value="<?php echo $codePostal; ?>" required pattern="\d{5}" title="En France, le code postal doit contenir exactement 5 chiffres">
            </div>
            <div class="form-group">
                <input type="text" id="ville" name="ville" placeholder="Ville" value="<?php echo $ville; ?>" required>
            </div>
            <div class="form-group">
                <input type="number" id="numero_rue" name="numero_rue" placeholder="Numéro de rue" value="<?php echo $numeroRue; ?>" required>
            </div>
            <div class="form-group">
                <input type="text" id="nom_rue" name="nom_rue" placeholder="Nom de la rue" value="<?php echo $nomRue; ?>" required>
            </div>
            <button type="submit">Modifier le compte</button>
        </form>
    </main>

    <?php include 'footer.php'; ?>
</body>
</html>