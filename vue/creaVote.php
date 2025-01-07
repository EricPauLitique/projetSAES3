<?php
require_once("../config/connexion.php");
require_once("../modele/groupe.php");
require_once("../modele/theme.php");
require_once("../modele/proposition.php");
require_once("../modele/comporte.php");

Connexion::connect();

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['prenom']) || !isset($_SESSION['nom'])) {
    // Redirige vers la page de connexion si non connecté
    header("Location: connexion.php");
    exit;
}

$prenom = htmlspecialchars($_SESSION['prenom']);
$nom = htmlspecialchars($_SESSION['nom']);
$user_id = htmlspecialchars($_SESSION['id']);
$groupeId = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Vérifier si l'ID du groupe est valide
if ($groupeId === 0) {
    $_SESSION['messageC'] = "L'ID du groupe est invalide.";
    header("Location: accueil.php");
    exit;
}

// Récupérer les propositions du groupe
$propositions = Proposition::getPropositionsByGroupeId($groupeId);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Créer un vote</title>
    <link href="../images/logoVC.ico" rel="shortcut icon" type="image/x-icon" />
    <link rel="stylesheet" href="../styles/creaVote.css">
</head>
<body>
<?php include 'header.php'; ?>

<main>
    <section>
        <div class="retour">    
            <img src="../images/retour.png" alt="retour" class="retour-icon"/>
            <a href="groupe.php?id=<?php echo $groupeId; ?>">Retour</a>
        </div>

        <br>

        <h1 id="titre">Créer un vote</h1>

        <form id="creaVoteForm">
            <input type="hidden" name="user_id" value="<?php echo $user_id; ?>">
            <input type="hidden" name="group_id" value="<?php echo $groupeId; ?>">
            <label for="vote_type_scrutin">Nom du vote :</label>
            <input type="text" id="vote_type_scrutin" name="vote_type_scrutin" placeholder="Nom du vote" required>
            <br>
            <label for="vote_duree">Durée du vote (en jours) :</label>
            <input type="number" id="vote_duree" name="vote_duree" placeholder="Durée du vote" required>
            <br>
            <label for="prop_id">Proposition :</label>
            <select id="prop_id" name="prop_id" required>
                <?php foreach ($propositions as $proposition): ?>
                    <option value="<?php echo $proposition->get('prop_id'); ?>"><?php echo htmlspecialchars($proposition->get('prop_titre')); ?></option>
                <?php endforeach; ?>
            </select>
            <br>
            <label for="vote_valide">Valide :</label>
            <select id="vote_valide" name="vote_valide" required>
                <option value="1">Oui</option>
                <option value="0">Non</option>
            </select>
            <br>
            <button type="submit">Créer le vote</button>
        </form>
        <div id="message"></div>
    </section>
</main>

<?php include 'footer.php'; ?>

<script>
document.getElementById('creaVoteForm').addEventListener('submit', async function(event) {
    event.preventDefault();

    const formData = new FormData(this);
    const data = {
        vote_type_scrutin: formData.get('vote_type_scrutin'),
        vote_duree: formData.get('vote_duree'),
        vote_valide: formData.get('vote_valide'),
        prop_id: formData.get('prop_id'),
        group_id: formData.get('group_id') // Add group_id to the data
    };

    const response = await fetch('../api.php?endpoint=votes', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(data)
    });

    const result = await response.json();
    const messageDiv = document.getElementById('message');

    if (result.status === 'success') {
        messageDiv.innerHTML = '<p style="color: green; font-weight: bold;"><b>Le vote a été créé avec succès.</b></p>';
        setTimeout(() => {
            window.location.href = 'liste_vote.php?id=' + formData.get('group_id');
        }, 2000);
    } else {
        messageDiv.innerHTML = '<p style="color: red; font-weight: bold;"><b>' + result.message + '</b></p>';
    }
});
</script>
</body>
</html>