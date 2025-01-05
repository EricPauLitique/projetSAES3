<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once(__DIR__ . "/../config/connexion.php");
require_once(__DIR__ . "/../modele/groupe.php");
require_once(__DIR__ . "/../modele/membre.php");


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
    <title>Accueil</title>
    <link href="../images/logoVC.ico" rel="shortcut icon" type="image/x-icon" />
    <link rel="stylesheet" href="../styles/accueil.css">
    <script>
        async function fetchGroupes() {
            const response = await fetch('../api.php?endpoint=accueil');
            const result = await response.json();

            if (result.status === 'success') {
                const groupes = result.data;
                const groupesList = document.getElementById('groupes-list');
                groupesList.innerHTML = '';

                if (groupes.length === 0) {
                    groupesList.innerHTML = '<p>Vous n\'avez aucun groupe. <a href="creagroupe.php"><b>Créer un groupe</b></a> ou <i>rejoindre un groupe via un lien invitation faite par le créateur</i></a>.</p>';
                } else {
                    groupes.forEach(groupe => {
                        const li = document.createElement('li');
                        li.innerHTML = `
                            <div class="group-item">
                                <div class="group-image">
                                    <a href="groupe.php?id=${groupe.id}">
                                        <img src="${groupe.image}" alt="Logo ${groupe.nom}" class="image-small" />
                                    </a>
                                </div>
                                <h2 class="group-title" style="color: ${groupe.couleur};">
                                    <a href="groupe.php?id=${groupe.id}" class="group-link">${groupe.nom}</a>
                                </h2>
                            </div>
                            <div class="boutons-container">
                                ${groupe.proprietaire ? `
                                    <form method="post" action="../controllers/controleurmodifGroupe.php">
                                        <input type="hidden" name="group_id" value="${groupe.id}">
                                        <button type="submit" class="btn-modify">Modifier</button>
                                    </form>
                                    <form method="POST" style="display:inline;">
                                        <input type="hidden" name="group_id" value="${groupe.id}" />
                                        <button type="button" name="delete_group" class="btn-delete" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce groupe ?');">Supprimer</button>
                                    </form>
                                ` : `
                                    <form method="POST" style="display:inline;">
                                        <input type="hidden" name="user_id" value="${<?php echo $id; ?>}">
                                        <input type="hidden" name="grp_id" value="${groupe.id}">
                                        <button type="button" class="btn-delete btn-quitter" style="width: 93.81px; height: 35px;" onclick="quitterGroupe(${<?php echo $id; ?>}, ${groupe.id})">Quitter</button>
                                    </form>
                                `}
                            </div>
                        `;
                        groupesList.appendChild(li);
                    });
                }
            } else {
                document.getElementById('error-message').innerText = result.message;
            }
        }

        async function quitterGroupe(userId, grpId) {
            if (confirm('Êtes-vous sûr de vouloir quitter ce groupe ?')) {
                const response = await fetch('../api.php?endpoint=quitterGroupe', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ user_id: userId, grp_id: grpId })
                });

                const result = await response.json();
                if (result.status === 'success') {
                    alert(result.message);
                    fetchGroupes(); // Rafraîchir la liste des groupes
                } else {
                    alert(result.message);
                }
            }
        }

        async function deleteGroup(groupId) {
            const response = await fetch('../api.php?endpoint=supprimergroupe', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ group_id: groupId })
            });

            const result = await response.json();
            if (result.status === 'success') {
                alert(result.message);
                fetchGroupes(); // Rafraîchir la liste des groupes
            } else {
                alert(result.message);
            }
        }

        async function modifyGroup(groupId) {
            console.log("ID du groupe envoyé : " + groupId); // Ajoutez ce message de débogage
            const response = await fetch('../api.php?endpoint=modifgroupe', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ group_id: groupId })
            });

            const result = await response.json();
            if (result.status === 'success') {
                // Rediriger vers la page de modification avec les données du groupe
                window.location.href = 'modifgroupe.php';
            } else {
                alert(result.message);
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            fetchGroupes();

            document.getElementById('groupes-list').addEventListener('click', (event) => {
                if (event.target.classList.contains('btn-delete')) {
                    const groupId = event.target.closest('form').querySelector('input[name="group_id"]').value;
                    if (confirm('Êtes-vous sûr de vouloir supprimer ce groupe ?')) {
                        deleteGroup(groupId);
                    }
                }

                if (event.target.classList.contains('btn-modify')) {
                    const groupId = event.target.closest('form').querySelector('input[name="group_id"]').value;
                    console.log("ID du groupe récupéré : " + groupId); // Ajoutez ce message de débogage
                    modifyGroup(groupId);
                }
            });
        });
    </script>
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
            <ul id="groupes-list">
                <!-- Les groupes seront insérés ici par JavaScript -->
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

    <footer>
        <p>© 2024 Voix Citoyenne. Tous droits réservés.</p>
    </footer>
</body>
</html>