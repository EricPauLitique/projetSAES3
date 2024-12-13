<?php
// Inclure la connexion et le modèle
require_once '../config/connexion.php';
require_once '../modele/utilisateur.php';

// Classe contrôleur pour la gestion des utilisateurs
class UtilisateurController {

     // Méthode pour gérer les requêtes GET
    public function handleGetRequest() {
        // Vérifier si un login a été fourni dans l'URL
        if (isset($_GET['user_id']) && !empty($_GET['user_id'])) {
            $login = $_GET['user_id'];
            
            // Se connecter à la base de données
            Connexion::connect();

            // Appeler la méthode pour récupérer un utilisateur par login
            $utilisateur = Utilisateur::getUtilisateurByLogin($login);

            // Vérifier si l'utilisateur existe et renvoyer la réponse

            $reponse = [];
            
            if ($utilisateur) {
                // Utilisateur trouvé, renvoyer les données
                $response[] = [
                    'email' => $utilisateur->get('user_mail'),
                    'password' => $utilisateur->get('user_mdp'), // Clé renommée
                    'prenom' => $utilisateur->get('user_prenom'),
                    'nom' => $utilisateur->get('user_nom'),
                    'adr_id' => $utilisateur->get('adr_id')
                ];
                            // Retourner le tableau de réponse sous forme de JSON
            header('Content-Type: application/json');
            echo json_encode($response, JSON_PRETTY_PRINT); // JSON bien formaté pour le débogage
            }
             else {
                // Si utilisateur non trouvé
                echo json_encode(['message' => 'Utilisateur non trouvé']);
            }
        } else {
            // Si aucun login n'est fourni ou est vide
            echo json_encode(['message' => 'Login manquant ou invalide']);
        } }

        
        public function listGet() {
            // Se connecter à la base de données
            Connexion::connect();
        
            // Appeler la méthode pour récupérer tous les utilisateurs
            $tab_u = Utilisateur::getAllUtilisateur();
        
            // Tableau pour contenir les données des utilisateurs
            $response = [];
        
            // Construire le tableau de réponse
            foreach ($tab_u as $u) {
                $response[] = [
                    'user_id' => $u->get('user_id'),
                    'email' => $u->get('user_mail'),
                    'password' => $u->get('user_mdp'), // Clé renommée
                    'prenom' => $u->get('user_prenom'),
                    'nom' => $u->get('user_nom'),
                    'adr_id' => $u->get('adr_id')
                ];
            }
        
            // Retourner le tableau de réponse sous forme de JSON
            header('Content-Type: application/json');
            echo json_encode($response, JSON_PRETTY_PRINT); // JSON bien formaté pour le débogage
        }


    // Méthode pour gérer les autres types de requêtes HTTP
    public function handleRequest() {
        // Vérifier la méthode HTTP
        if ($_SERVER['REQUEST_METHOD'] == 'GET') {
            $this->listGet();
        } else {
            // Méthode non autorisée
            echo json_encode(['message' => 'Méthode non autorisée']);
        }
    }
}


// Créer une instance du contrôleur et traiter la requête
$controller = new UtilisateurController();
$controller->handleRequest();
//$controller->listGet();
?>
