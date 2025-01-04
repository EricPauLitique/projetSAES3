<?php
require_once("../config/connexion.php");
require_once("../modele/groupe.php");
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
$groupeId = isset($_GET['id']) ? intval($_GET['id']) : 0;
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter un thème</title>
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

        <h1 id="titre">Ajouter un thème</h1>

        <?php
            if (isset($_SESSION['messageC'])) {
                echo $_SESSION['messageC'];
                unset($_SESSION['messageC']);
            }
            $limGRP = Groupe::getGroupByIdUnique2($groupeId);
            $limGRP = $limGRP->get('grp_lim_an');
        ?>

        <br>

        <!-- Formulaire pour créer un thème -->
        <form id="ajoutThemeForm">
            <input type="hidden" name="group_id" value="<?php echo $groupeId; ?>">
            <label for="nom_du_theme">Nom du thème :</label>
            <input type="text" id="nom_du_theme" name="nom_du_theme" placeholder="Nom du thème" required>
            <br>
            <label for="limite_theme">Limite des propositions :</label>
            <input type="number" id="limite_theme" name="limite_theme" placeholder="Limite pour le thème" required>
            <input type="hidden" name="limite_grp" value="<?php echo $limGRP; ?>">
            <br>
            <button type="submit">Créer le thème</button>
        </form>
        <div id="message"></div>
    </section>
</main>

<?php include 'footer.php'; ?>

<script>
document.getElementById('ajoutThemeForm').addEventListener('submit', async function(event) {
    event.preventDefault();

    const formData = new FormData(this);
    const data = {
        theme_nom: formData.get('nom_du_theme'),
        limite_theme: formData.get('limite_theme'),
        group_id: formData.get('group_id'),
        limite_grp: formData.get('limite_grp')
    };

    const response = await fetch('../api.php?endpoint=themes', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(data)
    });

    const result = await response.json();
    const messageDiv = document.getElementById('message');

    if (result.status === 'success') {
        messageDiv.innerHTML = '<p style="color: green; font-weight: bold;"><b>Le thème a été ajouté avec succès.</b></p>';
    } else {
        messageDiv.innerHTML = '<p style="color: red; font-weight: bold;"><b>' + result.message + '</b></p>';
    }
});
</script>
</body>
</html>