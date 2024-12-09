<?php

class Utilisateur {
    // Declarer les attributs :
    private string $login;
    private string $mdp;
    private string $nom;
    private string $prenom;
    private string $email;

    // Declarer le GET et SET :
    public function get($attribut) {
    return $this->$attribut;
    }
    
    public function set($attribut,$valeur) {
        $this->$attribut = $valeur;
    }


    // Declarer le constructeur :
    public function __construct(string $login=NULL, string $mdp=NULL, string $nom=NULL, string $prenom=NULL, string $email=NULL) {
      if (!is_null($login)){
        $this->login = $login;
        $this->mdp = $mdp;
        $this->nom = $nom;
        $this->prenom = $prenom;
        $this->email = $email;
    }
  }


    // Déclarer une méthode d'afficher
    public function afficher() {
        echo 'utilisateur ',$this->get("login"), ' (', $this->get("prenom"),' ',$this->get("nom"), '), email = ', $this->get("email") ;

    }
    

    public static function getAllUtilisateur() {
      $requete = "SELECT * FROM utilisateur1;";

      $resultat = connexion::pdo()->query($requete);
      
      $resultat->setFetchmode(PDO::FETCH_CLASS,"utilisateur");

      $mesUser = $resultat->fetchAll();

      return $mesUser;
    
    }


    public static function getUtilisateurByLogin($l) {
      try {
        $requete = "SELECT * FROM utilisateur1 WHERE login = :login";
        $stmt = connexion::pdo()->prepare($requete);
        $stmt->execute(['login' => $l]);
        
        $stmt->setFetchMode(PDO::FETCH_CLASS, 'utilisateur');
        $voiture = $stmt->fetch();
    
        return $voiture;
      } catch (PDOException $e) {
        echo 'Erreur : ' . $e->getMessage();
        return null;
      }
    }
    

}
?>

