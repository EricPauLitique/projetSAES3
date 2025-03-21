<?php
require_once(__DIR__ . "/../config/connexion.php");


class Membre implements JsonSerializable {
    protected int $user_id;
    protected int $grp_id;
    protected ?bool $coche_reac;      // Peut être NULL
    protected ?bool $coche_new_prop; // Peut être NULL
    protected ?bool $coche_res_vote; // Peut être NULL
    protected ?string $role;         // Peut être NULL
    protected ?string $prenom;       // Ajouté pour correspondre à la table utilisateur
    protected ?string $nom;          // Ajouté pour correspondre à la table utilisateur

    // Constructeur
    public function __construct(
        int $user_id = NULL,
        int $grp_id = NULL,
        ?bool $coche_reac = NULL,
        ?bool $coche_new_prop = NULL,
        ?bool $coche_res_vote = NULL,
        ?string $role = NULL,
        ?string $prenom = NULL,
        ?string $nom = NULL
    ) {
        if (!is_null($user_id) && !is_null($grp_id)) {
            $this->user_id = $user_id;
            $this->grp_id = $grp_id;
            $this->coche_reac = $coche_reac;
            $this->coche_new_prop = $coche_new_prop;
            $this->coche_res_vote = $coche_res_vote;
            $this->role = $role;
            $this->prenom = $prenom;
            $this->nom = $nom;
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

    // Méthode magique __toString
    public function __toString() {
        return "Membre : Utilisateur {$this->user_id}, Groupe {$this->grp_id}, 
                Réactions : {$this->coche_reac}, Nouvelles propositions : {$this->coche_new_prop}, 
                Résultats des votes : {$this->coche_res_vote}, Rôle : {$this->role}, 
                Prénom : {$this->prenom}, Nom : {$this->nom}";
    }

    // Récupérer tous les membres
    public static function getAllMembres() {
        $db = Connexion::pdo();
        $stmt = $db->query("SELECT * FROM membre");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Récupérer un membre spécifique par `user_id` et `grp_id`
    public static function getMembreByIds(int $user_id, int $grp_id) {
        $db = Connexion::pdo();
        $stmt = $db->prepare("SELECT * FROM membre WHERE user_id = :user_id AND grp_id = :grp_id");
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':grp_id', $grp_id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Récupérer les membres par ID utilisateur
    public static function getMembresByUserId(int $user_id) {
        try {
            $requete = "SELECT * FROM membre WHERE user_id = :user_id";
            $stmt = connexion::pdo()->prepare($requete);
            $stmt->execute(['user_id' => $user_id]);
            $stmt->setFetchMode(PDO::FETCH_CLASS, "Membre");
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            echo 'Erreur : ' . $e->getMessage();
            return null;
        }
    }

    // Récupérer les groupes par `user_id`
    public static function getGrpById(int $user_id) {
        try {
            $requete = "SELECT * FROM membre m INNER JOIN utilisateur u ON u.user_id = m.user_id INNER JOIN groupe g ON g.grp_id = m.grp_id WHERE u.user_id = :user_id";
            $stmt = Connexion::pdo()->prepare($requete);
            $stmt->execute(['user_id' => $user_id]);
            $stmt->setFetchMode(PDO::FETCH_CLASS, 'Membre');
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            echo 'Erreur : ' . $e->getMessage();
            return null;
        }
    }
    
    public static function siMembreInconnu(int $user_id, int $grp_id) {
        try {
            $requete = "SELECT count(*) as count FROM membre m INNER JOIN utilisateur u ON u.user_id = m.user_id INNER JOIN groupe g ON g.grp_id = m.grp_id WHERE m.grp_id = :grp_id AND u.user_id = :user_id";
            $stmt = Connexion::pdo()->prepare($requete);
            $stmt->execute(['user_id' => $user_id, 'grp_id' => $grp_id]);
            
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['count'];
        } catch (PDOException $e) {
            echo 'Erreur : ' . $e->getMessage();
            return null;
        }
    }

    public static function siMembreAgrp(int $user_id) {
        try {
            $requete = "SELECT count(*) as count FROM membre m INNER JOIN utilisateur u ON u.user_id = m.user_id INNER JOIN groupe g ON g.grp_id = m.grp_id WHERE u.user_id = :user_id";
            $stmt = Connexion::pdo()->prepare($requete);
            $stmt->execute(['user_id' => $user_id]);
            
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['count'];
        } catch (PDOException $e) {
            echo 'Erreur : ' . $e->getMessage();
            return null;
        }
    }

    // Récupérer les membres par `grp_id`
    public static function getMembresByGroupeId(int $grp_id) {
        try {
            $requete = "SELECT m.*, u.user_prenom, u.user_nom, role FROM membre m INNER JOIN utilisateur u ON u.user_id = m.user_id WHERE m.grp_id = :grp_id ORDER BY role DESC";
            $stmt = Connexion::pdo()->prepare($requete);
            $stmt->execute(['grp_id' => $grp_id]);
            $stmt->setFetchMode(PDO::FETCH_CLASS, 'Membre');
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            echo 'Erreur : ' . $e->getMessage();
            return null;
        }
    }

 /*   // Ajouter un nouveau membre
    public static function addMembre(array $data) {
        $db = Connexion::pdo();
        $stmt = $db->prepare("INSERT INTO membres (prenom, nom, email, groupe_id) VALUES (:prenom, :nom, :email, :groupe_id)");
        $stmt->bindParam(':prenom', $data['prenom']);
        $stmt->bindParam(':nom', $data['nom']);
        $stmt->bindParam(':email', $data['email']);
        $stmt->bindParam(':groupe_id', $data['groupe_id']);
        return $stmt->execute();
    }*/

    // Méthode pour supprimer un membre par `user_id` et `grp_id`
    public static function deleteMembre(int $user_id, int $grp_id) {
        $db = Connexion::pdo();
        $stmt = $db->prepare("DELETE FROM membre WHERE user_id = :user_id AND grp_id = :grp_id");
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':grp_id', $grp_id);
        return $stmt->execute();
    }

    // Méthode pour mettre à jour un membre
    public static function updateMembre($data) {
        $db = Connexion::pdo();
        $stmt = $db->prepare("UPDATE membre SET prenom = :prenom, nom = :nom, email = :email, groupe_id = :groupe_id WHERE user_id = :user_id AND grp_id = :grp_id");
        $stmt->bindParam(':user_id', $data['user_id']);
        $stmt->bindParam(':grp_id', $data['grp_id']);
        $stmt->bindParam(':prenom', $data['prenom']);
        $stmt->bindParam(':nom', $data['nom']);
        $stmt->bindParam(':email', $data['email']);
        $stmt->bindParam(':groupe_id', $data['groupe_id']);
        return $stmt->execute();
    }

    // Ajouter un nouveau membre avec coche_reac, coche_new_prop, coche_res_vote, role
    public static function addMembre($userId, $groupId, $cocheReac, $cocheNewProp, $cocheResVote, $role) {
        $db = Connexion::pdo();
        $stmt = $db->prepare("INSERT INTO membre (user_id, grp_id, coche_reac, coche_new_prop, coche_res_vote, role) VALUES (:user_id, :grp_id, :coche_reac, :coche_new_prop, :coche_res_vote, :role)");
        $stmt->bindParam(':user_id', $userId);
        $stmt->bindParam(':grp_id', $groupId);
        $stmt->bindParam(':coche_reac', $cocheReac);
        $stmt->bindParam(':coche_new_prop', $cocheNewProp);
        $stmt->bindParam(':coche_res_vote', $cocheResVote);
        $stmt->bindParam(':role', $role);
        $stmt->execute();
    }
}
?>