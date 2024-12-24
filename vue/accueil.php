<?php
session_start();
require_once("../config/connexion.php");
require_once("../modele/groupe.php");
require_once("../modele/membre.php");

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
    <div class="accueil" onclick="window.location.href='accueil.php';" style="cursor: pointer;">
        <img src="../images/logoVC.jpg" alt="Logo Voix Citoyenne"/>
        <h1>Voix Citoyenne</h1>
    </div>
    
    <!-- Menu des paramètres -->
        <div class="menu-parametres">

            <?php echo '<p class="username">' . 'Vous êtes connecté sous ' . '<b>' . $prenom . ' ' . $nom . '</b> </p> ' ?>
            <img src="../images/parametres.png" alt="Paramètres" class="parametres-icon"/>
            <ul class="menu-options">
                <li><a href="../controllers/logout.php">Se déconnecter</a></li>
                <li><a href="supprimer-compte.php">Supprimer mon compte</a></li>
                <li><a href="modifier-parametres.php">Modifier mes paramètres</a></li>
            </ul>
        </div>
    </header>

    <main>
        <h1>Bienvenue, <?php echo $prenom . ' ' . $nom; ?> !</h1>
        <p>Vous êtes maintenant connecté.</p>

        <section>
            <p>Bienvenue sur Voix Citoyenne, proposez des idées, débattez et votez ! Veillez à rester respectueux entre vous.</p>
        </section>

        <section>
            <h3>Liste de vos groupes :</h3>
            <ul>
                
                <?php
                Connexion::connect();
                $myGrp = Groupe::getGroupeById($id);
                $grp = Membre::getGrpById($id);
                ?>
                <?php
                if (isset($_SESSION['message'])) {
                    echo $_SESSION['message'];
                    // Une fois le message affiché, vous pouvez supprimer la session pour éviter qu'il s'affiche plusieurs fois
                    unset($_SESSION['message']);
                }
            ?>

                <?php
                // La liste de groupe dont il est propriétaire
                if (!empty($myGrp)) {
                    foreach ($myGrp as $listGrp) {
                        echo '<li>' . $listGrp->get('grp_nom') . ' ' . 
                             '<img src="' . $listGrp->get('grp_img') . '" alt="Logo ' . $listGrp->get('grp_nom') . '" class="image-small" />' .
                             '<div class="boutons-container">
                                 <form method="POST" action="../controllers/controleurSuppGroupe.php" style="display:inline;">
                                    <input type="hidden" name="group_id" value="' . $listGrp->get('grp_id') . '" />
                                    <button type="submit" name="delete_group" onclick="return confirm(\'Êtes-vous sûr de vouloir supprimer ce groupe ?\');">Supprimer</button>
                                 </form>
                                 <form method="POST" action="../controllers/controleurmodifGroupe.php" style="display:inline;">
                                    <input type="hidden" name="group_id" value="' . $listGrp->get('grp_id') .'" />
                                    <button type="submit" name="modify_group">Modifier</button>
                                </form>
                             </div>
                             </li>';
                    }
                }
                // La liste groupe en étant membre ou modérateur
                if (!empty($grp)) {
                    foreach ($grp as $listGrp) {
                        echo '<li>' . $listGrp->get('grp_nom') .'  '. '<img src="' . $listGrp->get('grp_img') . '" alt="Logo ' . $listGrp->get('grp_nom') . '"class="image-small" /></li>';
                    }
                } 

                // Si on a aucun groupe on affiche qu'il en a pas
                if (empty($myGrp)&&empty($grp)) {
                echo 'Aucun groupe trouvé.';
                }

                ?>
            </ul>
            <a href="creagroupe.php">
                <b><h3>Créer un groupe : </h3></b>
                <img src="../images/ajouter.png" alt="Créer un groupe" />
            </a>    

            
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
        <p>© 2024 Voix Citoyenne. Tous droits réservés.</p>
    </footer>

    <script src="script.js"></script>
</body>
</html>