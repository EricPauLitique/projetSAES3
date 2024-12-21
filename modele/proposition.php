<?php
require_once("../config/connexion.php");

class Proposition {
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

    // Récupérer toutes les propositions
    public static function getAllProposition() {
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
}
?>