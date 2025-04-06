<?php
header("Content-Type: application/json");

require_once __DIR__ . '/../../back/vendor/autoload.php';
require_once __DIR__ . '/../../back/middleware/authMiddleware.php';
require_once __DIR__ . '/../../back/Requetes/connexion.php';
require_once __DIR__ . '/../../back/Requetes/RequeteMatch.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

// 🔐 Vérification du token
verifyJWT();

// 📥 Récupération des données envoyées en JSON
$data = json_decode(file_get_contents("php://input"), true);

if (
    isset($data['dateMatch']) &&
    isset($data['time']) &&
    isset($data['placeMeeting']) &&
    isset($data['opponent'])
) {
    $dateMatch = $data['dateMatch'];
    $time = $data['time'];
    $placeMeeting = $data['placeMeeting'];
    $opponent = $data['opponent'];

    $match = new RequeteMatch();
    $matchExist = $match->existMatch($linkpdo, $dateMatch, $time, $opponent, $placeMeeting);

    if (!$matchExist) {
        $match->ajouterMatch($linkpdo, $dateMatch, $time, $opponent, $placeMeeting);
        http_response_code(201);
        echo json_encode(["message" => "Match ajouté avec succès"]);
    } else {
        http_response_code(409); // Conflit
        echo json_encode(["message" => "Le match existe déjà"]);
    }

} else {
    http_response_code(400);
    echo json_encode(["message" => "Données manquantes"]);
}
