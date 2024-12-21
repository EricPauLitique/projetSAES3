<?php
require_once("../config/connexion.php");

class Vote {
    protected int $vote_id;
    protected string $vote_type_scrutin;
    protected int $vote_duree;
    protected ?int $vote_valide; // Peut être NULL
    protected int $prop_id;

    // Constructeur
    public function __construct(
        int $vote_id = NULL,
        string $vote_type_scrutin = NULL,
        int $vote_duree = NULL,
        ?int $vote_valide = NULL,
        int $prop_id = NULL
    ) {
        if (!is_null($vote_id)) {
            $this->vote_id = $vote_id;
            $this->vote_type_scrutin = $vote_type_scrutin;
            $this->vote_duree = $vote_duree;
            $this->vote_valide = $vote_valide;
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
        return sprintf(
            "Vote ID: %d, Type: %s, Durée: %d, Valide: %s, Proposition ID: %d",
            $this->vote_id,
            $this->vote_type_scrutin,
            $this->vote_duree,
            is_null($this->vote_valide) ? "NULL" : ($this->vote_valide ? "Oui" : "Non"),
            $this->prop_id
        );
    }

    // Récupérer tous les votes
    public static function getAllVote() {
        $requete = "SELECT * FROM vote";
        try {
            $resultat = connexion::pdo()->query($requete);
            $resultat->setFetchMode(PDO::FETCH_CLASS, "Vote");
            return $resultat->fetchAll();
        } catch (PDOException $e) {
            echo 'Erreur : ' . $e->getMessage();
            return null;
        }
    }

    // Récupérer un vote par son ID
    public static function getVoteById($id) {
        try {
            $requete = "SELECT * FROM vote WHERE vote_id = :vote_id";
            $stmt = connexion::pdo()->prepare($requete);
            $stmt->execute(['vote_id' => $id]);
            $stmt->setFetchMode(PDO::FETCH_CLASS, 'Vote');
            return $stmt->fetch();
        } catch (PDOException $e) {
            echo 'Erreur : ' . $e->getMessage();
            return null;
        }
    }
}

?>