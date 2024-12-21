<?php
require_once("../config/connexion.php");

class Utilisateur {
    protected int $user_id;
    protected string $user_mail;
    protected string $user_mdp;
    protected string $user_prenom;
    protected string $user_nom;
    protected int $adr_id;

    // GET et SET
    public function get($attribut) {
        return $this->$attribut;
    }
    
    public function set($attribut, $valeur) {
        $this->$attribut = $valeur;
    }

    // Constructeur
    public function __construct(int $user_id = NULL, string $user_mail = NULL, string $user_mdp = NULL, string $user_prenom = NULL, string $user_nom = NULL, int $adr_id = NULL) {
        if (!is_null($user_id)) {
            $this->user_id = $user_id;
            $this->user_mail = $user_mail;
            $this->user_mdp = $user_mdp;
            $this->user_prenom = $user_prenom;
            $this->user_nom = $user_nom;
            $this->adr_id = $adr_id;
        }
    }

    // Méthode afficher
    public function afficher() {
        echo 'utilisateur ', $this->get("user_id"), ' (', $this->get("user_prenom"), ' ', $this->get("user_nom"), '), email = ', $this->get("user_mail");
    }

    // Récupérer tous les utilisateurs
    public static function getAllUtilisateur() {
        $requete = "SELECT * FROM utilisateur";
        $resultat = connexion::pdo()->query($requete);
        $resultat->setFetchMode(PDO::FETCH_CLASS, "Utilisateur");
        return $resultat->fetchAll();
    }

    // Récupérer un utilisateur par son mail
    public static function getUtilisateurByLogin($l) {
        try {
            $requete = "SELECT * FROM utilisateur WHERE user_id = :user_id";
            $stmt = connexion::pdo()->prepare($requete);
            $stmt->execute(['user_id' => $l]);
            $stmt->setFetchMode(PDO::FETCH_CLASS, 'Utilisateur');
            return $stmt->fetch();
        } catch (PDOException $e) {
            echo 'Erreur : ' . $e->getMessage();
            return null;
        }
    }
}

?>
