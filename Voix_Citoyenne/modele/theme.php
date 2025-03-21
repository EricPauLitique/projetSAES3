<?php
require_once(__DIR__ . "/../config/connexion.php");

class Theme implements JsonSerializable {
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

    // Implémentation de JsonSerializable
    public function jsonSerialize() {
        return get_object_vars($this);
    }

    // Méthode magique __toString
    public function __toString() {
        return 'Thème ' . $this->theme_id . ' : ' . $this->theme_nom;
    }

    // Récupérer tous les thèmes
    public static function getAllThemes() {
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
    public static function getThemeById($themeId) {
        try {
            $requete = "SELECT * FROM theme WHERE theme_id = :theme_id";
            $stmt = connexion::pdo()->prepare($requete);
            $stmt->execute(['theme_id' => $themeId]);
            $stmt->setFetchMode(PDO::FETCH_CLASS, 'Theme');
            return $stmt->fetch();
        } catch (PDOException $e) {
            echo 'Erreur : ' . $e->getMessage();
            return null;
        }
    }

    // Récupérer un thème par son nom
    public static function getThemeByName($themeNom) {
        try {
            $requete = "SELECT * FROM theme WHERE theme_nom = :theme_nom";
            $stmt = connexion::pdo()->prepare($requete);
            $stmt->execute(['theme_nom' => $themeNom]);
            $stmt->setFetchMode(PDO::FETCH_CLASS, 'Theme');
            return $stmt->fetch() ?: false;
        } catch (PDOException $e) {
            echo 'Erreur : ' . $e->getMessage();
            return false;
        }
    }

    // Créer un nouveau thème
    public static function createTheme($idTheme, $theme_nom) {
        try {
            $requete = "INSERT INTO theme (theme_id, theme_nom) VALUES (:theme_id, :theme_nom)";
            $stmt = Connexion::pdo()->prepare($requete);
            $stmt->execute(['theme_id' => $idTheme, 'theme_nom' => $theme_nom]);
            return Connexion::pdo()->lastInsertId();
        } catch (PDOException $e) {
            echo 'Erreur : ' . $e->getMessage();
            return false;
        }
    }

    // Mettre à jour un thème
    public static function updateTheme($themeId, $theme_nom) {
        try {
            $requete = "UPDATE theme SET theme_nom = :theme_nom WHERE theme_id = :theme_id";
            $stmt = Connexion::pdo()->prepare($requete);
            $stmt->execute(['theme_nom' => $theme_nom, 'theme_id' => $themeId]);
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            echo 'Erreur : ' . $e->getMessage();
            return false;
        }
    }

    // Supprimer un thème par son ID
    public static function deleteThemeById($themeId) {
        try {
            $requete = "DELETE FROM theme WHERE theme_id = :theme_id";
            $stmt = Connexion::pdo()->prepare($requete);
            $stmt->execute(['theme_id' => $themeId]);
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            echo 'Erreur : ' . $e->getMessage();
            return false;
        }
    }

    // LE max de thème
    public static function getMaxTheme() {
        try {
            $requete = "SELECT MAX(theme_id) as max_id FROM theme";
            $stmt = Connexion::pdo()->query($requete);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['max_id'] ?? 0;
        } catch (PDOException $e) {
            echo 'Erreur : ' . $e->getMessage();
            return 0;
        }
    }
}
?>