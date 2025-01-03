<?php
require_once(__DIR__ . "/../config/connexion.php");

class Utilisateur implements JsonSerializable {
    protected int $user_id;
    protected string $user_mail;
    protected string $user_mdp;
    protected string $user_prenom;
    protected string $user_nom;
    protected int $adr_id;

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
        $stmt = $pdo->prepare("INSERT INTO utilisateur (user_mail, user_mdp, user_prenom, user_nom, adr_id) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$utilisateur->user_mail, $utilisateur->user_mdp, $utilisateur->user_prenom, $utilisateur->user_nom, $utilisateur->adr_id]);
    }

    public static function getUtilisateurByLogin($user_id) {
        $pdo = Connexion::pdo();
        $stmt = $pdo->prepare("SELECT * FROM utilisateur WHERE user_id = ?");
        $stmt->execute([$user_id]);
        $stmt->setFetchMode(PDO::FETCH_CLASS, 'Utilisateur');
        return $stmt->fetch();
    }

    public static function getUtilisateurById($id) {
        $pdo = Connexion::PDO();
        $stmt = $pdo->prepare("SELECT * FROM utilisateur WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            return new Utilisateur($result);
        } else {
            throw new Exception("Utilisateur non trouvé.");
        }
    }

    public static function getAllUtilisateur() {
        $pdo = Connexion::pdo();
        $stmt = $pdo->query("SELECT * FROM utilisateur");
        return $stmt->fetchAll(PDO::FETCH_CLASS, 'Utilisateur');
    }

    public static function updateUtilisateur(Utilisateur $utilisateur) {
        $pdo = Connexion::pdo();
        $stmt = $pdo->prepare("UPDATE utilisateur SET user_mail = ?, user_prenom = ?, user_nom = ? WHERE user_id = ?");
        $stmt->execute([$utilisateur->user_mail, $utilisateur->user_prenom, $utilisateur->user_nom, $utilisateur->user_id]);
    }

    public static function deleteUtilisateur($user_id) {
        $pdo = Connexion::pdo();
        $stmt = $pdo->prepare("DELETE FROM utilisateur WHERE user_id = ?");
        $stmt->execute([$user_id]);
    }

    public static function prenomNomExists($prenom, $nom, $idUtilisateur = null) {
        $pdo = Connexion::PDO();
        $query = "SELECT COUNT(*) FROM utilisateur WHERE user_prenom = :prenom AND user_nom = :nom";
        $params = ['prenom' => $prenom, 'nom' => $nom];

        if ($idUtilisateur !== null) {
            $query .= " AND id != :id";
            $params['id'] = $idUtilisateur;
        }

        $stmt = $pdo->prepare($query);
        $stmt->execute($params);
        $count = $stmt->fetchColumn();

        return $count > 0;
    }

    public static function emailExists($email, $idUtilisateur = null) {
        $pdo = Connexion::PDO();
        $query = "SELECT COUNT(*) FROM utilisateur WHERE user_mail = :email";
        $params = ['email' => $email];

        if ($idUtilisateur !== null) {
            $query .= " AND id != :id";
            $params['id'] = $idUtilisateur;
        }

        $stmt = $pdo->prepare($query);
        $stmt->execute($params);
        $count = $stmt->fetchColumn();

        return $count > 0;
    }
}
?>
