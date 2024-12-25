<?php
require_once("../config/connexion.php");

class Adresse {
    protected int $adr_id;
    protected int $adr_cp;
    protected string $adr_ville;
    protected string $adr_rue;
    protected ?int $adr_num; // Peut être NULL

    // Constructeur
    public function __construct(
        int $adr_id = NULL,
        int $adr_cp = NULL,
        string $adr_ville = NULL,
        string $adr_rue = NULL,
        ?int $adr_num = NULL
    ) {
        if (!is_null($adr_id)) {
            $this->adr_id = $adr_id;
            $this->adr_cp = $adr_cp;
            $this->adr_ville = $adr_ville;
            $this->adr_rue = $adr_rue;
            $this->adr_num = $adr_num;
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
        return "{$this->adr_id}, {$this->adr_num}, {$this->adr_rue}, {$this->adr_cp} {$this->adr_ville}";
    }

    // Récupérer toutes les adresses
    public static function getAllAdresse() {
        $requete = "SELECT * FROM adresse";
        try {
            $resultat = connexion::pdo()->query($requete);
            $resultat->setFetchMode(PDO::FETCH_CLASS, "Adresse");
            return $resultat->fetchAll();
        } catch (PDOException $e) {
            echo 'Erreur : ' . $e->getMessage();
            return null;
        }
    }

    // Récupérer une adresse par son ID
    public static function getAdresseById($id) {
        try {
            $requete = "SELECT * FROM adresse WHERE adr_id = :adr_id";
            $stmt = connexion::pdo()->prepare($requete);
            $stmt->execute(['adr_id' => $id]);
            $stmt->setFetchMode(PDO::FETCH_CLASS, 'Adresse');
            return $stmt->fetch();
        } catch (PDOException $e) {
            echo 'Erreur : ' . $e->getMessage();
            return null;
        }
    }

    // Supprimer une adresse par son ID
    public static function deleteAdresse($id) {
        try {
            $requete = "DELETE FROM adresse WHERE adr_id = :adr_id";
            $stmt = connexion::pdo()->prepare($requete);
            return $stmt->execute(['adr_id' => $id]);
        } catch (PDOException $e) {
            echo 'Erreur : ' . $e->getMessage();
            return false;
        }
    }

    // Mettre à jour une adresse
    public static function updateAdresse(Adresse $adresse) {
        try {
            $requete = "UPDATE adresse SET adr_cp = :adr_cp, adr_ville = :adr_ville, adr_rue = :adr_rue, adr_num = :adr_num WHERE adr_id = :adr_id";
            $stmt = connexion::pdo()->prepare($requete);
            return $stmt->execute([
                'adr_cp' => $adresse->get('adr_cp'),
                'adr_ville' => $adresse->get('adr_ville'),
                'adr_rue' => $adresse->get('adr_rue'),
                'adr_num' => $adresse->get('adr_num'),
                'adr_id' => $adresse->get('adr_id')
            ]);
        } catch (PDOException $e) {
            throw new Exception('Erreur lors de la mise à jour de l\'adresse : ' . $e->getMessage());
        }
    }
}
?>