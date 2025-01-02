<?php
if (session_status() === PHP_SESSION_NONE) {
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
            <li><a href="#" onclick="logout()">Se déconnecter</a></li>
            <li><a href="#" onclick="confirmDeleteAccount()">Supprimer mon compte</a></li>
            <li><a href="modifCompte.php">Modifier mes paramètres</a></li>
        </ul>
    </div>
</header>

<script>
function confirmDeleteAccount() {
    if (confirm('Voulez-vous vraiment supprimer votre compte ?')) {
        if (confirm('En supprimant votre compte, toutes vos données seront perdues et ne pourront pas être récupérées. Êtes-vous sûr de vouloir continuer ?')) {
            deleteAccount();
        }
    }
}

async function deleteAccount() {
    const response = await fetch('../api.php?endpoint=supprimercompte', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        }
    });

    const result = await response.json();
    if (result.status === 'success') {
        window.location.href = 'connexion.php';
    } else {
        alert(result.message);
    }
}

async function logout() {
    const response = await fetch('../api.php?endpoint=logout', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        }
    });

    const result = await response.json();
    if (result.status === 'success') {
        window.location.href = 'connexion.php';
    } else {
        alert('Erreur lors de la déconnexion.');
    }
}
</script>