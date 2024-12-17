<?php
require_once("../config/connexion.php");
$titre = "Inscription";
include("../vue/debut.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupération et nettoyage des données
    $prenom = filter_input(INPUT_POST, 'prenom', FILTER_SANITIZE_STRING);
    $nom = filter_input(INPUT_POST, 'nom', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);
    $code_postal = filter_input(INPUT_POST, 'code_postal', FILTER_SANITIZE_NUMBER_INT);
    $ville = filter_input(INPUT_POST, 'ville', FILTER_SANITIZE_STRING);
    $numero_rue = filter_input(INPUT_POST, 'numero_rue', FILTER_SANITIZE_NUMBER_INT);
    $nom_rue = filter_input(INPUT_POST, 'nom_rue', FILTER_SANITIZE_STRING);

    // Validation des champs
    if (!$prenom || !$nom || !$email || !$password || !$code_postal || !$ville || !$numero_rue || !$nom_rue) {
        echo "Tous les champs doivent être remplis correctement.";
        exit;
    }

    // Connexion à la base de données
    Connexion::connect();
    $pdo = Connexion::PDO();

    try {
        // 1. Vérification de l'existence de l'email
        $stmt = $pdo->prepare("SELECT user_id FROM utilisateur WHERE user_mail = :email");
        $stmt->execute(['email' => $email]);
        if ($stmt->fetch()) {
            echo "<p style=\"color: red;\"><b>L'email existe déjà dans notre système.</b></p>";
            include ("../vue/creacompte.html");
            exit;
        }

        // 2. Vérification de l'existence de prénom + nom
        $stmt = $pdo->prepare("SELECT user_id FROM utilisateur WHERE user_prenom = :prenom AND user_nom = :nom");
        $stmt->execute(['prenom'
         => $prenom, 'nom' => $nom]);
        if ($stmt->fetch()) {
            echo '<p style="color: red;"><b>Un utilisateur avec le même prénom et nom existe déjà.</b></p>';
            include ("../vue/creacompte.html");
            exit;
        }

        // 3. Vérification de l'existence de l'adresse
        $cptIdAdresse = $pdo->prepare("SELECT COUNT(*) FROM adresse WHERE adr_num = :numero_rue AND adr_rue = :nom_rue");
        $cptIdAdresse->execute([
            'numero_rue' => $numero_rue,
            'nom_rue' => $nom_rue
        ]);
        $resultIdAdresse = $cptIdAdresse->fetchColumn();
        
        if ($resultIdAdresse == 0) {
            // Si l'adresse n'existe pas, calculer un nouvel ID pour l'adresse
            $stmt = $pdo->prepare("SELECT MAX(adr_id) FROM adresse");
            $stmt->execute();
            $maxIdAdresse = $stmt->fetchColumn();
            $resultIdAdresse = $maxIdAdresse + 1;  // ID manuel (pas auto-incrémenté)

            // Insertion de la nouvelle adresse
            $stmt3 = $pdo->prepare("
                INSERT INTO adresse (adr_id, adr_cp, adr_ville, adr_rue, adr_num)
                VALUES (:idAdresse, :code_postal, :ville, :nom_rue, :numero_rue)
            ");
            $stmt3->execute([
                'idAdresse' => $resultIdAdresse,
                'code_postal' => $code_postal,
                'ville' => $ville,
                'nom_rue' => $nom_rue,
                'numero_rue' => $numero_rue
            ]);
        }
        
        // Calcul manuel pour l'ID utilisateur
        $stmt2 = $pdo->prepare("SELECT MAX(user_id) FROM utilisateur");
        $stmt2->execute();
        $maxIdUtilisateur = $stmt2->fetchColumn();
        $resultIdUtilisateur = $maxIdUtilisateur + 1; // ID manuel pour utilisateur

        // Hachage du mot de passe
        $passwordHashed = password_hash($password, PASSWORD_DEFAULT);

        // 4. Insertion dans la base (utilisateur)
        $stmt4 = $pdo->prepare("
            INSERT INTO utilisateur(user_id, user_mail, user_mdp, user_prenom, user_nom, adr_id)
            VALUES (:user_id, :email, :password, :prenom, :nom, :idAdresse)
        ");
        $stmt4->execute([
            'user_id' => $resultIdUtilisateur,
            'prenom' => $prenom,
            'nom' => $nom,
            'email' => $email,
            'password' => $passwordHashed, // Insère le mot de passe haché
            'idAdresse' => $resultIdAdresse
        ]);

       session_start(); // Démarre la session

       // Après vérification réussie des identifiants
       $_SESSION['prenom'] = $prenom; // Stocke le prénom dans la session
       $_SESSION['nom'] = $nom;       // Stocke le nom dans la session
       
       // Redirection vers la page d'accueil
       header("Location: ../vue/accueil.php");
       exit;

    } catch (PDOException $e) {
        echo "Erreur : " . $e->getMessage();
    }
}
?>
