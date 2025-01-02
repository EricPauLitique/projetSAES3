<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once("../config/connexion.php");
require_once("../modele/groupe.php");
require_once("../modele/membre.php");

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
                                <form method="POST" action="../controllers/controleurmodifGroupe.php" style="display:inline;">
                                    <input type="hidden" name="group_id" value="${groupe.id}" />
                                    <button type="submit" name="modify_group" class="btn-modify">Modifier</button>
                                </form>
                                <form method="POST" action="../controllers/controleurSuppGroupe.php" style="display:inline;">
                                    <input type="hidden" name="group_id" value="${groupe.id}" />
                                    <button type="submit" name="delete_group" class="btn-delete" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce groupe ?');">Supprimer</button>
                                </form>
                            ` : `
                                <form method="POST" action="../controllers/controleurQuitterGroupe.php" style="display:inline;">
                                    <input type="hidden" name="user_id" value="${<?php echo $id; ?>}">
                                    <input type="hidden" name="grp_id" value="${groupe.id}">
                                    <button type="submit" class="btn-delete btn-quitter" style="width: 93.81px; height: 35px;" onclick="return confirm('Êtes-vous sûr de vouloir quitter ce groupe ?');">Quitter</button>
                                </form>
                            `}
                        </div>
                    `;
                    groupesList.appendChild(li);
                });
            } else {
                document.getElementById('error-message').innerText = result.message;
            }
        }

        document.addEventListener('DOMContentLoaded', fetchGroupes);
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