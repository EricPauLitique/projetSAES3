<?php
require_once("../config/connexion.php");

class Theme {
    protected int $theme_id;
    protected string $theme_nom;

    // Constructeur
    public function __construct(int $theme_id = NULL, string $theme_nom = NULL) {
        if (!is_null($theme_id)) {
            $this->theme_id = $theme_id;
            $this->theme_nom = $theme_nom;
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
        return 'Thème ' . $this->theme_id . ' : ' . $this->theme_nom;
    }

    // Récupérer tous les thèmes
    public static function getAllTheme() {
        $requete = "SELECT * FROM theme";
        try {
            $resultat = connexion::pdo()->query($requete);
            $resultat->setFetchMode(PDO::FETCH_CLASS, "Theme");
            return $resultat->fetchAll();
        } catch (PDOException $e) {
            echo 'Erreur : ' . $e->getMessage();
            return null;
        }
    }

    // Récupérer un thème par son ID
    public static function getThemeById($grId) {
        try {
            $requete = "SELECT * FROM theme NATURAL JOIN comporte WHERE grp_id = :grp_id";
            $stmt = connexion::pdo()->prepare($requete);
            $stmt->execute(['grp_id' => $grId]);
            $stmt->setFetchMode(PDO::FETCH_CLASS, 'Theme');
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            echo 'Erreur : ' . $e->getMessage();
            return null;
        }
    }



}

?>