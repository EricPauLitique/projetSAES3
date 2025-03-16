<?php
require_once(__DIR__ . "/../config/connexion.php");

class Reaction implements JsonSerializable {
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
        return $this->$attribut ?? null;
    }

    public function set($attribut, $valeur) {
        $this->$attribut = $valeur;
    }

    // Implémentation de JsonSerializable
    public function jsonSerialize() {
        return get_object_vars($this);
    }

    // Méthodes CRUD
    public static function createReaction(Reaction $reaction) {
        $pdo = Connexion::pdo();
        $stmt = $pdo->prepare("INSERT INTO reaction (reac_type, reac_img, prop_id, com_id, user_id) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$reaction->reac_type, $reaction->reac_img, $reaction->prop_id, $reaction->com_id, $reaction->user_id]);
    }

    public static function getReactionById($reac_id) {
        $pdo = Connexion::pdo();
        $stmt = $pdo->prepare("SELECT * FROM reaction WHERE reac_id = ?");
        $stmt->execute([$reac_id]);
        $stmt->setFetchMode(PDO::FETCH_CLASS, 'Reaction');
        return $stmt->fetch();
    }

    public static function getAllReactions() {
        $pdo = Connexion::pdo();
        $stmt = $pdo->query("SELECT * FROM reaction");
        return $stmt->fetchAll(PDO::FETCH_CLASS, 'Reaction');
    }

    public static function updateReaction(Reaction $reaction) {
        $pdo = Connexion::pdo();
        $stmt = $pdo->prepare("UPDATE reaction SET reac_type = ?, reac_img = ?, prop_id = ?, com_id = ?, user_id = ? WHERE reac_id = ?");
        $stmt->execute([$reaction->reac_type, $reaction->reac_img, $reaction->prop_id, $reaction->com_id, $reaction->user_id, $reaction->reac_id]);
    }

    public static function deleteReaction($reac_id) {
        $pdo = Connexion::pdo();
        $stmt = $pdo->prepare("DELETE FROM reaction WHERE reac_id = ?");
        $stmt->execute([$reac_id]);
    }
}
?>