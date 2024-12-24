<?php
session_start();
require_once("../config/connexion.php");
require_once("../modele/groupe.php");
require_once("../modele/membre.php");

// Vérifie si l'utilisateur est connecté
if (!isset($_SESSION['prenom']) || !isset($_SESSION['nom'])) {
    // Redirige vers la page de connexion si non connecté
    header("Location: connexion.html");
    exit;
}

$prenom = htmlspecialchars($_SESSION['prenom']);
$nom = htmlspecialchars($_SESSION['nom']);
$id = htmlspecialchars($_SESSION['id']);

// Récupérer l'ID du groupe depuis l'URL
$groupeId = isset($_GET['id']) ? intval($_GET['id']) : 0;

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

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($groupe->get('grp_nom')); ?></title>
    <link href="../images/logoVC.ico" rel="shortcut icon" type="image/x-icon" />
    <link rel="stylesheet" href="../styles/groupe.css">
</head>
<body>
    <?php include 'header.php'; 
    Connexion::connect()?>

    <main>
        <h1 id="titreGroupe">
            <img src="<?php echo htmlspecialchars($groupe->get('grp_img')); ?>" alt="Image associée" class="image-gauche">
            <?php echo 'Groupe :&nbsp;<b><i><u>' . htmlspecialchars($groupe->get('grp_nom')) . '</u></i></b>'; ?>
            <img src="<?php echo htmlspecialchars($groupe->get('grp_img')); ?>" alt="Image associée" class="image-gauche">

        </h1>
        <aside>
            <h3>Liste des membres :</h3>
            <table>
                <thead>
                    <tr>
                        <th>Photo</th>
                        <th>Prénom/Nom</th>
                        <th>Rôle</th>
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
                                    <img src="../images/createur.png" alt="Propriétaire" class="image-small" />
                                </td>
                                <td>' . $prenomProprio . ' ' . $nomProprio . '</td>
                                <td>Propriétaire</td>
                              </tr>';
                    }

                    if (!empty($membres)) {
                        foreach ($membres as $membre) {

                            echo '<tr>';
                            if ($membre->get('role') == 'Modérateur') {
                            echo '<td><img src="../images/user.png" alt="Moderateur" class="image-small" /></td>';
                        
                            } else {
                            echo '<td><img src="../images/user.png" alt="Membre" class="image-small" /></td>';
                        }
                            echo '<td>' . htmlspecialchars($membre->get('user_prenom')) . ' ' . htmlspecialchars($membre->get('user_nom')) . '</td>
                                    <td>' . htmlspecialchars($membre->get('role')) . '</td>
                                  </tr>';
                        }
                    }
                    ?>
                </tbody>
            </table>
        </aside>

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

    <!-- Lien vers un fichier JavaScript -->
    <script src="script.js"></script>
</body>
</html>