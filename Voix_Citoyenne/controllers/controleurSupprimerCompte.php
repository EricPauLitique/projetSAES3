<?php
session_start();
require_once(__DIR__ . "/../config/connexion.php");
require_once(__DIR__ . "/../modele/membre.php");
require_once(__DIR__ . "/../modele/utilisateur.php");
require_once(__DIR__ . "/../modele/groupe.php");
require_once(__DIR__ . "/../modele/adresse.php");

Connexion::connect();

if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    // Vérification si l'ID utilisateur est passé dans la requête POST
    if (isset($_SESSION['id'])) {
        $idUtilisateur = htmlspecialchars($_SESSION['id']);
        $prenom = htmlspecialchars($_SESSION['prenom']);
        $nom = htmlspecialchars($_SESSION['nom']);

        // Vérification si l'utilisateur est membre ou propriétaire de groupes
        if (Membre::siMembreAgrp($idUtilisateur) > 0 || Groupe::siProprioAgrp($idUtilisateur) > 0) {
            // Réponse JSON pour erreur
            echo json_encode(['status' => 'error', 'message' => "Erreur, Vous devez d'abord supprimer/quitter vos groupes avant de supprimer votre compte."]);
            exit;
        }

        // Suppression de l'utilisateur de la base de données
        try {
            // Récupérer l'adresse de l'utilisateur
            $utilisateur = Utilisateur::getUtilisateurById($idUtilisateur);
            if ($utilisateur) {
                $adrId = $utilisateur->get('adr_id');   

                // Supprimer l'utilisateur
                $result = Utilisateur::deleteUtilisateur($idUtilisateur);

                if ($result) {
                    // Vérifier si l'adresse est utilisée par d'autres utilisateurs
                    if (!Utilisateur::isAdresseUsedByOthers($adrId, $idUtilisateur)) {
                        // Supprimer l'adresse associée
                        Adresse::deleteAdresse($adrId);
                    }
                    // Définir un message de succès dans la session
                    $_SESSION['message'] = 'Votre compte ' . $prenom . ' ' . $nom . ' a été supprimé avec succès.';

                    // Réponse JSON pour succès
                    echo json_encode(['status' => 'success', 'message' => 'Votre compte a été supprimé avec succès.']);
                    session_destroy(); // Détruire la session après suppression du compte
                    exit;
                } else {
                    echo json_encode(['status' => 'error', 'message' => 'Erreur lors de la suppression du compte.']);
                    exit;
                }
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Utilisateur non trouvé.']);
                exit;
            }
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => 'Erreur : ' . $e->getMessage()]);
            exit;
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'ID utilisateur manquant.']);
        exit;
    }
}
?>