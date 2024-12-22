<?php
require_once("../config/connexion.php");

class Groupe {
    protected int $grp_id;
    protected string $grp_nom;
    protected ?string $grp_couleur;
    protected ?string $grp_img;
    protected float $grp_lim_an;
    protected int $user_id;

    // Constructeur
    public function __construct(
        int $grp_id = NULL,
        string $grp_nom = NULL,
        ?string $grp_couleur = NULL,
        ?string $grp_img = NULL,
        float $grp_lim_an = NULL,
        int $user_id = NULL
    ) {
        if (!is_null($grp_id)) {
            $this->grp_id = $grp_id;
            $this->grp_nom = $grp_nom;
            $this->grp_couleur = $grp_couleur;
            $this->grp_img = $grp_img;
            $this->grp_lim_an = $grp_lim_an;
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

    // Méthode magique __toString
    public function afficher() {
        echo "Groupe {$this->grp_nom} Couleur : {$this->grp_couleur}, Image : {$this->grp_img}, Limite annuelle : {$this->grp_lim_an}, Utilisateur : {$this->user_id}";
    }

    // Récupérer tous les groupes
    public static function getAllGroupes() {
        $requete = "SELECT * FROM groupe";
        try {
            $resultat = connexion::pdo()->query($requete);
            $resultat->setFetchMode(PDO::FETCH_CLASS, "Groupe");
            return $resultat->fetchAll();
        } catch (PDOException $e) {
            echo 'Erreur : ' . $e->getMessage();
            return null;
        }
    }

    // Récupérer un groupe par son ID
    public static function getGroupeById($user_id) {
        $requete = "SELECT * FROM groupe WHERE user_id = :user_id";
        $stmt = connexion::pdo()->prepare($requete);
        $stmt->execute(['user_id' => $user_id]);
    
        return $stmt->fetchAll(PDO::FETCH_CLASS, 'Groupe'); // Retourne un tableau d'objets Groupe
    }

    public static function deleteGroupById($groupId) {
        $db = Connexion::pdo();  // Obtenez la connexion à la base de données
    
        try {
            // Démarrer la transaction
            $db->beginTransaction();
    
            // Supprimer les lignes associées au groupe dans la table 'comporte'
            $stmt = $db->prepare("DELETE FROM comporte WHERE grp_id = :grp_id");
            $stmt->bindParam(':grp_id', $groupId, PDO::PARAM_INT);
            $stmt->execute();
    
            // Supprimer le groupe dans la table 'groupe'
            $stmt = $db->prepare("DELETE FROM groupe WHERE grp_id = :grp_id");
            $stmt->bindParam(':grp_id', $groupId, PDO::PARAM_INT);
            $stmt->execute();
    
            // Commit de la transaction si tout s'est bien passé
            $db->commit();
            
            return true; // Suppression réussie
        } catch (PDOException $e) {
            // En cas d'erreur, annuler les changements
            $db->rollBack();
            error_log("Erreur lors de la suppression du groupe: " . $e->getMessage());
            return false; // Échec de la suppression
        }
    }



}   
        


?>