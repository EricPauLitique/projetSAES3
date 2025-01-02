<?php
require_once(__DIR__ . "/../config/connexion.php");

class Vote implements JsonSerializable{
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

    // Implémentation de JsonSerializable
    public function jsonSerialize() {
        return get_object_vars($this);
    }

    // Récupérer tous les votes
    public static function getAllVotes() {
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
    public static function getVoteById($voteId) {
        try {
            $requete = "SELECT * FROM vote WHERE vote_id = :vote_id";
            $stmt = connexion::pdo()->prepare($requete);
            $stmt->execute(['vote_id' => $voteId]);
            $stmt->setFetchMode(PDO::FETCH_CLASS, 'Vote');
            return $stmt->fetch();
        } catch (PDOException $e) {
            echo 'Erreur : ' . $e->getMessage();
            return null;
        }
    }

    // Créer un nouveau vote
    public static function createVote($vote_type_scrutin, $vote_duree, $vote_valide, $prop_id) {
        try {
            $requete = "INSERT INTO vote (vote_type_scrutin, vote_duree, vote_valide, prop_id) VALUES (:vote_type_scrutin, :vote_duree, :vote_valide, :prop_id)";
            $stmt = Connexion::pdo()->prepare($requete);
            $stmt->execute(['vote_type_scrutin' => $vote_type_scrutin, 'vote_duree' => $vote_duree, 'vote_valide' => $vote_valide, 'prop_id' => $prop_id]);
            return Connexion::pdo()->lastInsertId();
        } catch (PDOException $e) {
            echo 'Erreur : ' . $e->getMessage();
            return false;
        }
    }

    // Mettre à jour un vote
    public static function updateVote(Vote $vote) {
        try {
            $requete = "UPDATE vote SET vote_type_scrutin = :vote_type_scrutin, vote_duree = :vote_duree, vote_valide = :vote_valide, prop_id = :prop_id WHERE vote_id = :vote_id";
            $stmt = Connexion::pdo()->prepare($requete);
            $stmt->execute([
                'vote_type_scrutin' => $vote->vote_type_scrutin,
                'vote_duree' => $vote->vote_duree,
                'vote_valide' => $vote->vote_valide,
                'prop_id' => $vote->prop_id,
                'vote_id' => $vote->vote_id
            ]);
            return true;
        } catch (PDOException $e) {
            echo 'Erreur : ' . $e->getMessage();
            return false;
        }
    }

    // Supprimer un vote
    public static function deleteVote($vote_id) {
        try {
            $requete = "DELETE FROM vote WHERE vote_id = :vote_id";
            $stmt = Connexion::pdo()->prepare($requete);
            $stmt->execute(['vote_id' => $vote_id]);
            return true;
        } catch (PDOException $e) {
            echo 'Erreur : ' . $e->getMessage();
            return false;
        }
    }
}
?>