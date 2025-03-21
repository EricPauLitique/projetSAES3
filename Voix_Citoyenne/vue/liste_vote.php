<?php
// filepath: /Users/ericse/ProjetS3/projetSAES3/vue/liste_prop.php
require_once("../config/connexion.php");
require_once("../modele/groupe.php");
require_once("../modele/membre.php");
require_once("../modele/proposition.php");
require_once("../modele/vote.php");

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

// Récupérer l'ID du groupe depuis l'URL
$groupeId = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($groupeId === 0) {
    // Redirige vers la page d'accueil si l'ID du groupe est invalide
    $_SESSION['messageC'] = "L'ID du groupe est invalide.";
    header("Location: accueil.php");
    exit;
}

// Connexion à la base de données
Connexion::connect();

// Récupérer les informations du groupe
$groupe = Groupe::getGroupByIdUnique2($groupeId);
if (!$groupe) {
    // Redirige vers la page d'accueil si le groupe n'existe pas
    $_SESSION['messageC']= "Le groupe n'a pas été trouvé.";
    header("Location: accueil.php");
    exit;
}

if (Membre::siMembreInconnu($id, $groupeId) == 0 && Groupe::siProprioInconnu($id, $groupeId) == 0) {
    // Redirige vers la page d'accueil si l'utilisateur n'est pas membre du groupe
    $message = "Vous n'êtes pas autorisé à aller dans le groupe.";
    $_SESSION['messageC'] = $message;
    header("Location: accueil.php");
    exit;
} 

// Vérifier si l'utilisateur est le propriétaire du groupe
$isProprietaire = Groupe::siProprioInconnu($id, $groupeId) == 1;
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des votes</title>
    <link href="../images/logoVC.ico" rel="shortcut icon" type="image/x-icon" />
    <link rel="stylesheet" href="../styles/prop.css">
</head>
<body>
    <?php include 'header.php'; ?>
 

    <main>
        <div class="retour">    
            <img src="../images/retour.png" alt="retour" class="retour-icon"/>
            <a href="accueil.php">Retour</a>
        </div>
        <h1 id="titreGroupe">
            <img src="<?php echo htmlspecialchars($groupe->get('grp_img')); ?>" alt="Image associée" class="image-gauche">
            <?php echo 'Groupe :&nbsp;<b><i><u>' . htmlspecialchars($groupe->get('grp_nom')) . '</u></i></b>'; ?>
        </h1>

        <nav>
            <ul>
                <li><a href="groupe.php?id=<?php echo $groupeId; ?>" >Groupe</a></li>
                <li><a href="liste_prop.php?id=<?php echo $groupeId; ?>">Propositions</a></li>
                <li><a href="liste_vote.php?id=<?php echo $groupeId; ?>" class="active">Vote</a></li>
            </ul>
        </nav> 
        <aside>
            <h3>Liste des membres :</h3>
            <table>
                <thead>
                    <tr>
                        <th>Photo</th>
                        <th>Prénom/Nom</th>
                        <th>Rôle</th>
                        <?php if ($isProprietaire) { ?>
                            <th>Actions</th>
                        <?php } ?>
                    </tr>
                </thead>
                <tbody>

                    <?php
                    // Récupérer les membres du groupe
                    $proprio = Groupe::getProprio($groupeId);
                    $membres = Membre::getMembresByGroupeId($groupeId); // Assurez-vous que cette méthode existe dans votre modèle Membre

                    if ($proprio) {
                        $prenomProprio = htmlspecialchars($proprio['user_prenom']);
                        $nomProprio = htmlspecialchars($proprio['user_nom']);
                        echo '<tr>
                                <td>
                                    <img src="../images/createur.png" alt="Créateur" class="image-small" />
                                </td>
                                <td>' . $prenomProprio . ' ' . $nomProprio . '</td>
                                <td>Créateur</td>';
                        if ($isProprietaire) {
                            echo '<td> Vous ne pouvez pas vous retirer vous-même. </td>';
                        }
                        echo '</tr>';
                    }

                    if (!empty($membres)) {
                        foreach ($membres as $membre)  {
                            echo '<tr>';
                            if ($membre->get('role') == 'Modérateur') {
                                echo '<td><img src="../images/user.png" alt="Moderateur" class="image-small" /></td>';
                            } else {
                                echo '<td><img src="../images/user.png" alt="Membre" class="image-small" /></td>';
                            }
                            echo '<td>' . htmlspecialchars($membre->get('user_prenom')) . ' ' . htmlspecialchars($membre->get('user_nom')) . '</td>
                                  <td>' . htmlspecialchars($membre->get('role')) . '</td>';
                            if ($isProprietaire) {
                                echo '<td>
                                        <form method="POST" action="../controllers/controleurSupprimerMembre.php">
                                            <input type="hidden" name="user_id" value="' . $membre->get('user_id') . '">
                                            <input type="hidden" name="grp_id" value="' . $groupeId . '" />
                                            <button type="submit" class="btn-delete" onclick="return confirm(\'Êtes-vous sûr de vouloir supprimer ce membre ?\');">Supprimer</button>
                                        </form>
                                      </td>';
                            }
                            echo '</tr>';
                        }
                    }
                    ?>
                </tbody>
            </table>
        </aside>
        

        <!-- Contenu principal -->
        <section>
            <h2>Votes</h2>
            <button onclick="window.location.href='creaVote.php?id=<?php echo $groupeId; ?>';">Créer un vote</button>
            <table>
                <thead>
                    <tr>
                        <th>Nom du Vote</th>
                        <th>Durée</th>
                        <th>Valide</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php            
                    $lesVotes = Vote::getVotesByGroupeId($groupeId);    
                    foreach ($lesVotes as $vote) {
                        $voteId = $vote->get('vote_id'); 
                        $voteTitre = htmlspecialchars($vote->get('vote_type_scrutin'));
                        $voteDuree = htmlspecialchars($vote->get('vote_duree'));
                        $voteValide = htmlspecialchars($vote->get('vote_valide')) ? 'Oui' : 'Non';
                        echo '<tr>';
                        echo '<td><a href="resultat_vote.php?id=' . $groupeId . '&vote_id=' . $voteId . '">' . $voteTitre . '</a></td>';
                        echo '<td>' . $voteDuree . '</td>';
                        echo '<td>' . $voteValide . '</td>';
                        echo '<td><a href="modifier_vote.php?id=' . $voteId . '">Modifier</a> | <a href="supprimer_vote.php?id=' . $voteId . '">Supprimer</a></td>';
                        echo '</tr>';
                    }      
                    ?>    
                </tbody>
            </table>
        </section>
    </main>

    <?php include 'footer.php'; ?>
</body>
</html>


