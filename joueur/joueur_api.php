<?php
header("Content-Type: application/json");

 ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../../back/middleware/authMiddleware.php';
require_once __DIR__ . '/../../back/Requetes/connexion.php';
require_once __DIR__ . '/../../back/Requetes/RequeteJoueur.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

verifyJWT(); // 🔐 sécurité token

// Définir le type de requête (GET, POST, PUT, DELETE)
switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        if (isset($_GET['id'])) {
            // Récupérer un joueur spécifique
            $id = $_GET['id'];
            $joueur = getJoueurById($id);
            echo json_encode($joueur);
        } else {
            // Récupérer tous les joueurs
            $joueurs = getTousLesJoueursAvecCommentaires();

            // Assurer que la réponse est un tableau JSON
            echo json_encode($joueurs);
        }
        break;

    case 'POST':
        // Ajouter un joueur
        $data = json_decode(file_get_contents("php://input"), true);
        
        // Vérifier que toutes les données nécessaires sont présentes
        if (isset($data['nom'], $data['prenom'], $data['numero_licence'], $data['date_naissance'], $data['taille'], $data['poids'], $data['statut'])) {
            try {
                // Appeler la fonction pour ajouter le joueur dans la base de données
                $joueur = ajouterJoueur(
                    $data['nom'],
                    $data['prenom'],
                    $data['numero_licence'],
                    $data['date_naissance'],
                    $data['taille'],
                    $data['poids'],
                    $data['statut']
                );
                
                // Réponse JSON avec succès
                echo json_encode(["success" => true, "message" => "Joueur ajouté avec succès", "joueur" => $joueur]);
            } catch (PDOException $e) {
                // Réponse JSON en cas d'erreur avec la base de données
                echo json_encode(["success" => false, "message" => "Erreur : " . $e->getMessage()]);
            }
        } else {
            // Réponse JSON si les données sont manquantes
            echo json_encode(["success" => false, "message" => "Données manquantes pour l'ajout"]);
        }
        break;

    case 'PUT':
        // Modifier un joueur
        $data = json_decode(file_get_contents("php://input"), true);
        if (isset($data['id'], $data['nom'], $data['prenom'], $data['numero_licence'], $data['date_naissance'], $data['taille'], $data['poids'], $data['statut'])) {
            mettreAJourJoueur(
                $data['id'],
                $data['nom'],
                $data['prenom'],
                $data['numero_licence'],
                $data['date_naissance'],
                $data['taille'],
                $data['poids'],
                $data['statut']
            );
            echo json_encode(["message" => "Joueur mis à jour avec succès"]);
        } else {
            echo json_encode(["message" => "Données manquantes pour la mise à jour"]);
        }
        break;

    case 'DELETE':
        // Supprimer un joueur
        if (isset($_GET['id'])) {
            $id = $_GET['id'];
            supprimerJoueur($id);
            echo json_encode(["message" => "Joueur supprimé avec succès"]);
        } else {
            echo json_encode(["message" => "ID du joueur manquant pour suppression"]);
        }
        break;

    default:
        echo json_encode(["message" => "Méthode HTTP non supportée"]);
        break;
}
?>
