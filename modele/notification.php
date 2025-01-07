<?php

require_once(__DIR__ . "/../config/connexion.php");

class Notification implements JsonSerializable {   
    protected int $notif_id;
    protected ?string $notif_contenu; // Peut être NULL
    protected ?string $notif_date; // Peut être NULL

    // Constructeur
    public function __construct(int $notif_id = NULL, ?string $notif_contenu = NULL, ?string $notif_date = NULL) {
        if (!is_null($notif_id)) {
            $this->notif_id = $notif_id;
            $this->notif_contenu = $notif_contenu;
            $this->notif_date = $notif_date;
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
        return "Notification ID {$this->notif_id} : {$this->notif_contenu}, Date: {$this->notif_date}";
    }

    // Implémentation de JsonSerializable
    public function jsonSerialize() {
        return get_object_vars($this);
    }

    // Récupérer toutes les notifications
    public static function getAllNotifications() {
        $requete = "SELECT * FROM notification";
        try {
            $resultat = connexion::pdo()->query($requete);
            $resultat->setFetchMode(PDO::FETCH_CLASS, "Notification");
            return $resultat->fetchAll();
        } catch (PDOException $e) {
            echo 'Erreur : ' . $e->getMessage();
            return null;
        }
    }

    // Récupérer une notification par ID
    public static function getNotificationById(int $notif_id) {
        try {
            $requete = "SELECT * FROM notification WHERE notif_id = :notif_id";
            $stmt = connexion::pdo()->prepare($requete);
            $stmt->execute(['notif_id' => $notif_id]);
            $stmt->setFetchMode(PDO::FETCH_CLASS, 'Notification');
            return $stmt->fetch();
        } catch (PDOException $e) {
            echo 'Erreur : ' . $e->getMessage();
            return null;
        }
    }

    // Ajouter une nouvelle notification
    public static function addNotification(string $notif_contenu, string $notif_date) {
        try {
            $requete = "INSERT INTO notification (notif_contenu, notif_date) VALUES (:notif_contenu, :notif_date)";
            $stmt = connexion::pdo()->prepare($requete);
            return $stmt->execute(['notif_contenu' => $notif_contenu, 'notif_date' => $notif_date]);
        } catch (PDOException $e) {
            echo 'Erreur : ' . $e->getMessage();
            return false;
        }
    }

    // Supprimer une notification par ID
    public static function deleteNotification(int $notif_id) {
        try {
            $requete = "DELETE FROM notification WHERE notif_id = :notif_id";
            $stmt = connexion::pdo()->prepare($requete);
            return $stmt->execute(['notif_id' => $notif_id]);
        } catch (PDOException $e) {
            echo 'Erreur : ' . $e->getMessage();
            return false;
        }
    }
}
?>