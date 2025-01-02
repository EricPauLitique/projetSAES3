<?php
require_once(__DIR__ . "/../config/connexion.php");

class Groupe implements JsonSerializable {
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
    public function __toString() {
        return "Groupe {$this->grp_nom} Couleur : {$this->grp_couleur}, Image : {$this->grp_img}, Limite annuelle : {$this->grp_lim_an}, Utilisateur : {$this->user_id}";
    }

    // Implémentation de JsonSerializable
    public function jsonSerialize() {
        return get_object_vars($this);
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

    public static function getGroupByIdUnique($groupId) {
        $query = "SELECT * FROM groupe WHERE grp_id = :groupId";
        $stmt = connexion::pdo()->prepare($query);
        $stmt->bindParam(':groupId', $groupId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function getGroupByIdUnique2($groupId) {
        $query = "SELECT * FROM groupe WHERE grp_id = :groupId";
        $stmt = Connexion::pdo()->prepare($query);
        $stmt->bindParam(':groupId', $groupId, PDO::PARAM_INT);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_CLASS, 'Groupe');
        return $stmt->fetch();
    }

    public static function getProprio($groupId) {
        $query = "SELECT user_prenom, user_nom FROM groupe g inner join utilisateur u on (u.user_id = g.user_id) WHERE grp_id = :groupId";
        $stmt = connexion::pdo()->prepare($query);
        $stmt->bindParam(':groupId', $groupId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function siProprioInconnu($user_id, $groupId) {
        try {
            $requete = "SELECT count(*) as count FROM groupe WHERE grp_id = :groupId AND user_id = :user_id";
            $stmt = connexion::pdo()->prepare($requete);
            $stmt->execute(['user_id' => $user_id, 'groupId' => $groupId]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['count'];
        } catch (PDOException $e) {
            echo 'Erreur : ' . $e->getMessage();
            return null;
        }
    }

    public static function siProprioAgrp($user_id) {
        try {
            $requete = "SELECT count(*) as count FROM groupe WHERE user_id = :user_id";
            $stmt = connexion::pdo()->prepare($requete);
            $stmt->execute(['user_id' => $user_id]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['count'];
        } catch (PDOException $e) {
            echo 'Erreur : ' . $e->getMessage();
            return null;
        }
    }

    public static function getGroupeById($user_id) {
        $requete = "SELECT * FROM groupe WHERE user_id = :user_id";
        $stmt = connexion::pdo()->prepare($requete);
        $stmt->execute(['user_id' => $user_id]);
        return $stmt->fetchAll(PDO::FETCH_CLASS, 'Groupe');
    }

    // Supprimer un groupe et ses associations
    public static function deleteGroupById($groupId) {
        $db = Connexion::pdo();
        try {
            $db->beginTransaction();
            $stmt = $db->prepare("DELETE FROM comporte WHERE grp_id = :grp_id");
            $stmt->bindParam(':grp_id', $groupId, PDO::PARAM_INT);
            $stmt->execute();
            $stmt = $db->prepare("DELETE FROM groupe WHERE grp_id = :grp_id");
            $stmt->bindParam(':grp_id', $groupId, PDO::PARAM_INT);
            $stmt->execute();
            $db->commit();
            return true;
        } catch (PDOException $e) {
            $db->rollBack();
            error_log("Erreur lors de la suppression du groupe: " . $e->getMessage());
            return false;
        }
    }

    public static function handleImageUpload($group, $file, $groupDir = null) {
        if (!is_array($group)) {
            throw new InvalidArgumentException('Le paramètre $group doit être un tableau.');
        }
        if (!is_array($file)) {
            throw new InvalidArgumentException('Le paramètre $file doit être un tableau.');
        }
        $newImagePath = $group['grp_img'];
        if (isset($file) && $file['error'] == 0) {
            $imageTmpPath = $file['tmp_name'];
            $imageName = $file['name'];
            if ($groupDir === null) {
                $cleanedGroupName = preg_replace('/[^a-zA-Z0-9_]/', '_', $group['grp_nom']);
                $groupDir = "../images/groupes/" . $cleanedGroupName;
            }
            if (!is_dir($groupDir)) {
                mkdir($groupDir, 0777, true);
            }
            $files = glob($groupDir . '/*');
            foreach ($files as $file) {
                if (is_file($file)) {
                    unlink($file);
                }
            }
            $newImagePath = $groupDir . "/" . uniqid() . "_" . $imageName;
            if (move_uploaded_file($imageTmpPath, $newImagePath)) {
                if ($group['grp_img'] != '../images/groupes/groupe.png' && file_exists($group['grp_img'])) {
                    unlink($group['grp_img']);
                }
            } else {
                $_SESSION['message'] = '<b><i style="color: red;">Erreur lors de l\'upload de l\'image.</i></b>';
                header("Location: ../vue/modifier_groupe.php?group_id=" . $group['grp_id']);
                exit();
            }
        }
        return $newImagePath;
    }

    public static function updateGroup($groupId, $newGroupName, $newGroupColor, $newAnnualLimit, $newImagePath) {
        $db = Connexion::pdo();
        $updateQuery = "UPDATE groupe SET 
                            grp_nom = :nom, 
                            grp_couleur = :couleur, 
                            grp_lim_an = :limite, 
                            grp_img = :image
                        WHERE grp_id = :grp_id";
        $stmt = $db->prepare($updateQuery);
        return $stmt->execute([
            'nom' => $newGroupName,
            'couleur' => $newGroupColor,
            'limite' => $newAnnualLimit,
            'image' => $newImagePath,
            'grp_id' => $groupId
        ]);
    }

    public static function groupNameExists($nomGroupe, $grpID) {
        try {
            $pdo = Connexion::pdo();
            $stmt = $pdo->prepare("SELECT count(*) FROM groupe WHERE grp_nom = :grp_nom AND grp_id != :grp_id");
            $stmt->execute(['grp_nom' => $nomGroupe, 'grp_id' => $grpID]);
            $resultNameExists = $stmt->fetchColumn();
            return $resultNameExists > 0;
        } catch (PDOException $e) {
            echo 'Erreur : ' . $e->getMessage();
            return false;
        }
    }
}
?>