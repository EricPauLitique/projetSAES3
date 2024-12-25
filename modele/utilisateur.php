<?php
require_once("../config/connexion.php");

class Utilisateur {
    protected int $user_id;
    protected string $user_mail;
    protected string $user_mdp;
    protected string $user_prenom;
    protected string $user_nom;
    protected int $adr_id;

    // GET et SET
    public function get($attribut) {
        return $this->$attribut;
    }
    
    public function set($attribut, $valeur) {
        $this->$attribut = $valeur;
    }

    // Constructeur
    public function __construct(int $user_id = NULL, string $user_mail = NULL, string $user_mdp = NULL, string $user_prenom = NULL, string $user_nom = NULL, int $adr_id = NULL) {
        if (!is_null($user_id)) {
            $this->user_id = $user_id;
            $this->user_mail = $user_mail;
            $this->user_mdp = $user_mdp;
            $this->user_prenom = $user_prenom;
            $this->user_nom = $user_nom;
            $this->adr_id = $adr_id;
        }
    }

    // Méthode afficher
    public function afficher() {
        echo 'utilisateur ', $this->get("user_id"), ' (', $this->get("user_prenom"), ' ', $this->get("user_nom"), '), email = ', $this->get("user_mail");
    }

    // Récupérer tous les utilisateurs
    public static function getAllUtilisateur() {
        $requete = "SELECT * FROM utilisateur";
        $resultat = connexion::pdo()->query($requete);
        $resultat->setFetchMode(PDO::FETCH_CLASS, "Utilisateur");
        return $resultat->fetchAll();
    }

    // Récupérer un utilisateur par son mail
    public static function getUtilisateurByLogin($l) {
        try {
            $requete = "SELECT * FROM utilisateur WHERE user_id = :user_id";
            $stmt = connexion::pdo()->prepare($requete);
            $stmt->execute(['user_id' => $l]);
            $stmt->setFetchMode(PDO::FETCH_CLASS, 'Utilisateur');
            return $stmt->fetch();
        } catch (PDOException $e) {
            echo 'Erreur : ' . $e->getMessage();
            return null;
        }
    }

    // Supprimer un utilisateur par son ID
    public static function deleteUtilisateur(int $user_id) {
        try {
            $requete = "DELETE FROM utilisateur WHERE user_id = :user_id";
            $stmt = connexion::pdo()->prepare($requete);
            return $stmt->execute(['user_id' => $user_id]);
        } catch (PDOException $e) {
            echo 'Erreur : ' . $e->getMessage();
            return false;
        }
    }

    // Vérifier si l'email existe déjà pour un autre utilisateur
    public static function emailExists($email, $userId) {
        try {
            $requete = "SELECT user_id FROM utilisateur WHERE user_mail = :email AND user_id != :user_id";
            $stmt = connexion::pdo()->prepare($requete);
            $stmt->execute(['email' => $email, 'user_id' => $userId]);
            return $stmt->fetch() !== false;
        } catch (PDOException $e) {
            throw new Exception('Erreur lors de la vérification de l\'email : ' . $e->getMessage());
        }
    }

    // Vérifier si le prénom et le nom existent déjà pour un autre utilisateur
    public static function prenomNomExists($prenom, $nom, $userId) {
        try {
            $requete = "SELECT user_id FROM utilisateur WHERE user_prenom = :prenom AND user_nom = :nom AND user_id != :user_id";
            $stmt = connexion::pdo()->prepare($requete);
            $stmt->execute(['prenom' => $prenom, 'nom' => $nom, 'user_id' => $userId]);
            return $stmt->fetch() !== false;
        } catch (PDOException $e) {
            throw new Exception('Erreur lors de la vérification du prénom et du nom : ' . $e->getMessage());
        }
    }

    // Mettre à jour un utilisateur
    public static function updateUtilisateur(Utilisateur $utilisateur) {
        try {
            $requete = "UPDATE utilisateur SET user_mail = :user_mail, user_mdp = :user_mdp, user_prenom = :user_prenom, user_nom = :user_nom WHERE user_id = :user_id";
            $stmt = connexion::pdo()->prepare($requete);
            return $stmt->execute([
                'user_mail' => $utilisateur->get('user_mail'),
                'user_mdp' => $utilisateur->get('user_mdp'),
                'user_prenom' => $utilisateur->get('user_prenom'),
                'user_nom' => $utilisateur->get('user_nom'),
                'user_id' => $utilisateur->get('user_id')
            ]);
        } catch (PDOException $e) {
            throw new Exception('Erreur lors de la mise à jour de l\'utilisateur : ' . $e->getMessage());
        }
    }

}

?>
