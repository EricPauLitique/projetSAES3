<?php
require_once("../config/connexion.php");

class Reaction {
    protected int $reac_id;
    protected int $reac_type;
    protected ?string $reac_img; // Peut être NULL
    protected ?int $prop_id;    // Peut être NULL
    protected ?int $com_id;     // Peut être NULL
    protected int $user_id;

    // Constructeur
    public function __construct(
        int $reac_id = NULL,
        int $reac_type = NULL,
        ?string $reac_img = NULL,
        ?int $prop_id = NULL,
        ?int $com_id = NULL,
        int $user_id = NULL
    ) {
        if (!is_null($reac_id)) {
            $this->reac_id = $reac_id;
            $this->reac_type = $reac_type;
            $this->reac_img = $reac_img;
            $this->prop_id = $prop_id;
            $this->com_id = $com_id;
            $this->user_id = $user_id;
        }
    }

    // Méthodes GET et SET
    public function get($attribut) {
        return $this->$attribut;
    }

    public function set($attribut, $valeur) {
        $this->$attribut = $valeur;
    }

    // Méthode magique __toString
    public function __toString() {
        return 'Reaction ' . $this->reac_id . ' : Type = ' . $this->reac_type . 
               ', Image = ' . ($this->reac_img ?? 'Aucune') .
               ', Utilisateur = ' . $this->user_id;
    }

    // Récupérer toutes les réactions
    public static function getAllReaction() {
        $requete = "SELECT * FROM reaction";
        try {
            $resultat = connexion::pdo()->query($requete);
            $resultat->setFetchMode(PDO::FETCH_CLASS, "Reaction");
            return $resultat->fetchAll();
        } catch (PDOException $e) {
            echo 'Erreur : ' . $e->getMessage();
            return null;
        }
    }

    // Récupérer une réaction par son ID
    public static function getReactionById($id) {
        try {
            $requete = "SELECT * FROM reaction WHERE reac_id = :reac_id";
            $stmt = connexion::pdo()->prepare($requete);
            $stmt->execute(['reac_id' => $id]);
            $stmt->setFetchMode(PDO::FETCH_CLASS, 'Reaction');
            return $stmt->fetch();
        } catch (PDOException $e) {
            echo 'Erreur : ' . $e->getMessage();
            return null;
        }
    }
}
/*
Connexion::connect();
$s = Reaction::getAllReaction();
foreach($s as $p) {
    echo $p;
    echo "<br>";
}
*/
?>