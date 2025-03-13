<?php
session_start();

// Connexion à la base de données
try {
    $db = new PDO('mysql:host=localhost;dbname=logistique;charset=utf8', 'TP', '', [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
} catch (Exception $e) {
    die('Erreur : ' . $e->getMessage());
}
try {
// Vérification si c'est une requête Ajax pour filtrer les données
if (isset($_POST['question_id'])) {

    echo json_encode(["message" => "rentre dans le if"]);

    $question_id = $_POST['question_id'];

 
    $type = isset($_POST['type_de_camion']) ? $_POST['type_de_camion'] : null;
    $date = isset($_POST['date']) ? $_POST['date'] : null;
    $nom = isset($_POST['nom']) ? $_POST['nom'] : null;
    $prenom = isset($_POST['prenom']) ? $_POST['prenom'] : null;
    $num_permis = isset($_POST['num_permis']) ? $_POST['num_permis'] : null;
    $poids_total = isset($_POST['poids_total']) ? $_POST['poids_total'] : null;
    $immatriculation = isset($_POST['immatriculation']) ? $_POST['immatriculation'] : null;
    $ville_d_arrivee = isset($_POST['ville_d_arrivee']) ? $_POST['ville_d_arrivee'] : null;
    



    // Liste des colonnes autorisées par table
    $allowedColumns = [
        "question1" => ["id_affectation", "date", "num_permis", "immatriculation", "num_marchandise"],
        "question2" => ["num_permis", "nom", "prenom"],
        "question3" => ["immatriculation", "type", "poids_total"],
        "question4" => ["num_marchandise", "nom", "type_camion_requis", "poids", "date_transport", "ville_depart", "ville_arrivee"]
    ];



    try {
        // Requête pour récupérer les affectations filtrées
        $question_id = isset($_POST['question_id']) ? $_POST['question_id'] : '';

        if (empty($question_id)) {
            echo json_encode(['success' => false, 'message' => 'Question ID manquant']);
            exit;
        }

        switch ($question_id) {
            case "question1":
                $req = $db->prepare("SELECT 
                                        C.immatriculation, 
                                        C.type, 
                                        M.nom AS marchandise, 
                                        M.ville_arrivee
                                    FROM Affectation A
                                    JOIN Camion C ON A.immatriculation = C.immatriculation
                                    JOIN Marchandise M ON A.num_marchandise = M.num_marchandise
                                    WHERE C.type = :type 
                                    AND M.ville_arrivee = :ville_d_arrivee;
                                    ");
                if (isset($type) && isset($ville_d_arrivee)) {
                    $req->bindParam(':type', $type, PDO::PARAM_STR);
                    $req->bindParam(':ville_d_arrivee', $ville_d_arrivee, PDO::PARAM_STR);
                } else {
                    die('Erreur : les paramètres sont manquants.');
                }
                break;

            case "question2":
                $req = $db->prepare("INSERT INTO Camion (immatriculation, type, poids_total) 
                                    VALUES (:immatriculation, :type, :poids_total)");
                if (isset($immatriculation) && isset($type) && isset($poids_total)) {
                    $req->bindParam(':type', $type, PDO::PARAM_STR);
                    $req->bindParam(':immatriculation', $immatriculation, PDO::PARAM_STR);
                    $req->bindParam(':poids_total', $poids_total, PDO::PARAM_STR);
                } else {
                    die('Erreur : les paramètres sont manquants.');
                }
                break;

            case "question31":
                $req = $db->prepare("UPDATE Chauffeur 
                                    SET nom = :nom, 
                                        prenom = :prenom
                                    WHERE num_permis = :num_permis");
                $req->bindParam(':nom', $nom, PDO::PARAM_STR);
                $req->bindParam(':prenom', $prenom, PDO::PARAM_STR);
                $req->bindParam(':num_permis', $num_permis, PDO::PARAM_STR);
                break;

            case "question32":
                $req = $db->prepare("DELETE FROM Chauffeur WHERE num_permis = :num_permis");
                $req->bindParam(':num_permis', $num_permis, PDO::PARAM_STR);
                break;

            case "question4":
                $req = $db->prepare("SELECT 
                                    C.immatriculation, 
                                    C.type, 
                                    L.date, 
                                    L.ville_matin, 
                                    L.ville_soir
                                FROM Localisation L
                                JOIN Camion C ON L.immatriculation = C.immatriculation
                                WHERE C.type = :type 
                                AND L.date = :date;
                                ");
                    $req->bindParam(':type', $type, PDO::PARAM_STR);
                    $req->bindParam(':date', $date, PDO::PARAM_STR);
                break;

            default:
                die('Erreur : question_id invalide');
        }
        var_dump($_POST);  // Vérifie les données envoyées via POST

        $req->execute();

        // Retourner uniquement les lignes filtrées
        $output = "";
            
        $results = $req->fetchAll(PDO::FETCH_ASSOC);
        if ($results) {
            $output = "";
            foreach ($results as $row) {
                $output .= '<tr>';
                foreach ($row as $column => $value) {
                    $output .= "<td>" . htmlspecialchars($value) . "</td>";
                }
                $output .= '</tr>';
            }
            var_dump($_POST);  // Vérifie les données envoyées via POST

        } else {
            $output = '<tr><td colspan="7" class="text-center">Aucune donnée trouvée</td></tr>';
        }



var_dump($output); // Debug, vérifier ce qui est renvoyé
echo json_encode(['success' => true, 'html' => $output]);
exit;

    } 

catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Erreur: ' . $e->getMessage()]);
    }

    exit; // Arrêter l'exécution du script après avoir renvoyé la réponse
}
else {
    var_dump($_POST);
}
}
catch (Exception $e) {
    die('Erreur : ' . $e->getMessage());
}
?>
