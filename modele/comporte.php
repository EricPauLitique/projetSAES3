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

    // Calculer la somme des limites des thèmes pour un groupe donné
    public static function getSumLimiteThemeByGroupId($grp_id) {
        try {
            $requete = "SELECT SUM(lim_theme) as sommeMonetaire FROM comporte WHERE grp_id = :grp_id";
            $stmt = connexion::pdo()->prepare($requete);
            $stmt->execute(['grp_id' => $grp_id]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['sommeMonetaire'] ?? 0;
        } catch (PDOException $e) {
            echo 'Erreur : ' . $e->getMessage();
            return 0;
        }
    }

    // Récupérer un thème par son ID
    public static function getThemesbyidGroupe($id) {
        try {
            $requete = "SELECT * FROM comporte Natural join theme  WHERE grp_id = :grp_id";
            $stmt = connexion::pdo()->prepare($requete);
            $stmt->execute(['grp_id' => $id]);
            $stmt->setFetchMode(PDO::FETCH_CLASS, 'Theme');
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            echo 'Erreur : ' . $e->getMessage();
            return null;
        }
    }


        // Mettre à jour le thème
        public static function updateTheme($themeId, $grp_id, $prixTheme) {
            try {
                $requete = "UPDATE theme SET grp_id = :grp_id, lim_theme = :lim_theme WHERE theme_id = :theme_id";
                $stmt = connexion::pdo()->prepare($requete);
                $stmt->execute(['theme_id' => $themeId, 'grp_id' => $grp_id, 'lim_theme' => $prixTheme]);
                return true;
            } catch (PDOException $e) {
                echo 'Erreur : ' . $e->getMessage();
                return false;
            }
        }

        // Supprimer le thème
        public static function deleteThemeGrp($themeId, $grp_id) {
            try {
                $requete = "DELETE FROM comporte WHERE theme_id = :theme_id AND grp_id = :grp_id";
                $stmt = connexion::pdo()->prepare($requete);
                $stmt->execute(['theme_id' => $themeId, 'grp_id' => $grp_id]);
                return true;
            } catch (PDOException $e) {
                echo 'Erreur : ' . $e->getMessage();
                return false;
            }
        }
    
}

?>
