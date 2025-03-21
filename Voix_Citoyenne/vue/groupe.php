<?php
session_start();
require_once("../config/connexion.php");
require_once("../modele/groupe.php");
require_once("../modele/membre.php");
require_once("../modele/theme.php");
require_once("../modele/comporte.php");

// Vérifie si l'utilisateur est connecté
if (!isset($_SESSION['prenom']) || !isset($_SESSION['nom'])) {
    header("Location: connexion.php");
    exit;
}

$prenom = htmlspecialchars($_SESSION['prenom']);
$nom = htmlspecialchars($_SESSION['nom']);
$id = htmlspecialchars($_SESSION['id']);

// Récupérer l'ID du groupe depuis l'URL
$groupeId = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($groupeId === 0) {
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
    <title><?php echo htmlspecialchars($groupe->get('grp_nom')); ?></title>
    <link href="<?php echo htmlspecialchars($groupe->get('grp_img')); ?>" rel="shortcut icon" type="image/x-icon" />
    <link rel="stylesheet" href="../styles/groupe.css">
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
                <li><a href="liste_vote.php?id=<?php echo $groupeId; ?>">Vote</a></li>
            </ul>
        </nav>  

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

        <!-- Ajoutez cette section pour afficher le message de succès -->
        <div id="successMessage" style="color: green; font-weight: bold;"></div>

        <?php if ($isProprietaire) { ?>
    <section>
        <br>
        <button id="showInviteFormButton" class="right-button">
            <img src="../images/ajoutuser.png" alt="Nouvelle Image" class="button-image">
            Inviter un utilisateur
        </button>
        <form id="inviteUserForm" style="display: none; padding: 20px;">
            <label for="inviteEmail">Saisissez l'adresse e-mail de l'utilisateur que vous souhaitez inviter :</label>
            <input type="email" id="inviteEmail" name="inviteEmail" placeholder="Adresse e-mail de l'utilisateur" required>
            <button class="right-button" type="submit">Inviter</button>
        </form>
    </section>
<?php } ?>

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

        <?php $lesThemes = Comporte::getThemesbyidGroupe($groupeId); ?>

        <section>
            
            <h3>Liste des thèmes</h3>
            <table >
                <thead>
                    <tr>
                        <th>Nom du thème</th>
                        <?php if ($isProprietaire) { ?>
                        <th>Prix</th>
                        <th>Actions</th>
                        <?php } ?>
                    </tr>
                </thead>
                <tbody>
                    <!-- Contenu des thèmes --> 
                    <?php                
                    foreach ($lesThemes as $theme) {
                        echo '<tr>';
                        echo '<td>' . $theme->get('theme_nom') . '</td>'; 


                        if ($isProprietaire) {
                        echo '<td>' . $theme->get('lim_theme') . '€</td>'; // Assurez-vous que la colonne 'theme_prix' existe dans votre base de données
                        echo '<td>
                        <div class="boutons-container">
                            <form method="GET" action="modifTheme.php" style="display:inline;">
                                <input type="hidden" name="theme_id" value="' . $theme->get('theme_id') . '" />
                                <input type="hidden" name="group_id" value="' . $groupeId . '" />
                                <button type="submit" name="modify_theme" class="btn-modify">Modifier</button>
                            </form>
                            <form method="POST" action="../controllers/controleurSuppTheme.php" style="display:inline;">
                                <input type="hidden" name="theme_id" value="' . $theme->get('theme_id') . '" />
                                <input type="hidden" name="group_id" value="' . $groupeId . '" />
                                <button type="submit" name="delete_theme" class="btn-delete" onclick="return confirm(\'Êtes-vous sûr de vouloir supprimer ce thème ?\');">Supprimer</button>
                            </form>
                        </div>
                              </td>';
                        echo '</tr>';
                    }  else {
                        
                        echo '</tr>';
                    }
                    }

               
                    
                    
                    ?>
                </tbody>
            </table>
        </section>

        <?php if ($isProprietaire) { ?>
<section>
    <h3>Ajouter un nouveau thème</h3>
    <button onclick="window.location.href='ajoutTheme.php?id=<?php echo $groupeId; ?>';">Ajouter un thème</button>
</section>
<?php } ?>

        <section>
            <h3>Dernières décisions prises</h3>
            <ul>
                <!-- Contenu des décisions -->
            </ul>
        </section>

        <section>
            <h3>Propositions récentes</h3>
            <!-- Contenu des propositions -->
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