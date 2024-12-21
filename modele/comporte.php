<?php


require_once("../config/connexion.php");

class Comporte {
    protected int $grp_id;
    protected int $theme_id;
    protected float $lim_theme;

    // Constructeur
    public function __construct(
        int $grp_id = NULL,
        int $theme_id = NULL,
        float $lim_theme = NULL
    ) {
        if (!is_null($grp_id)) {
            $this->grp_id = $grp_id;
            $this->theme_id = $theme_id;
            $this->lim_theme = $lim_theme;
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
        return "Groupe {$this->grp_id}, Thème {$this->theme_id}, Limite du thème : {$this->lim_theme}";
    }

    // Récupérer toutes les associations
    public static function getAllComporte() {
        $requete = "SELECT * FROM comporte";
        try {
            $resultat = connexion::pdo()->query($requete);
            $resultat->setFetchMode(PDO::FETCH_CLASS, "Comporte");
            return $resultat->fetchAll();
        } catch (PDOException $e) {
            echo 'Erreur : ' . $e->getMessage();
            return null;
        }
    }

    // Récupérer une association par IDs
    public static function getComporteById($grp_id, $theme_id) {
        try {
            $requete = "SELECT * FROM comporte WHERE grp_id = :grp_id AND theme_id = :theme_id";
            $stmt = connexion::pdo()->prepare($requete);
            $stmt->execute(['grp_id' => $grp_id, 'theme_id' => $theme_id]);
            $stmt->setFetchMode(PDO::FETCH_CLASS, 'Comporte');
            return $stmt->fetch();
        } catch (PDOException $e) {
            echo 'Erreur : ' . $e->getMessage();
            return null;
        }
    }
}

?>
