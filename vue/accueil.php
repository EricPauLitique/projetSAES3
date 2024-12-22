<?php
session_start();
require_once("../config/connexion.php");
require_once("../modele/groupe.php");

// Vérifie si l'utilisateur est connecté
if (!isset($_SESSION['prenom']) || !isset($_SESSION['nom'])) {
    // Redirige vers la page de connexion si non connecté
    header("Location: connexion.html");
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
    <title>Accueil</title>
    <link href="../images/logoVC.ico" rel="shortcut icon" type="image/x-icon" />
    <link rel="stylesheet" href="../styles/accueil.css">
</head>
<body>
    <header>
        <img src="../images/logoVC.jpg" alt="Logo Voix Citoyenne"/>
        <h1>Voix Citoyenne</h1>
        <img src="../images/parametres.png" alt="Paramètres"/>
    </header>

    <main>
        <h1>Bienvenue, <?php echo $prenom . ' ' . $nom; ?> !</h1>
        <p>Vous êtes maintenant connecté.</p>
        <a href="../controllers/logout.php">Se déconnecter</a>

        <section>
            <p>Bienvenue sur Voix Citoyenne, proposez des idées, débattez et votez ! Veillez à rester respectueux entre vous.</p>
        </section>

        <section>
            <h3>Liste de vos groupes :</h3>
            <ul>
                <?php
                Connexion::connect();
                $grp = Groupe::getGroupeById($id);
                
                if (!empty($grp)) {
                    foreach ($grp as $listGrp) {
                        echo '<li>' . $listGrp->get('grp_nom') .'  '. '<img src="' . $listGrp->get('grp_img') . '" alt="Logo ' . $listGrp->get('grp_nom') . '"class="image-small" /></li>';
                    }
                } else {
                    echo 'Aucun groupe trouvé.';
                }

                ?>
            </ul>
            <img src="../images/ajouter.png" alt="Créer un groupe" href="creagroupe.php"/>
            <a href="creagroupe.php">Creer un groupe</a>

            <p>Créer un groupe</p>
        </section>

        <section>
            <h3>Notifications :</h3>
            <ul>
                <li>Notif 1</li>
                <li>Notif 2</li>
                <li>Notif 3</li>
            </ul>
        </section>
    </main>

    <footer>
        <p>© 2024 MonSite. Tous droits réservés.</p>
    </footer>

    <script src="script.js"></script>
</body>
</html>