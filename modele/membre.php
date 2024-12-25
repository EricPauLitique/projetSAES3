<?php 
require_once("../config/connexion.php");

class Membre {
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

    // Méthode magique __toString
    public function __toString() {
        return "Membre : Utilisateur {$this->user_id}, Groupe {$this->grp_id}, 
                Réactions : {$this->coche_reac}, Nouvelles propositions : {$this->coche_new_prop}, 
                Résultats des votes : {$this->coche_res_vote}, Rôle : {$this->role}, 
                Prénom : {$this->prenom}, Nom : {$this->nom}";
    }

    // Récupérer tous les membres
    public static function getAllMembres() {
        $requete = "SELECT * FROM membre";
        try {
            $resultat = connexion::pdo()->query($requete);
            $resultat->setFetchMode(PDO::FETCH_CLASS, "Membre");
            return $resultat->fetchAll();
        } catch (PDOException $e) {
            echo 'Erreur : ' . $e->getMessage();
            return null;
        }
    }

    // Récupérer un membre spécifique par `user_id` et `grp_id`
    public static function getMembreByIds(int $user_id, int $grp_id) {
        try {
            $requete = "SELECT * FROM membre WHERE user_id = :user_id AND grp_id = :grp_id";
            $stmt = connexion::pdo()->prepare($requete);
            $stmt->execute(['user_id' => $user_id, 'grp_id' => $grp_id]);
            $stmt->setFetchMode(PDO::FETCH_CLASS, 'Membre');
            return $stmt->fetch();
        } catch (PDOException $e) {
            echo 'Erreur : ' . $e->getMessage();
            return null;
        }
    }

    // Récupérer les groupes par `user_id`
    public static function getGrpById(int $user_id) {
        try {
            $requete = "SELECT * FROM membre m INNER JOIN utilisateur u ON u.user_id = m.user_id INNER JOIN groupe g ON g.grp_id = m.grp_id WHERE u.user_id = :user_id";
            $stmt = connexion::pdo()->prepare($requete);
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
            $stmt = connexion::pdo()->prepare($requete);
            $stmt->execute(['user_id' => $user_id, 'grp_id' => $grp_id]);
            
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
            $requete = "SELECT m.*, u.user_prenom, u.user_nom, role FROM membre m INNER JOIN utilisateur u ON u.user_id = m.user_id WHERE m.grp_id = :grp_id";
            $stmt = connexion::pdo()->prepare($requete);
            $stmt->execute(['grp_id' => $grp_id]);
            $stmt->setFetchMode(PDO::FETCH_CLASS, 'Membre');
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            echo 'Erreur : ' . $e->getMessage();
            return null;
        }
    }

    // Ajouter un nouveau membre
    public static function addMembre(array $data) {
        try {
            $requete = "INSERT INTO membre (user_id, grp_id, coche_reac, coche_new_prop, coche_res_vote, role) 
                        VALUES (:user_id, :grp_id, :coche_reac, :coche_new_prop, :coche_res_vote, :role)";
            $stmt = connexion::pdo()->prepare($requete);
            return $stmt->execute($data);
        } catch (PDOException $e) {
            echo 'Erreur : ' . $e->getMessage();
            return false;
        }
    }

    // Méthode pour supprimer un membre par `user_id` et `grp_id`
    public static function deleteMembre(int $user_id, int $grp_id) {
        try {
            $requete = "DELETE FROM membre WHERE user_id = :user_id AND grp_id = :grp_id";
            $stmt = connexion::pdo()->prepare($requete);
            $stmt->execute(['user_id' => $user_id, 'grp_id' => $grp_id]);

            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            throw new Exception('Erreur : ' . $e->getMessage());
        }
    }
}
?>