<?php
header("Content-Type: application/json");

require_once __DIR__ . '/../../back/vendor/autoload.php';
require_once __DIR__ . '/../../back/middleware/authMiddleware.php';
require_once __DIR__ . '/../../back/db.php';
require_once __DIR__ . '/../../back/Requetes/RequeteMatch.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

$linkpdo = connectDB();

$method = $_SERVER['REQUEST_METHOD'];
$match = new RequeteMatch();
verifyJWT(); // 🔐 sécurité token

switch ($method) {
    case 'GET':
        if (isset($_GET['id'])) {
            $matchUnique = $match->getMatch($_GET['id'], $linkpdo)->fetch(PDO::FETCH_ASSOC);
            echo json_encode($matchUnique);
            exit;
        }
    
        if (isset($_GET['recherche'])) {
            $result = $match->rechercherMatch($linkpdo, $_GET['recherche']);
        } else {
            $result = $match->getMatchs($linkpdo);
        }
    
        $data = [];
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $data[] = $row;
        }
    
        echo json_encode($data);
        break;
    

    case 'DELETE':
    $data = json_decode(file_get_contents("php://input"), true);

    if (!isset($data['id'])) {
        http_response_code(400);
        echo json_encode(["message" => "ID manquant"]);
        break;
    }

    $id = $data['id'];
    
    // Vérifie si le match existe avant suppression
    $checkMatch = $match->getMatch($id, $linkpdo)->fetch();

    if (!$checkMatch) {
        http_response_code(404);
        echo json_encode(["message" => "Aucun match trouvé avec l'identifiant donné"]);
        break;
    }

    // Suppression
    $match->supprimerMatch($linkpdo, $id);
    http_response_code(200);
    echo json_encode(["message" => "Match supprimé avec succès"]);
    break;

        

    default:
        http_response_code(405);
        echo json_encode(["message" => "Méthode non autorisée"]);
        break;
}
