<?php
header("Content-Type: application/json");

require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../../back/middleware/authMiddleware.php';
require_once __DIR__ . '/../../back/Requetes/connexion.php';
require_once __DIR__ . '/../../back/Requetes/RequeteJoueur.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;


verifyJWT(); // 🔐 sécurité token
// Vérifier le type de méthode HTTP
switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        if (isset($_GET['id'])) {
            // Récupérer un joueur spécifique
            $id = $_GET['id'];
            $joueur = getJoueurById($id);
            echo json_encode($joueur);
        } else {
            // Récupérer tous les joueurs
            $joueurs = getTousLesJoueurs();
            echo json_encode($joueurs);
        }
        break;

    case 'POST':
        // Supprimer un joueur via POST
        $data = json_decode(file_get_contents("php://input"), true); // Récupérer les données du body JSON
        if (isset($data['id'])) {
            $id_joueur = $data['id'];  // Récupérer l'ID du joueur depuis le body de la requête
            try {
                // Appeler la fonction pour supprimer le joueur et ses relations associées
                supprimerJoueurs($id_joueur);
                echo json_encode(["message" => "Joueur supprimé avec succès"]);
            } catch (PDOException $e) {
                echo json_encode(["message" => "Erreur lors de la suppression : " . $e->getMessage()]);
            }
        } else {
            echo json_encode(["message" => "ID du joueur manquant pour suppression"]);
        }
        break;

    case 'DELETE':
        // ✅ Lire le corps de la requête DELETE en JSON
        $json = file_get_contents("php://input");
        $data = json_decode($json, true);
    
        if (isset($data['id'])) {
            $id_joueur = $data['id'];
    
            try {
                supprimerJoueurs($id_joueur);
                echo json_encode([
                    "success" => true,
                    "message" => "Joueur supprimé avec succès"
                ]);
            } catch (PDOException $e) {
                echo json_encode([
                    "success" => false,
                    "message" => "Erreur suppression : " . $e->getMessage()
                ]);
            }
        } else {
            echo json_encode([
                "success" => false,
                "message" => "ID manquant pour suppression"
            ]);
        }
        break;
        

    default:
        echo json_encode(["message" => "Méthode HTTP non supportée"]);
        break;
}
?>
