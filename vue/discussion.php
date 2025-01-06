<?php
session_start();
require_once("../config/connexion.php");
require_once("../modele/groupe.php");
require_once("../modele/membre.php");
require_once("../modele/theme.php");
require_once("../modele/comporte.php");
require_once("../modele/commentaire.php");
require_once("../modele/reaction.php");
require_once("../modele/proposition.php");

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
$propId = isset($_GET['prop_id']) ? intval($_GET['prop_id']) : 0;

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

// Récupérer les informations de la proposition
$proposition = Proposition::getPropositionById($propId);
if (!$proposition) {
    // Redirige vers la page d'accueil si la proposition n'existe pas
    $_SESSION['messageC'] = "La proposition n'a pas été trouvée.";
    header("Location: accueil.php");
    exit;
}

// Récupérer les commentaires de la proposition
$lesCommentaires = Commentaire::getCommentairesByPropositionId($propId);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($groupe->get('grp_nom')); ?></title>
    <link href="<?php echo htmlspecialchars($groupe->get('grp_img')); ?>" rel="shortcut icon" type="image/x-icon" />
    <link rel="stylesheet" href="../styles/discussion.css">
</head>
<body>
    <?php include 'header.php'; ?>

    <!-- Barre de navigation -->
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
                <li><a href="groupe.php?id=<?php echo $groupeId; ?>" class="active">Groupe</a></li>
                <li><a href="liste_prop.php?id=<?php echo $groupeId; ?>">Propositions</a></li>
                <li><a href="vote.php?id=<?php echo $groupeId; ?>">Vote</a></li>
            </ul>
        </nav>  
        < <aside>
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
                                        <button class="btn-delete" onclick="deleteMembre(' . $membre->get('user_id') . ', ' . $groupeId . ')">Supprimer</button>
                                      </td>';
                            }
                            echo '</tr>';
                        }
                    }
                    ?>
                </tbody>
            </table>
        </aside>

        <section>
        <?php            
        $lesCommentaires = Commentaire::getCommentairesByPropositionId($propId);    
        foreach ($lesCommentaires as $commentaire) {
            $comTxt = $commentaire->get('com_txt'); 
            $comDate = $commentaire->get('com_date');
            echo '<p>' . htmlspecialchars($comTxt) . '</p>';            
            echo '<p><small>' . htmlspecialchars($comDate) . '</small></p>';
            echo '<br>';
            }      
        ?>
            <img src="../images/user.png" /><p>Alexandra Lamy</p>
            <p>Je suis d'accord avec cette proposition !</p>
            <p>00h00</p>
            <img src="../images/signaler.png" /><p>signaler</p>
            <p>1</p><img src="../images/poucehaut2.png" /><p>0</p><img src="../images/poucebas.png" />
            <br>
            <img src="../images/user.png" /><p>Vous</p>
            <p>Pas moi</p>
            <p>00h12</p>
            <img src="../images/signaler.png" /><p>signaler</p>
            <p>0</p><img src="../images/poucehaut.png" /><p>0</p><img src="../images/poucebas.png" />
        </section>
        </main>
    
    <?php include 'footer.php'; ?>

    <!-- Bouton de défilement -->
    <button onclick="topFunction()" id="scrollButton" title="Go to top">Top</button>

    <script>
    // Afficher le message de succès s'il existe dans sessionStorage
    const successMessage = sessionStorage.getItem('message');
    if (successMessage) {
        document.getElementById('successMessage').innerText = successMessage;
        sessionStorage.removeItem('message');
    }

    document.getElementById('inviteUserForm').addEventListener('submit', function(event) {
        event.preventDefault();
        const email = document.getElementById('inviteEmail').value;
        const groupId = <?php echo $groupeId; ?>;

        fetch('../api.php?endpoint=inviteUser', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ email, groupId })
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                alert('Invitation envoyée avec succès.');
            } else {
                alert('Erreur lors de l\'envoi de l\'invitation : ' + data.message);
            }
        })
        .catch(error => console.error('Erreur:', error));
    });

    function deleteMembre(userId, grpId) {
        if (confirm('Êtes-vous sûr de vouloir supprimer ce membre ?')) {
            fetch(`../api.php?endpoint=membres&user_id=${userId}&grp_id=${grpId}`, {
                method: 'DELETE'
            })
            .then(response => response.json())
            .then(data => {
                if (data.message) {
                    alert(data.message);
                    location.reload();
                }
            })
            .catch(error => console.error('Erreur:', error));
        }
    }

    // Bouton de défilement
    window.onscroll = function() {scrollFunction()};

    function scrollFunction() {
        if (document.body.scrollTop > 20 || document.documentElement.scrollTop > 20) {
            document.getElementById("scrollButton").style.display = "block";
        } else {
            document.getElementById("scrollButton").style.display = "none";
        }
    }

    function topFunction() {
        document.body.scrollTop = 0; // Pour Safari
        document.documentElement.scrollTop = 0; // Pour Chrome, Firefox, IE et Opera
    }

    // Afficher/Masquer le formulaire d'invitation
    document.getElementById('showInviteFormButton').addEventListener('click', function() {
        const inviteForm = document.getElementById('inviteUserForm');
        if (inviteForm.style.display === 'none') {
            inviteForm.style.display = 'block';
        } else {
            inviteForm.style.display = 'none';
        }
    });
    </script>
</body>
</html>