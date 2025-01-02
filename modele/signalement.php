<?php
require_once(__DIR__ . "/../config/connexion.php");

class Signalement implements JsonSerializable {
    protected int $sig_id;
    protected string $sig_nature;
    protected ?int $prop_id;
    protected ?int $com_id;
    protected int $user_id;

    public function get($attribut) {
        return $this->$attribut;
    }
    
    public function set($attribut, $valeur) {
        $this->$attribut = $valeur;
    }

    public function __construct(
        int $sig_id = NULL,
        string $sig_nature = NULL,
        ?int $prop_id = NULL,
        ?int $com_id = NULL,
        int $user_id = NULL
    ) {
        if (!is_null($sig_id)) {
            $this->sig_id = $sig_id;
            $this->sig_nature = $sig_nature;
            $this->prop_id = $prop_id;
            $this->com_id = $com_id;
            $this->user_id = $user_id;
        }
    }

    // Implémentation de JsonSerializable
    public function jsonSerialize() {
        return get_object_vars($this);
    }

    public function afficher() {
        echo 'Signalement ', $this->get("sig_id"), 
             ' : ', $this->get("sig_nature"), 
             ' (Utilisateur ', $this->get("user_id"), ')';
    }

    // Nouvelle méthode adresse()
    public function adresse() {
        return 'Adresse liée au signalement ' . $this->sig_id;
    }

    public static function getAllSignalement() {
        $requete = "SELECT * FROM signalement";
        try {
            $resultat = connexion::pdo()->query($requete);
            $resultat->setFetchMode(PDO::FETCH_CLASS, "Signalement");
            return $resultat->fetchAll();
        } catch (PDOException $e) {
            echo 'Erreur : ' . $e->getMessage();
            return null;
        }
    }

    public static function getSignalementById($id) {
        try {
            $requete = "SELECT * FROM signalement WHERE sig_id = :sig_id";
            $stmt = connexion::pdo()->prepare($requete);
            $stmt->execute(['sig_id' => $id]);
            $stmt->setFetchMode(PDO::FETCH_CLASS, 'Signalement');
            return $stmt->fetch();
        } catch (PDOException $e) {
            echo 'Erreur : ' . $e->getMessage();
            return null;
        }
    }
}

?>