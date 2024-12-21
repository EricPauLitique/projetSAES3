<?php
require_once("../config/connexion.php");

class ChoixVote {
    protected int $user_id;
    protected int $vote_id;
    protected ?int $choix_user; // Peut être NULL

    // Constructeur
    public function __construct(
        int $user_id = NULL,
        int $vote_id = NULL,
        ?int $choix_user = NULL
    ) {
        if (!is_null($user_id)) {
            $this->user_id = $user_id;
            $this->vote_id = $vote_id;
            $this->choix_user = $choix_user;
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
        return "Utilisateur {$this->user_id} a choisi {$this->choix_user} pour le vote {$this->vote_id}";
    }

    // Récupérer tous les choix de vote
    public static function getAllChoixVote() {
        $requete = "SELECT * FROM choixVote";
        try {
            $resultat = connexion::pdo()->query($requete);
            $resultat->setFetchMode(PDO::FETCH_CLASS, "ChoixVote");
            return $resultat->fetchAll();
        } catch (PDOException $e) {
            echo 'Erreur : ' . $e->getMessage();
            return null;
        }
    }

    // Récupérer un choix de vote spécifique par user_id et vote_id
    public static function getChoixVoteById($user_id, $vote_id) {
        try {
            $requete = "SELECT * FROM choixVote WHERE user_id = :user_id AND vote_id = :vote_id";
            $stmt = connexion::pdo()->prepare($requete);
            $stmt->execute(['user_id' => $user_id, 'vote_id' => $vote_id]);
            $stmt->setFetchMode(PDO::FETCH_CLASS, 'ChoixVote');
            return $stmt->fetch();
        } catch (PDOException $e) {
            echo 'Erreur : ' . $e->getMessage();
            return null;
        }
    }
}

?>