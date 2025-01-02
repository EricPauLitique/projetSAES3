<?php
session_start();
require_once(__DIR__ . "/../config/connexion.php");
require_once(__DIR__ . "/../modele/groupe.php");
require_once(__DIR__ . "/../modele/membre.php");

Connexion::connect();

// Vérifie si l'utilisateur est connecté
if (!isset($_SESSION['prenom']) || !isset($_SESSION['nom'])) {
    echo json_encode(['status' => 'error', 'message' => 'Utilisateur non connecté.']);
    exit;
}

$prenom = htmlspecialchars($_SESSION['prenom']);
$nom = htmlspecialchars($_SESSION['nom']);
$id = htmlspecialchars($_SESSION['id']);

try {
    // Récupérer les groupes dont l'utilisateur est propriétaire
    $myGrp = Groupe::getGroupeById($id);
    // Récupérer les groupes dont l'utilisateur est membre ou modérateur
    $grp = Membre::getGrpById($id);

    $groupes = [];

    // La liste de groupe dont il est propriétaire
    if (!empty($myGrp)) {
        foreach ($myGrp as $listGrp) {
            $groupes[] = [
                'id' => $listGrp->get('grp_id'),
                'nom' => strtoupper($listGrp->get('grp_nom')),
                'couleur' => htmlspecialchars($listGrp->get('grp_couleur')),
                'image' => $listGrp->get('grp_img'),
                'proprietaire' => true
            ];
        }
    }

    // La liste groupe en étant membre ou modérateur
    if (!empty($grp)) {
        foreach ($grp as $listGrp) {
            $groupes[] = [
                'id' => $listGrp->get('grp_id'),
                'nom' => strtoupper($listGrp->get('grp_nom')),
                'couleur' => htmlspecialchars($listGrp->get('grp_couleur')),
                'image' => $listGrp->get('grp_img'),
                'proprietaire' => false
            ];
        }
    }

    // Réponse JSON pour succès
    echo json_encode(['status' => 'success', 'message' => 'Groupes récupérés avec succès.', 'data' => $groupes]);
    exit;
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => 'Erreur : ' . $e->getMessage()]);
    exit;
}
?>