<?php
require_once(__DIR__ . "/../config/connexion.php");

class NotifUtilisateur implements JsonSerializable {
    protected int $user_id;
    protected int $notif_id;
    protected ?string $notif_date; 

    // Constructeur
    public function __construct(int $user_id = NULL, int $notif_id = NULL, ?string $notif_date = NULL) {
        if (!is_null($user_id) && !is_null($notif_id)) {
            $this->user_id = $user_id;
            $this->notif_id = $notif_id;
            $this->notif_date = $notif_date ?? date('Y-m-d H:i:s'); // Utiliser la date actuelle si non fournie
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
        return "Utilisateur ID {$this->user_id} - Notification ID {$this->notif_id}, Date: {$this->notif_date}";
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

    // Récupérer les notifications par date et utilisateur
    public static function getNotificationsByUserIdAndDate(int $user_id, string $date) {
        try {
            $requete = "SELECT n.* FROM notifUtilisateur nu
                        JOIN notification n ON nu.notif_id = n.notif_id
                        WHERE nu.user_id = :user_id AND DATE(n.notif_date) = :notif_date";
            $stmt = connexion::pdo()->prepare($requete);
            $stmt->execute(['user_id' => $user_id, 'notif_date' => $date]);
            $stmt->setFetchMode(PDO::FETCH_CLASS, "Notification");
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            echo 'Erreur : ' . $e->getMessage();
            return null;
        }
    }

    // Associer une notification à un utilisateur
    public static function addNotifUtilisateur(int $user_id, int $notif_id, string $notif_date) {
        try {
            $requete = "INSERT INTO notifUtilisateur (user_id, notif_id, notif_date) VALUES (:user_id, :notif_id, :notif_date)";
            $stmt = connexion::pdo()->prepare($requete);
            return $stmt->execute(['user_id' => $user_id, 'notif_id' => $notif_id, 'notif_date' => $notif_date]);
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