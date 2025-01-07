<?php
require_once(__DIR__ . "/../config/connexion.php");

class Proposition implements JsonSerializable {
    protected int $prop_id;
    protected string $prop_titre;
    protected string $prop_desc;
    protected ?string $prop_date_min; // Peut être NULL
    protected int $user_id;
    protected int $theme_id;
    protected ?float $prop_cout; // Peut être NULL

    // Constructeur
    public function __construct(
        int $prop_id = NULL,
        string $prop_titre = NULL,
        string $prop_desc = NULL,
        ?string $prop_date_min = NULL,
        int $user_id = NULL,
        int $theme_id = NULL,
        ?float $prop_cout = NULL
    ) {
        if (!is_null($prop_id)) {
            $this->prop_id = $prop_id;
            $this->prop_titre = $prop_titre;
            $this->prop_desc = $prop_desc;
            $this->prop_date_min = $prop_date_min;
            $this->user_id = $user_id;
            $this->theme_id = $theme_id;
            $this->prop_cout = $prop_cout;
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
        return "Proposition {$this->prop_id}: {$this->prop_titre} ({$this->prop_desc})";
    }

    // Implémentation de JsonSerializable
    public function jsonSerialize() {
        return get_object_vars($this);
    }

    // Récupérer toutes les propositions
    public static function getAllPropositions() {
        $requete = "SELECT * FROM proposition";
        try {
            $resultat = connexion::pdo()->query($requete);
            $resultat->setFetchMode(PDO::FETCH_CLASS, "Proposition");
            return $resultat->fetchAll();
        } catch (PDOException $e) {
            echo 'Erreur : ' . $e->getMessage();
            return null;
        }
    }

    // Récupérer une proposition par son ID
    public static function getPropositionById($id) {
        try {
            $requete = "SELECT * FROM proposition WHERE prop_id = :prop_id";
            $stmt = connexion::pdo()->prepare($requete);
            $stmt->execute(['prop_id' => $id]);
            $stmt->setFetchMode(PDO::FETCH_CLASS, 'Proposition');
            return $stmt->fetch();
        } catch (PDOException $e) {
            echo 'Erreur : ' . $e->getMessage();
            return null;
        }
    }

    public static function getPropositionsByGroupeId($id) {
        try {
            $requete = "SELECT * FROM proposition natural join theme natural join comporte WHERE grp_id = :grp_id";
            $stmt = connexion::pdo()->prepare($requete);
            $stmt->execute(['grp_id' => $id]);
            $stmt->setFetchMode(PDO::FETCH_CLASS, 'Proposition');
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            echo 'Erreur : ' . $e->getMessage();
            return null;
        }
    }

    // Récuperer le dernier id proposition
    public static function getLastId() {
        try {
            $requete = "SELECT MAX(prop_id) as last_id FROM proposition";
            $stmt = connexion::pdo()->prepare($requete);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC)['last_id'];
        } catch (PDOException $e) {
            echo 'Erreur : ' . $e->getMessage();
            return null;
        }
    }

    // Ajouter une proposition
    public static function addProposition($prop_titre, $prop_desc, $prop_date_min, $user_id, $theme_id, $prop_cout) {
        try {
            $lastId = self::getLastId();
            $newId = $lastId ? $lastId + 1 : 1;
            $requete = "INSERT INTO proposition (prop_id, prop_titre, prop_desc, prop_date_min, user_id, theme_id, prop_cout) VALUES (:prop_id, :prop_titre, :prop_desc, :prop_date_min, :user_id, :theme_id, :prop_cout)";
            $stmt = connexion::pdo()->prepare($requete);
            $stmt->execute([
                'prop_id' => $newId,
                'prop_titre' => $prop_titre,
                'prop_desc' => $prop_desc,
                'prop_date_min' => $prop_date_min,
                'user_id' => $user_id,
                'theme_id' => $theme_id,
                'prop_cout' => $prop_cout
            ]);
            return $newId;
        } catch (PDOException $e) {
            echo 'Erreur : ' . $e->getMessage();
            return false;
        }
    }
}
?>