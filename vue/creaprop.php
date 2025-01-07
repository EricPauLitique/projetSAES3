<?php
require_once("../config/connexion.php");
require_once("../modele/groupe.php");
require_once("../modele/theme.php");
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

// Récupérer les thèmes du groupe
$themes = Theme::getThemesByGroupeId($groupeId);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Créer une proposition</title>
    <link href="../images/logoVC.ico" rel="shortcut icon" type="image/x-icon" />
    <link rel="stylesheet" href="../styles/creaProp.css">
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

        <h1 id="titre">Créer une proposition</h1>

        <form id="creaPropForm">
            <input type="hidden" name="user_id" value="<?php echo $user_id; ?>">
            <input type="hidden" name="group_id" value="<?php echo $groupeId; ?>">
            <input type="hidden" name="prop_date_min" value="">
            <input type="hidden" name="prop_cout" value="">
            <label for="prop_titre">Titre de la proposition :</label>
            <input type="text" id="prop_titre" name="prop_titre" placeholder="Titre de la proposition" required>
            <br>
            <label for="prop_desc">Description de la proposition :</label>
            <textarea id="prop_desc" name="prop_desc" placeholder="Description de la proposition" required></textarea>
            <br>
            <label for="theme_id">Thème :</label>
            <select id="theme_id" name="theme_id" required>
                <?php foreach ($themes as $theme): ?>
                    <option value="<?php echo $theme->get('theme_id'); ?>"><?php echo htmlspecialchars($theme->get('theme_nom')); ?></option>
                <?php endforeach; ?>
            </select>
            <br>
            <button type="submit">Créer la proposition</button>
        </form>
        <div id="message"></div>
    </section>
</main>

<?php include 'footer.php'; ?>

<script>
document.getElementById('creaPropForm').addEventListener('submit', async function(event) {
    event.preventDefault();

    const formData = new FormData(this);
    const data = {
        prop_titre: formData.get('prop_titre'),
        prop_desc: formData.get('prop_desc'),
        prop_date_min: formData.get('prop_date_min') || null,
        user_id: formData.get('user_id'),
        theme_id: formData.get('theme_id'),
        prop_cout: formData.get('prop_cout') || null
    };

    const response = await fetch('../api.php?endpoint=propositions', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(data)
    });

    const result = await response.json();
    const messageDiv = document.getElementById('message');

    if (result.status === 'success') {
        messageDiv.innerHTML = '<p style="color: green; font-weight: bold;"><b>La proposition a été ajoutée avec succès.</b></p>';
        setTimeout(() => {
            window.location.href = 'liste_prop.php?id=' + formData.get('group_id');
        }, 2000);
    } else {
        messageDiv.innerHTML = '<p style="color: red; font-weight: bold;"><b>' + result.message + '</b></p>';
    }
});
</script>
</body>
</html>