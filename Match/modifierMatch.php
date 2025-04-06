<?php
header("Content-Type: application/json");

require_once __DIR__ . '/../../back/vendor/autoload.php';
require_once __DIR__ . '/../../back/middleware/authMiddleware.php';
require_once __DIR__ . '/../../back/Requetes/connexion.php';
require_once __DIR__ . '/../../back/Requetes/RequeteMatch.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

verifyJWT(); // 🔐 sécurité token

$method = $_SERVER['REQUEST_METHOD'];
$match = new RequeteMatch();

if ($method === 'PUT') {
    $data = json_decode(file_get_contents("php://input"), true);

    if (
        isset($data['id']) &&
        isset($data['dateMatch']) &&
        isset($data['time']) &&
        isset($data['placeMeeting']) &&
        isset($data['opponent']) &&
        isset($data['result'])
    ) {
        $match->modifierMatch(
            $linkpdo,
            $data['id'],
            $data['dateMatch'],
            $data['time'],
            $data['opponent'],
            $data['placeMeeting'],
            $data['result']
        );

        echo json_encode(["message" => "Match modifié avec succès"]);
    } else {
        http_response_code(400);
        echo json_encode(["message" => "Données incomplètes"]);
    }
} else {
    http_response_code(405);
    echo json_encode(["message" => "Méthode non autorisée"]);
}
