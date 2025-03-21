<?php

require_once(__DIR__ . "/../config/connexion.php");

class Comporte implements JsonSerializable {
    protected int $grp_id;
    protected int $theme_id;
    protected int $lim_theme;

    // Constructeur
    public function __construct(
        int $grp_id = NULL,
        int $theme_id = NULL,
        int $lim_theme = NULL
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

    // Implémentation de JsonSerializable
    public function jsonSerialize() {
        return get_object_vars($this);
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
            $requete = "SELECT * FROM comporte NATURAL JOIN theme WHERE grp_id = :grp_id";
            $stmt = connexion::pdo()->prepare($requete);
            $stmt->execute(['grp_id' => $id]);
            $stmt->setFetchMode(PDO::FETCH_CLASS, 'Theme');
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            echo 'Erreur : ' . $e->getMessage();
            return null;
        }
    }

    // Ajouter un thème à un groupe
    public static function addThemeToGroup($grp_id, $theme_id, $lim_theme) {
        try {
            $requete = "INSERT INTO comporte (grp_id, theme_id, lim_theme) VALUES (:grp_id, :theme_id, :lim_theme)";
            $stmt = Connexion::pdo()->prepare($requete);
            $stmt->execute(['grp_id' => $grp_id, 'theme_id' => $theme_id, 'lim_theme' => $lim_theme]);
            return true;
        } catch (PDOException $e) {
            echo 'Erreur : ' . $e->getMessage();
            return false;
        }
    }

    // Vérifier si un thème existe déjà dans un groupe
    public static function existsThemeInGroup($grp_id, $theme_id) {
        try {
            $requete = "SELECT COUNT(*) FROM comporte WHERE grp_id = :grp_id AND theme_id = :theme_id";
            $stmt = Connexion::pdo()->prepare($requete);
            $stmt->execute(['grp_id' => $grp_id, 'theme_id' => $theme_id]);
            return $stmt->fetchColumn() > 0;
        } catch (PDOException $e) {
            echo 'Erreur : ' . $e->getMessage();
            return false;
        }
    }

    // Mettre à jour la limite du thème existant
    public static function updateThemeLimit($grp_id, $theme_id, $lim_theme) {
        try {
            $requete = "UPDATE comporte SET lim_theme = lim_theme + :lim_theme WHERE grp_id = :grp_id AND theme_id = :theme_id";
            $stmt = Connexion::pdo()->prepare($requete);
            $stmt->execute(['lim_theme' => $lim_theme, 'grp_id' => $grp_id, 'theme_id' => $theme_id]);
            return true;
        } catch (PDOException $e) {
            echo 'Erreur : ' . $e->getMessage();
            return false;
        }
    }

    // Mettre à jour uniquement la limite du thème
    public static function updateThemeLimitOnly($grp_id, $theme_id, $lim_theme) {
        try {
            $requete = "UPDATE comporte SET lim_theme = :lim_theme WHERE grp_id = :grp_id AND theme_id = :theme_id";
            $stmt = Connexion::pdo()->prepare($requete);
            $stmt->execute(['lim_theme' => $lim_theme, 'grp_id' => $grp_id, 'theme_id' => $theme_id]);
            return $stmt->rowCount() > 0;
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
