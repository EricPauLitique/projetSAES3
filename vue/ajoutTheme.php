<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['prenom']) || !isset($_SESSION['nom'])) {
    // Redirige vers la page de connexion si non connecté
    header("Location: connexion.php");
    exit;
}

$prenom = htmlspecialchars($_SESSION['prenom']);
$nom = htmlspecialchars($_SESSION['nom']);
$groupeId = isset($_GET['id']) ? intval($_GET['id']) : 0;
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter un thème</title>
    <link href="../images/logoVC.ico" rel="shortcut icon" type="image/x-icon" />
    <link rel="stylesheet" href="../styles/groupe.css">
</head>
<body>
<?php include 'header.php'; ?>

<main>
    <section>
        <div class="retour">    
            <img src="../images/retour.png" alt="retour" class="retour-icon"/>
            <a href="groupe.php?id=<?php echo $groupeId; ?>">Retour</a>
        </div>

        <br>

        <h1 id="titre">Ajouter un thème</h1>

        <?php
            if (isset($_SESSION['messageC'])) {
                echo $_SESSION['messageC'];
                unset($_SESSION['messageC']);
            }
        ?>

        <br>

        <!-- Formulaire pour créer un thème -->
        <form action="../controllers/controleurCreaTheme2.php" method="POST">
            <input type="hidden" name="group_id" value="<?php echo $groupeId; ?>">
            <label for="nom_du_theme">Nom du thème :</label>
            <input type="text" id="nom_du_theme" name="nom_du_theme" placeholder="Nom du thème" required>
            <br>
            <label for="limite_theme">Limite des propositions :</label>
            <input type="number" id="limite_theme" name="limite_theme" placeholder="Limite pour le thème" required>
            <br>
            <button type="submit">Créer le thème</button>
        </form>
    </section>
</main>

<?php include 'footer.php'; ?>

<script src="script.js"></script>
</body>
</html>