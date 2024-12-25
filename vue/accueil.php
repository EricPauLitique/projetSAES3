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
    <?php include 'header.php'; ?>

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

                <?php if (isset($_SESSION['messageC'])): ?>
                    <div style="color: red;">
                        <b>
                        <?php echo $_SESSION['messageC']; ?>
                        </b>
                    </div>
                <?php unset($_SESSION['messageC']); endif; ?>
        
                <?php if (isset($_SESSION['message'])): ?>
                    <div style="color: green;">
                        <b>
                        <?php echo $_SESSION['message']; ?>
                        </b>
                    </div>
                <?php unset($_SESSION['message']); endif; ?>

                <?php
                // La liste de groupe dont il est propriétaire
                if (!empty($myGrp)) {
                    foreach ($myGrp as $listGrp) {
                        $couleur = htmlspecialchars($listGrp->get('grp_couleur'));
                        echo '<li>
                                <div class="group-item">
                                    <div class="group-image">
                                        <a href="groupe.php?id=' . $listGrp->get('grp_id') . '">
                                            <img src="' . $listGrp->get('grp_img') . '" alt="Logo ' . $listGrp->get('grp_nom') . '" class="image-small" />
                                        </a>
                                    </div>
                                       <b><h2 style="color: ' . $couleur . ';"><a href="groupe.php?id=' . $listGrp->get('grp_id') . '">' . strtoupper($listGrp->get('grp_nom')) . '</a></h2></b>
                                </div>
                                <div class="boutons-container">
                                    <form method="POST" action="../controllers/controleurmodifGroupe.php" style="display:inline;">
                                        <input type="hidden" name="group_id" value="' . $listGrp->get('grp_id') .'" />
                                        <button type="submit" name="modify_group" class="btn-modify">Modifier</button>
                                    </form>
                                    <form method="POST" action="../controllers/controleurSuppGroupe.php" style="display:inline;">
                                        <input type="hidden" name="group_id" value="' . $listGrp->get('grp_id') . '" />
                                        <button type="submit" name="delete_group" class="btn-delete" onclick="return confirm(\'Êtes-vous sûr de vouloir supprimer ce groupe ?\');">Supprimer</button>
                                    </form>
                                </div>
                              </li>';
                    }
                }
                // La liste groupe en étant membre ou modérateur
                if (!empty($grp)) {
                    foreach ($grp as $listGrp) {
                        $couleur = htmlspecialchars($listGrp->get('grp_couleur'));
                        echo '<li>
                                <div class="group-item">
                                    <div class="group-image">
                                        <a href="groupe.php?id=' . $listGrp->get('grp_id') . '">
                                            <img src="' . $listGrp->get('grp_img') . '" alt="Logo ' . $listGrp->get('grp_nom') . '" class="image-small" />
                                        </a>
                                    </div>
                                </div>
                                    <div class="group-text" style="color: ' . $couleur . ';">
                                        <a href="groupe.php?id=' . $listGrp->get('grp_id') . '">' . strtoupper($listGrp->get('grp_nom')) . '</a>
                                    </div>
                                    <div class="boutons-container">
                                        <form method="POST" action="../controllers/controleurQuitterGroupe.php" style="display:inline;">
                                            <input type="hidden" name="user_id" value="' . $id . '">
                                            <input type="hidden" name="grp_id" value="' . $listGrp->get('grp_id') . '">
                                            <button type="submit" class="btn-delete" onclick="return confirm(\'Êtes-vous sûr de vouloir quitter ce groupe ?\');">Quitter</button>
                                        </form>
                                    </div>
                                
                              </li>';
                    }
                }

                // Si on a aucun groupe on affiche qu'il en a pas
                if (empty($myGrp) && empty($grp)) {
                    echo '<b> Aucun groupe trouvé. </b>';
                }
                ?>
            </ul>
            <a href="creagroupe.php">
                <b><p>Créer un groupe : </p></b>
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

    <?php include 'footer.php'; ?>
</body>
</html>