<?php
require_once(__DIR__ . "/../config/connexion.php");

class Utilisateur {
    protected ?int $user_id;
    protected string $user_mail;
    protected string $user_mdp;
    protected string $user_prenom;
    protected string $user_nom;
    protected int $adr_id;

    // Constructeur
    public function __construct(?int $user_id = null, string $user_mail = '', string $user_mdp = '', string $user_prenom = '', string $user_nom = '', int $adr_id = 0) {
        $this->user_id = $user_id;
        $this->user_mail = $user_mail;
        $this->user_mdp = $user_mdp;
        $this->user_prenom = $user_prenom;
        $this->user_nom = $user_nom;
        $this->adr_id = $adr_id;
    }

    // Méthodes GET et SET
    public function get($attribut) {
        return $this->$attribut ?? null;
    }

    public function set($attribut, $valeur) {
        $this->$attribut = $valeur;
    }

    // Méthode __toString()
    public function __toString() {
        return "Utilisateur [ID: $this->user_id, Email: $this->user_mail, Prénom: $this->user_prenom, Nom: $this->user_nom, Adresse ID: $this->adr_id]";
    }

    // Implémentation de JsonSerializable
    public function jsonSerialize() {
        return get_object_vars($this);
    }

    // Méthodes CRUD
    public static function createUtilisateur(Utilisateur $utilisateur) {
        $pdo = Connexion::pdo();
        $stmt = $pdo->prepare("INSERT INTO utilisateur (user_mail, user_mdp, user_prenom, user_nom, adr_id) VALUES (:user_mail, :user_mdp, :user_prenom, :user_nom, :adr_id)");
        $stmt->bindParam(':user_mail', $utilisateur->user_mail);
        $stmt->bindParam(':user_mdp', $utilisateur->user_mdp);
        $stmt->bindParam(':user_prenom', $utilisateur->user_prenom);
        $stmt->bindParam(':user_nom', $utilisateur->user_nom);
        $stmt->bindParam(':adr_id', $utilisateur->adr_id);
        return $stmt->execute();
    }

    public static function getUtilisateurByLogin($login) {
        $pdo = Connexion::pdo();
        $stmt = $pdo->prepare("SELECT * FROM utilisateur WHERE user_mail = :login");
        $stmt->bindParam(':login', $login);
        $stmt->execute();
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($data) {
            return new self($data['user_id'], $data['user_mail'], $data['user_mdp'], $data['user_prenom'], $data['user_nom'], $data['adr_id']);
        }
        return null;
    }

    public static function getUtilisateurById($id) {
        $pdo = Connexion::pdo();
        $stmt = $pdo->prepare("SELECT * FROM utilisateur WHERE user_id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($data) {
            return new self($data['user_id'], $data['user_mail'], $data['user_mdp'], $data['user_prenom'], $data['user_nom'], $data['adr_id']);
        }
        return null;
    }

        // Vérifier si une adresse est utilisée par d'autres utilisateurs
        public static function isAdresseUsedByOthers($adr_id, $user_id) {
            try {
                $requete = "SELECT COUNT(*) FROM utilisateur WHERE adr_id = :adr_id AND user_id != :user_id";
                $stmt = connexion::pdo()->prepare($requete);
                $stmt->execute(['adr_id' => $adr_id, 'user_id' => $user_id]);
                return $stmt->fetchColumn() > 0;
            } catch (PDOException $e) {
                echo 'Erreur : ' . $e->getMessage();
                return false;
            }
        }

    public static function getAllUtilisateur() {
        $pdo = Connexion::pdo();
        $stmt = $pdo->query("SELECT * FROM utilisateur");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function updateUtilisateur(Utilisateur $utilisateur) {
        $pdo = Connexion::pdo();
        $stmt = $pdo->prepare("UPDATE utilisateur SET user_mail = :user_mail, user_prenom = :user_prenom, user_nom = :user_nom WHERE user_id = :user_id");
        $stmt->bindParam(':user_id', $utilisateur->user_id);
        $stmt->bindParam(':user_mail', $utilisateur->user_mail);
        $stmt->bindParam(':user_prenom', $utilisateur->user_prenom);
        $stmt->bindParam(':user_nom', $utilisateur->user_nom);
        return $stmt->execute();
    }

    public static function deleteUtilisateur($user_id) {
        $pdo = Connexion::pdo();
        $stmt = $pdo->prepare("DELETE FROM utilisateur WHERE user_id = :user_id");
        $stmt->bindParam(':user_id', $user_id);
        return $stmt->execute();
    }

    public static function prenomNomExists($prenom, $nom, $idUtilisateur = null) {
        $pdo = Connexion::pdo();
        $query = "SELECT COUNT(*) FROM utilisateur WHERE user_prenom = :prenom AND user_nom = :nom";
        $params = ['prenom' => $prenom, 'nom' => $nom];

        if ($idUtilisateur !== null) {
            $query .= " AND user_id != :id";
            $params['id'] = $idUtilisateur;
        }

        $stmt = $pdo->prepare($query);
        $stmt->execute($params);
        $count = $stmt->fetchColumn();

        return $count > 0;
    }

    public static function emailExists($email, $idUtilisateur = null) {
        $pdo = Connexion::pdo();
        $query = "SELECT COUNT(*) FROM utilisateur WHERE user_mail = :email";
        $params = ['email' => $email];

        if ($idUtilisateur !== null) {
            $query .= " AND user_id != :id";
            $params['id'] = $idUtilisateur;
        }

        $stmt = $pdo->prepare($query);
        $stmt->execute($params);
        $count = $stmt->fetchColumn();

        return $count > 0;
    }

    public static function siMailExisteGrp($email, $groupeId) {
        $pdo = Connexion::pdo();
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM utilisateur WHERE user_mail = :email AND user_id IN (SELECT user_id FROM membre WHERE grp_id = :groupeId)");
        $stmt->execute(['email' => $email, 'groupeId' => $groupeId]);
        return $stmt->fetchColumn() > 0;
    }

    public static function getNameUser($email) {
        $pdo = Connexion::pdo();
        $stmt = $pdo->prepare("SELECT * FROM utilisateur WHERE user_mail = :email");
        $stmt->execute(['email' => $email]);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        return $data ? new self($data['user_id'], $data['user_mail'], $data['user_mdp'], $data['user_prenom'], $data['user_nom'], $data['adr_id']) : null;
    }
}
?>
