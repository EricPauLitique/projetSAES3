<?php 
require_once("../config/connexion.php");

class Membre {
    protected int $user_id;
    protected int $grp_id;
    protected ?bool $coche_reac;      // Peut être NULL
    protected ?bool $coche_new_prop; // Peut être NULL
    protected ?bool $coche_res_vote; // Peut être NULL
    protected ?string $role;         // Peut être NULL

    // Constructeur
    public function __construct(
        int $user_id = NULL,
        int $grp_id = NULL,
        ?bool $coche_reac = NULL,
        ?bool $coche_new_prop = NULL,
        ?bool $coche_res_vote = NULL,
        ?string $role = NULL
    ) {
        if (!is_null($user_id) && !is_null($grp_id)) {
            $this->user_id = $user_id;
            $this->grp_id = $grp_id;
            $this->coche_reac = $coche_reac;
            $this->coche_new_prop = $coche_new_prop;
            $this->coche_res_vote = $coche_res_vote;
            $this->role = $role;
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
                Résultats des votes : {$this->coche_res_vote}, Rôle : {$this->role}";
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
}


?>