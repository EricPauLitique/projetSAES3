<?php
require_once(__DIR__ . "/../config/connexion.php");

class Adresse {
    private ?int $adr_id;
    private string $code_postal;
    private string $ville;
    private string $numero_rue;
    private string $nom_rue;

    public function __construct(?int $adr_id, string $code_postal, string $ville, string $numero_rue, string $nom_rue) {
        $this->adr_id = $adr_id;
        $this->code_postal = $code_postal;
        $this->ville = $ville;
        $this->numero_rue = $numero_rue;
        $this->nom_rue = $nom_rue;
    }

    public function getId(): ?int {
        return $this->adr_id;
    }

    public function get($property) {
        return $this->$property;
    }

    public function set($property, $value) {
        $this->$property = $value;
    }

    public static function createAdresse($adresse) {
        $db = Connexion::pdo();
        $stmt = $db->prepare("INSERT INTO adresses (code_postal, ville, numero_rue, nom_rue) VALUES (:code_postal, :ville, :numero_rue, :nom_rue)");
        $stmt->bindParam(':code_postal', $adresse->code_postal);
        $stmt->bindParam(':ville', $adresse->ville);
        $stmt->bindParam(':numero_rue', $adresse->numero_rue);
        $stmt->bindParam(':nom_rue', $adresse->nom_rue);
        $stmt->execute();
        $adresse->adr_id = $db->lastInsertId();
    }

    public static function getAdresseById($id) {
        try {
            $requete = "SELECT * FROM adresse WHERE adr_id = :adr_id";
            $stmt = Connexion::pdo()->prepare($requete);
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