<?php
require_once(__DIR__ . "/../config/connexion.php");
require_once(__DIR__ . "/../modele/theme.php");
require_once(__DIR__ . "/../modele/comporte.php");

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
$themeId = isset($_GET['theme_id']) ? intval($_GET['theme_id']) : 0;
$groupeId = isset($_GET['group_id']) ? intval($_GET['group_id']) : 0;

if ($themeId === 0 || $groupeId === 0) {
    // Redirige vers la page du groupe si l'ID du thème ou du groupe est invalide
    $_SESSION['messageC'] = "ID du thème ou du groupe invalide.";
    header("Location: groupe.php?id=" . $groupeId);
    exit;
}

// Récupérer les informations du thème
$theme = Theme::getThemeById($themeId);
$comporte = Comporte::getComporteById($groupeId, $themeId);
if (!$theme) {
    // Redirige vers la page du groupe si le thème n'existe pas
    $_SESSION['messageC'] = "Le thème n'a pas été trouvé.";
    header("Location: groupe.php?id=" . $groupeId);
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier un thème</title>
    <link href="../images/logoVC.ico" rel="shortcut icon" type="image/x-icon" />
    <link rel="stylesheet" href="../styles/creaTheme.css">
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

        <h1 id="titre">Modifier un thème</h1>

        <?php
            if (isset($_SESSION['messageC'])) {
                echo $_SESSION['messageC'];
                unset($_SESSION['messageC']);
            }
        ?>

        <br>

        <!-- Formulaire pour modifier un thème -->
        <form id="modifThemeForm">
            <input type="hidden" name="theme_id" value="<?php echo $themeId; ?>">
            <input type="hidden" name="group_id" value="<?php echo $groupeId; ?>">
            <label for="nom_du_theme">Nom du thème :</label>
            <input type="text" id="nom_du_theme" name="nom_du_theme" value="<?php echo htmlspecialchars($theme->get('theme_nom')); ?>" required>
            <br>
            <label for="limite_theme">Limite des propositions :</label>
            <input type="number" id="limite_theme" name="limite_theme" value="<?php echo htmlspecialchars($comporte->get('lim_theme')); ?>" required>
            <br>
            <button type="submit">Modifier le thème</button>
        </form>
        <div id="message"></div>
    </section>
</main>

<?php include 'footer.php'; ?>

<script>
document.getElementById('modifThemeForm').addEventListener('submit', async function(event) {
    event.preventDefault();

    const formData = new FormData(this);
    const data = {
        theme_id: formData.get('theme_id'),
        group_id: formData.get('group_id')
    };

    if (formData.get('nom_du_theme')) {
        data.theme_nom = formData.get('nom_du_theme');
    }

    if (formData.get('limite_theme')) {
        data.limite_theme = formData.get('limite_theme');
    }

    const response = await fetch('../api.php?endpoint=themes', {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(data)
    });

    const result = await response.json();
    const messageDiv = document.getElementById('message');

    if (result.status === 'success') {
        sessionStorage.setItem('message', 'Le thème a été modifié avec succès.');
        window.location.href = 'groupe.php?id=' + data.group_id;
    } else {
        messageDiv.innerHTML = '<p style="color: red; font-weight: bold;"><b>' + result.message + '</b></p>';
    }
});
</script>
</body>
</html>