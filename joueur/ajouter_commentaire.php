<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

require_once __DIR__ . '/../../back/Requetes/RequeteJoueur.php';

$response = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);

    if (isset($data['id_joueur'], $data['commentaire'])) {
        $id_joueur = (int)$data['id_joueur'];
        $commentaire = htmlspecialchars($data['commentaire']);

        try {
            ajouterOuMettreAJourCommentaire($id_joueur, $commentaire);
            $response = [
                "success" => true,
                "message" => "Commentaire ajouté avec succès"
            ];
        } catch (PDOException $e) {
            $response = [
                "success" => false,
                "message" => "Erreur : " . $e->getMessage()
            ];
        }
    } else {
        $response = [
            "success" => false,
            "message" => "Données incomplètes"
        ];
    }
} else {
    $response = [
        "success" => false,
        "message" => "Méthode non autorisée"
    ];
}

echo json_encode($response);
?>
