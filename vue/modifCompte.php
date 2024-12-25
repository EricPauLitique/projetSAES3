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
                <input type="text" id="prenom" name="prenom" placeholder="Prénom" value="<?php echo htmlspecialchars($utilisateur->get('user_prenom')); ?>" required>
            </div>
            <div class="form-group">
                <input type="text" id="nom" name="nom" placeholder="Nom" value="<?php echo htmlspecialchars($utilisateur->get('user_nom')); ?>" required>
            </div>
            <div class="form-group">
                <input type="email" id="email" name="email" placeholder="Adresse e-mail" value="<?php echo htmlspecialchars($utilisateur->get('user_mail')); ?>" required>
            </div>
            <div class="form-group">
                <input type="password" id="ancien_password" name="ancien_password" placeholder="Ancien mot de passe" required>
            </div>
            <div class="form-group no-padding">
                <button type="button" class="no-padding" onclick="togglePasswordSection()">Voulez-vous modifier le mot de passe ?</button>
            </div>
            <div id="password-section" style="display: none;">
                <div class="form-group">
                    <input type="password" id="nouveau_password" name="nouveau_password" placeholder="Nouveau mot de passe">
                </div>
                <div class="form-group">
                    <input type="password" id="confirmer_password" name="confirmer_password" placeholder="Confirmer le nouveau mot de passe">
                </div>
            </div>
            <div class="form-group">
                <input type="number" id="code_postal" name="code_postal" placeholder="Code postal" value="<?php echo htmlspecialchars($adresse->get('adr_cp')); ?>" required pattern="\d{5}" title="En France, le code postal doit contenir exactement 5 chiffres">
            </div>
            <div class="form-group">
                <input type="text" id="ville" name="ville" placeholder="Ville" value="<?php echo htmlspecialchars($adresse->get('adr_ville')); ?>" required>
            </div>
            <div class="form-group">
                <input type="number" id="numero_rue" name="numero_rue" placeholder="Numéro de rue" value="<?php echo htmlspecialchars($adresse->get('adr_num')); ?>" required>
            </div>
            <div class="form-group">
                <input type="text" id="nom_rue" name="nom_rue" placeholder="Nom de la rue" value="<?php echo htmlspecialchars($adresse->get('adr_rue')); ?>" required>
            </div>
            <button type="submit">Modifier le compte</button>
        </form>
    </main>

    <?php include 'footer.php'; ?>
</body>
</html>