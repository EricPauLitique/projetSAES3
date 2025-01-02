<?php
require_once(__DIR__ . "/../config/connexion.php");

Connexion::connect();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupération et nettoyage des données
    $data = json_decode(file_get_contents("php://input"), true);
    $prenom = filter_var($data['prenom'], FILTER_SANITIZE_STRING);
    $nom = filter_var($data['nom'], FILTER_SANITIZE_STRING);
    $email = filter_var($data['email'], FILTER_VALIDATE_EMAIL);
    $password = filter_var($data['password'], FILTER_SANITIZE_STRING);
    $confirm_password = filter_var($data['confirm_password'], FILTER_SANITIZE_STRING);
    $code_postal = filter_var($data['code_postal'], FILTER_SANITIZE_NUMBER_INT);
    $ville = filter_var($data['ville'], FILTER_SANITIZE_STRING);
    $numero_rue = filter_var($data['numero_rue'], FILTER_SANITIZE_NUMBER_INT);
    $nom_rue = filter_var($data['nom_rue'], FILTER_SANITIZE_STRING);

    $prenom = ucfirst(strtolower($prenom));
    $nom = ucfirst(strtolower($nom));

    // Validation des champs
    if (!$prenom || !$nom || !$email || !$password || !$confirm_password || !$code_postal || !$ville || !$numero_rue || !$nom_rue) {
        echo json_encode(['status' => 'error', 'message' => 'Tous les champs doivent être remplis correctement.']);
        exit;
    }

    if ($password !== $confirm_password) {
        echo json_encode(['status' => 'error', 'message' => 'Les mots de passe ne correspondent pas.']);
        exit;
    }

    // Validation du code postal
    if (!preg_match('/^\d{5}$/', $code_postal)) {
        echo json_encode(['status' => 'error', 'message' => 'Le code postal doit contenir exactement 5 chiffres.']);
        exit;
    }

    // Connexion à la base de données
    $pdo = Connexion::PDO();

    try {
        // 1. Vérification de l'existence de l'email
        $stmt = $pdo->prepare("SELECT user_id FROM utilisateur WHERE user_mail = :email");
        $stmt->execute(['email' => $email]);
        if ($stmt->fetch()) {
            echo json_encode(['status' => 'error', 'message' => "L'email existe déjà dans notre système."]);
            exit;
        }

        // 3. Vérification de l'existence de l'adresse
        $cptIdAdresse = $pdo->prepare("SELECT distinct adr_id FROM adresse WHERE adr_num = :numero_rue AND adr_rue = :nom_rue");
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

        // Réponse JSON pour succès
        echo json_encode(['status' => 'success', 'message' => 'Votre compte a été créé avec succès.']);
        exit;

    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => "Erreur : " . $e->getMessage()]);
        exit;
    }
}
?>