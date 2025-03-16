<?php
require_once(__DIR__ . "/../config/connexion.php");

class Commentaire implements JsonSerializable {
    protected int $com_id;
    protected string $com_txt;
    protected string $com_date; // Utilisation de string pour datetime
    protected int $user_id;
    protected int $prop_id;

    // Constructeur
    public function __construct(
        int $com_id = NULL,
        string $com_txt = NULL,
        string $com_date = NULL,
        int $user_id = NULL,
        int $prop_id = NULL
    ) {
        if (!is_null($com_id)) {
            $this->com_id = $com_id;
            $this->com_txt = $com_txt;
            $this->com_date = $com_date;
            $this->user_id = $user_id;
            $this->prop_id = $prop_id;
        }
    }

    // Méthodes GET et SET
    public function get($attribut) {
        return $this->$attribut ?? null;
    }

    public function set($attribut, $valeur) {
        $this->$attribut = $valeur;
    }

    // Méthode magique __toString
    public function __toString() {
        return "Commentaire {$this->com_id} : {$this->com_txt}, Date: {$this->com_date}, Utilisateur: {$this->user_id}, Proposition: {$this->prop_id}";
    }

    // Implémentation de JsonSerializable
    public function jsonSerialize() {
        return get_object_vars($this);
    }

    // Récupérer tous les commentaires
    public static function getAllCommentaires() {
        $requete = "SELECT * FROM commentaire";
        try {
            $resultat = connexion::pdo()->query($requete);
            $resultat->setFetchMode(PDO::FETCH_CLASS, "Commentaire");
            return $resultat->fetchAll();
        } catch (PDOException $e) {
            echo 'Erreur : ' . $e->getMessage();
            return null;
        }
    }

    // Récupérer un commentaire par son ID
    public static function getCommentaireById($id) {
        try {
            $requete = "SELECT * FROM commentaire WHERE com_id = :com_id";
            $stmt = connexion::pdo()->prepare($requete);
            $stmt->execute(['com_id' => $id]);
            $stmt->setFetchMode(PDO::FETCH_CLASS, 'Commentaire');
            return $stmt->fetch();
        } catch (PDOException $e) {
            echo 'Erreur : ' . $e->getMessage();
            return null;
        }
    }

    public static function getCommentairesByPropositionId($propId) {
        try {
            $requete = "SELECT * FROM commentaire natural join utilisateur WHERE prop_id = :prop_id";
            $stmt = connexion::pdo()->prepare($requete);
            $stmt->execute(['prop_id' => $propId]);
            $stmt->setFetchMode(PDO::FETCH_CLASS, 'Commentaire');
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            echo 'Erreur : ' . $e->getMessage();
            return null;
        }
    }

    // Afficher son id Maximum commentaire
    public static function getMaxIdCommentaire() {
        $requete = "SELECT MAX(com_id) FROM commentaire";
        try {
            $resultat = connexion::pdo()->query($requete);
            return $resultat->fetchColumn();
        } catch (PDOException $e) {
            echo 'Erreur : ' . $e->getMessage();
            return null;
        }
    }

    // Ajouter un commentaire
    public static function addCommentaire(string $com_txt, int $user_id, int $prop_id) {
        try {
            $lastId = self::getMaxIdCommentaire();
            $newId = $lastId ? $lastId + 1 : 1;
            $requete = "INSERT INTO commentaire (com_id, com_txt, com_date, user_id, prop_id) VALUES (:com_id, :com_txt, CURRENT_TIMESTAMP, :user_id, :prop_id)";
            $stmt = connexion::pdo()->prepare($requete);
            $stmt->execute([
                'com_id' => $newId,
                'com_txt' => $com_txt,
                'user_id' => $user_id,
                'prop_id' => $prop_id
            ]);
            return $newId;
        } catch (PDOException $e) {
            echo 'Erreur : ' . $e->getMessage();
            return null;
        }
    }
}
?>