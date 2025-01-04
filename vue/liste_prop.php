<?php
// filepath: /Users/ericse/ProjetS3/projetSAES3/vue/liste_prop.php
require_once("../config/connexion.php");
require_once("../modele/groupe.php");
require_once("../modele/membre.php");

Connexion::connect();

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Vérifie si l'utilisateur est connecté
if (!isset($_SESSION['prenom']) || !isset($_SESSION['nom'])) {
    // Redirige vers la page de connexion si non connecté
    header("Location: connexion.php");
    exit;
}

$prenom = htmlspecialchars($_SESSION['prenom']);
$nom = htmlspecialchars($_SESSION['nom']);
$id = htmlspecialchars($_SESSION['id']);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des propositions</title>
    <link href="../images/logoVC.ico" rel="shortcut icon" type="image/x-icon" />
    <link rel="stylesheet" href="../styles/style.css">
</head>
<body>
    <?php include 'header.php'; ?>

    <main>
        <aside>
            <p>Liste des membres</p>
            <ul>
                <li><img src="../images/user.png" /><img src="../images/createur.png" />nom prénom 1 (créateur)</li>
                <li><img src="../images/user.png" />nom prénom 2</li>
                <li><img src="../images/user.png" />nom prénom 3</li>
            </ul>
        </aside>
        <!-- Contenu principal -->
        <section>
            <h2>Propositions</h2>
            <!-- Ajoutez ici le contenu des propositions -->
        </section>
    </main>

    <?php include 'footer.php'; ?>
    <script src="script.js"></script>
</body>
</html>