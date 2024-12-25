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
$prenom = isset($_POST['prenom']) ? htmlspecialchars($_POST['prenom']) : htmlspecialchars($utilisateur->get('user_prenom'));
$nom = isset($_POST['nom']) ? htmlspecialchars($_POST['nom']) : htmlspecialchars($utilisateur->get('user_nom'));
$email = isset($_POST['email']) ? htmlspecialchars($_POST['email']) : htmlspecialchars($utilisateur->get('user_mail'));
$codePostal = isset($_POST['code_postal']) ? htmlspecialchars($_POST['code_postal']) : htmlspecialchars($adresse->get('adr_cp'));
$ville = isset($_POST['ville']) ? htmlspecialchars($_POST['ville']) : htmlspecialchars($adresse->get('adr_ville'));
$numeroRue = isset($_POST['numero_rue']) ? htmlspecialchars($_POST['numero_rue']) : htmlspecialchars($adresse->get('adr_num'));
$nomRue = isset($_POST['nom_rue']) ? htmlspecialchars($_POST['nom_rue']) : htmlspecialchars($adresse->get('adr_rue'));

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
    </script>
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
        <form action="../controllers/controleurModifCompte.php" method="POST">
            <div class="form-group">
                <input type="text" id="prenom" name="prenom" placeholder="Prénom" value="<?php echo $prenom; ?>" required>
            </div>
            <div class="form-group">
                <input type="text" id="nom" name="nom" placeholder="Nom" value="<?php echo $nom; ?>" required>
            </div>
            <div class="form-group">
                <input type="email" id="email" name="email" placeholder="Adresse e-mail" value="<?php echo $email; ?>" required>
            </div>
            <div class="form-group">
                <input type="password" id="ancien_password" name="ancien_password" placeholder="Ancien mot de passe" required>
                <img src="../images/eye-closed.png" alt="Afficher le mot de passe" id="ancien_password-eye" onclick="togglePasswordVisibility('ancien_password')" style="cursor: pointer; width: 20px; vertical-align: middle;">
            </div>
            <div class="form-group no-padding">
                <button type="button" class="no-padding" onclick="togglePasswordSection()">Voulez-vous modifier le mot de passe ?</button>
            </div>
            <div id="password-section" style="display: none;">
                <div class="form-group">
                    <input type="password" id="nouveau_password" name="nouveau_password" placeholder="Nouveau mot de passe">
                    <img src="../images/eye-closed.png" alt="Afficher le mot de passe" id="nouveau_password-eye" onclick="togglePasswordVisibility('nouveau_password')" style="cursor: pointer; width: 20px; vertical-align: middle;">
                </div>
                <div class="form-group">
                    <input type="password" id="confirmer_password" name="confirmer_password" placeholder="Confirmer le nouveau mot de passe">
                    <img src="../images/eye-closed.png" alt="Afficher le mot de passe" id="confirmer_password-eye" onclick="togglePasswordVisibility('confirmer_password')" style="cursor: pointer; width: 20px; vertical-align: middle;">
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