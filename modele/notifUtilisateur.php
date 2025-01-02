<?php
require_once(__DIR__ . "/../config/connexion.php");

class NotifUtilisateur implements JsonSerializable {
    protected int $user_id;
    protected int $notif_id;

    // Constructeur
    public function __construct(int $user_id = NULL, int $notif_id = NULL) {
        if (!is_null($user_id) && !is_null($notif_id)) {
            $this->user_id = $user_id;
            $this->notif_id = $notif_id;
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
        return "Utilisateur ID {$this->user_id} - Notification ID {$this->notif_id}";
    }

    // Implémentation de JsonSerializable
    public function jsonSerialize() {
        return get_object_vars($this);
    }

    // Récupérer toutes les relations utilisateur-notification
    public static function getAllNotifUtilisateurs() {
        $requete = "SELECT * FROM notifUtilisateur";
        try {
            $resultat = connexion::pdo()->query($requete);
            $resultat->setFetchMode(PDO::FETCH_CLASS, "NotifUtilisateur");
            return $resultat->fetchAll();
        } catch (PDOException $e) {
            echo 'Erreur : ' . $e->getMessage();
            return null;
        }
    }

    // Récupérer les notifications d'un utilisateur
    public static function getNotificationsByUserId(int $user_id) {
        try {
            $requete = "SELECT * FROM notifUtilisateur WHERE user_id = :user_id";
            $stmt = connexion::pdo()->prepare($requete);
            $stmt->execute(['user_id' => $user_id]);
            $stmt->setFetchMode(PDO::FETCH_CLASS, "NotifUtilisateur");
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            echo 'Erreur : ' . $e->getMessage();
            return null;
        }
    }

    // Associer une notification à un utilisateur
    public static function addNotifUtilisateur(int $user_id, int $notif_id) {
        try {
            $requete = "INSERT INTO notifUtilisateur (user_id, notif_id) VALUES (:user_id, :notif_id)";
            $stmt = connexion::pdo()->prepare($requete);
            return $stmt->execute(['user_id' => $user_id, 'notif_id' => $notif_id]);
        } catch (PDOException $e) {
            echo 'Erreur : ' . $e->getMessage();
            return false;
        }
    }

    // Supprimer une association utilisateur-notification
    public static function deleteNotifUtilisateur(int $user_id, int $notif_id) {
        try {
            $requete = "DELETE FROM notifUtilisateur WHERE user_id = :user_id AND notif_id = :notif_id";
            $stmt = connexion::pdo()->prepare($requete);
            return $stmt->execute(['user_id' => $user_id, 'notif_id' => $notif_id]);
        } catch (PDOException $e) {
            echo 'Erreur : ' . $e->getMessage();
            return false;
        }
    }
}


?>