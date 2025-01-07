<?php
session_start();

require_once(__DIR__ . "/../config/connexion.php");
require_once(__DIR__ . "/../modele/groupe.php");
require_once(__DIR__ . "/../modele/membre.php");
require_once(__DIR__ . "/../modele/theme.php");
require_once(__DIR__ . "/../modele/comporte.php");
require_once(__DIR__ . "/../modele/commentaire.php");
require_once(__DIR__ . "/../modele/reaction.php");
require_once(__DIR__ . "/../modele/proposition.php");
require_once(__DIR__ . "/../modele/notifUtilisateur.php");

// Vérifie si l'utilisateur est connecté
if (!isset($_SESSION['prenom']) || !isset($_SESSION['nom'])) {
    // Redirige vers la page de connexion si non connecté
    error_log("Utilisateur non connecté, redirection vers la page de connexion.");
    header("Location: connexion.php");
    exit;
}

$prenom = htmlspecialchars($_SESSION['prenom']);
$nom = htmlspecialchars($_SESSION['nom']);
$id = htmlspecialchars($_SESSION['id']);
error_log("Utilisateur connecté : $prenom $nom (ID: $id)");

// Récupérer l'ID du groupe depuis l'URL
$groupeId = isset($_GET['id']) ? intval($_GET['id']) : 0;
$propId = isset($_GET['prop_id']) ? intval($_GET['prop_id']) : 0;
error_log("ID du groupe : $groupeId, ID de la proposition : $propId");

if ($groupeId === 0) {
    // Redirige vers la page d'accueil si l'ID du groupe est invalide
    $_SESSION['messageC'] = "L'ID du groupe est invalide.";
    error_log("ID du groupe invalide, redirection vers la page d'accueil.");
    header("Location: accueil.php");
    exit;
}

// Connexion à la base de données
Connexion::connect();
error_log("Connexion à la base de données établie.");

// Récupérer les informations du groupe
$groupe = Groupe::getGroupByIdUnique2($groupeId);
if (!$groupe) {
    // Redirige vers la page d'accueil si le groupe n'existe pas
    $_SESSION['messageC']= "Le groupe n'a pas été trouvé.";
    error_log("Groupe non trouvé, redirection vers la page d'accueil.");
    header("Location: accueil.php");
    exit;
}

if (Membre::siMembreInconnu($id, $groupeId) == 0 && Groupe::siProprioInconnu($id, $groupeId) == 0) {
    // Redirige vers la page d'accueil si l'utilisateur n'est pas membre du groupe
    $message = "Vous n'êtes pas autorisé à aller dans le groupe.";
    $_SESSION['messageC'] = $message;
    error_log("Utilisateur non autorisé à accéder au groupe, redirection vers la page d'accueil.");
    header("Location: accueil.php");
    exit;
} 

// Vérifier si l'utilisateur est le propriétaire du groupe
$isProprietaire = Groupe::siProprioInconnu($id, $groupeId) == 1;
error_log("Utilisateur est propriétaire du groupe : " . ($isProprietaire ? "Oui" : "Non"));

// Récupérer les informations de la proposition
$proposition = Proposition::getPropositionById($propId);
if (!$proposition) {
    // Redirige vers la page d'accueil si la proposition n'existe pas
    $_SESSION['messageC'] = "La proposition n'a pas été trouvée.";
    error_log("Proposition non trouvée, redirection vers la page d'accueil.");
    header("Location: accueil.php");
    exit;
}

// Récupérer les commentaires de la proposition
$lesCommentaires = Commentaire::getCommentairesByPropositionId($propId);
error_log("Nombre de commentaires récupérés : " . count($lesCommentaires));
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
            <?php echo 'Groupe :&nbsp;<b><i><u>' . htmlspecialchars($groupe->get('grp_nom')); ?></u></i></b>
        </h1>

        <nav>
            <ul>
                <li><a href="groupe.php?id=<?php echo $groupeId; ?>" >Groupe</a></li>
                <li><a href="liste_prop.php?id=<?php echo $groupeId; ?>"class="active">Propositions</a></li>
                <li><a href="vote.php?id=<?php echo $groupeId; ?>">Vote</a></li>
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
            <h2>Discussion sur la proposition</h2>
            <div class="proposition-details">
                <h3><?php echo htmlspecialchars($proposition->get('prop_titre')); ?></h3>
                <p><?php echo htmlspecialchars($proposition->get('prop_desc')); ?></p>
            </div>

            <div class="commentaires">
                <h3>Commentaires</h3>
                <?php if (!empty($lesCommentaires)) { ?>
                    <?php foreach ($lesCommentaires as $commentaire) { ?>
                        <div class="commentaire">
                            <div class="commentaire-header">
                                <img src="../images/user.png" alt="User" class="commentaire-avatar">
                                <span class="commentaire-username"><?php echo htmlspecialchars($commentaire->get('user_prenom')) . ' ' . htmlspecialchars($commentaire->get('user_nom')); ?></span>
                                <span class="commentaire-date"><?php echo htmlspecialchars($commentaire->get('com_date')); ?></span>
                            </div>
                            <div class="commentaire-body">
                                <p><?php echo htmlspecialchars($commentaire->get('com_txt')); ?></p>
                                <div class="commentaire-signalement">
                                    <button class="btn-report"><img src="../images/signaler.png"></button>
                                    <?php if ($isProprietaire) { ?>
                                        <button class="btn-delete" onclick="deleteCommentaire(<?php echo $commentaire->get('com_id'); ?>)"><img src="../images/supprimer.png"></button>
                                    <?php } ?>
                                </div>
                            </div>
                            <div class="commentaire-footer">
                                <button class="btn-like"><img src="../images/poucehaut.png"></button>
                                <button class="btn-dislike"><img src="../images/poucebas.png"></button>
                            </div>
                        </div>
                    <?php } ?>
                <?php } else { ?>
                    <p>Aucun commentaire trouvé.</p>
                <?php } ?>
            </div>

            <div class="ajouter-commentaire">
                <h3>Ajouter un commentaire</h3>
                <form id="ajouterCommentaireForm" method="POST">
                    <textarea id="commentaireTexte" name="commentaireTexte" placeholder="Écrire un commentaire..." required></textarea>
                    <button type="submit">Ajouter</button>
                </form>
            </div>
        </section>
    </main>
    
    <?php include 'footer.php'; ?>

    <!-- Bouton de défilement -->
    <button onclick="topFunction()" id="scrollButton" title="Go to top">Top</button>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Afficher le message de succès s'il existe dans sessionStorage
        const successMessage = sessionStorage.getItem('message');
        if (successMessage) {
            document.getElementById('successMessage').innerText = successMessage;
            sessionStorage.removeItem('message');
        }

        const inviteUserForm = document.getElementById('inviteUserForm');
        if (inviteUserForm) {
            inviteUserForm.addEventListener('submit', function(event) {
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
                .catch(error => {
                    console.error('Erreur:', error);
                });
            });
        }

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
                .catch(error => {
                    console.error('Erreur:', error);
                });
            }
        }

        function deleteCommentaire(comId) {
            if (confirm('Êtes-vous sûr de vouloir supprimer ce commentaire ?')) {
                fetch(`../api.php?endpoint=commentaires&id=${comId}`, {
                    method: 'DELETE'
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        alert('Commentaire supprimé avec succès.');
                        fetchCommentaires();
                    } else {
                        alert('Erreur lors de la suppression du commentaire : ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Erreur:', error);
                });
            }
        }

        function fetchCommentaires() {
            const propId = <?php echo $propId; ?>;
            fetch(`../api.php?endpoint=commentaires&prop_id=${propId}`)
                .then(response => response.json())
                .then(data => {
                    const commentairesDiv = document.querySelector('.commentaires');
                    commentairesDiv.innerHTML = '<h3>Commentaires</h3>';
                    if (data.length > 0) {
                        data.forEach(commentaire => {
                            const commentaireDiv = document.createElement('div');
                            commentaireDiv.classList.add('commentaire');
                            commentaireDiv.innerHTML = `
                                <div class="commentaire-header">
                                    <img src="../images/user.png" alt="User" class="commentaire-avatar">
                                    <span class="commentaire-username">${commentaire.user_prenom} ${commentaire.user_nom}</span>
                                    <span class="commentaire-date">${commentaire.com_date}</span>
                                </div>
                                <div class="commentaire-body">
                                    <p>${commentaire.com_txt}</p>
                                    <div class="commentaire-signalement">
                                        <button class="btn-report">Signaler</button>
                                        <?php if ($isProprietaire) { ?>
                                            <button class="btn-delete" onclick="deleteCommentaire(${commentaire.com_id})">Supprimer</button>
                                        <?php } ?>
                                    </div>
                                </div>
                                <div class="commentaire-footer">
                                    <button class="btn-like">J'aime</button>
                                    <button class="btn-dislike">Je n'aime pas</button>
                                    <button class="btn-reply">Répondre</button>
                                </div>
                            `;
                            commentairesDiv.appendChild(commentaireDiv);
                        });
                    } else {
                        commentairesDiv.innerHTML += '<p>Aucun commentaire trouvé.</p>';
                    }
                })
                .catch(error => {
                    console.error('Erreur:', error);
                });
        }

        // Ajouter un commentaire
        document.getElementById('ajouterCommentaireForm').addEventListener('submit', function(event) {
            event.preventDefault();
            console.log("Form submitted"); // Debugging line
            const commentaireTexte = document.getElementById('commentaireTexte').value;
            const propId = <?php echo $propId; ?>;
            const userId = <?php echo $id; ?>;

            console.log("Sending data:", { com_txt: commentaireTexte, prop_id: propId, user_id: userId }); // Debugging line

            fetch('../api.php?endpoint=commentaires', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ com_txt: commentaireTexte, prop_id: propId, user_id: userId })
            })
            .then(response => response.json())
            .then(data => {
                console.log("Response received:", data); // Debugging line
                if (data.status === 'success') {
                    document.getElementById('commentaireTexte').value = '';
                    fetchCommentaires();
                } else {
                    alert('Erreur lors de l\'ajout du commentaire : ' + data.message);
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
            });
        });

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
        const showInviteFormButton = document.getElementById('showInviteFormButton');
        if (showInviteFormButton) {
            showInviteFormButton.addEventListener('click', function() {
                const inviteForm = document.getElementById('inviteUserForm');
                if (inviteForm.style.display === 'none') {
                    inviteForm.style.display = 'block';
                } else {
                    inviteForm.style.display = 'none';
                }
            });
        }

        // Initial fetch of comments
        fetchCommentaires();
    });
    </script>
</body>
</html>