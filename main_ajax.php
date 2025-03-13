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
if (isset($_POST['donnee']) && isset($_POST['attribut'])) {

    echo json_encode(["message" => "rentre dans le if"]);

 
    $donnee = $_POST['donnee'];
    $attribut = $_POST['attribut'];
    $table_id = $_POST['table_id'];


    // Liste des colonnes autorisées par table
    $allowedColumns = [
        "affec-tab" => ["id_affectation", "date", "num_permis", "immatriculation", "num_marchandise"],
        "chauff-tab" => ["num_permis", "nom", "prenom"],
        "cam-tab" => ["immatriculation", "type", "poids_total"],
        "march-tab" => ["num_marchandise", "nom", "type_camion_requis", "poids", "date_transport", "ville_depart", "ville_arrivee"]
    ];

    // Vérification que l'attribut et la table sont valides
    if (!isset($allowedColumns[$table_id]) || !in_array($attribut, $allowedColumns[$table_id])) {
        die('Erreur : Attribut ou table invalide');
    }

    try {
        // Requête pour récupérer les affectations filtrées
        switch ($table_id) {
            case "affec-tab":
                $req = $db->prepare("SELECT id_affectation, date, num_permis, immatriculation, num_marchandise FROM Affectation WHERE $attribut = :donnee");
                break;
            case "chauff-tab":
                $req = $db->prepare("SELECT num_permis, nom, prenom FROM Chauffeur WHERE $attribut = :donnee");
                break;
            case "cam-tab":
                $req = $db->prepare("SELECT immatriculation, type, poids_total FROM Camion WHERE $attribut = :donnee");
                break;
            case "march-tab":
                $req = $db->prepare("SELECT num_marchandise, nom, type_camion_requis, poids, date_transport, ville_depart, ville_arrivee FROM Marchandise WHERE $attribut = :donnee");
                break;
            default:
                die('Erreur : Table invalide');
        }
        $req->bindParam(':donnee', $donnee, PDO::PARAM_STR);
        $req->execute();

        // Retourner uniquement les lignes filtrées
        $output = "";
while ($row = $req->fetch(PDO::FETCH_ASSOC)) { // Utilise PDO::FETCH_ASSOC pour éviter des erreurs d'index
    if ($table_id === "affec-tab") {
        $output .= '<tr>
                        <td>' . htmlspecialchars($row['id_affectation'] ?? '') . '</td>
                        <td>' . htmlspecialchars($row['date'] ?? '') . '</td>
                        <td>' . htmlspecialchars($row['num_permis'] ?? '') . '</td>
                        <td>' . htmlspecialchars($row['immatriculation'] ?? '') . '</td>
                        <td>' . htmlspecialchars($row['num_marchandise'] ?? '') . '</td>
                    </tr>';
    } elseif ($table_id === "chauff-tab") {
        $output .= '<tr>
                        <td>' . htmlspecialchars($row['num_permis'] ?? '') . '</td>
                        <td>' . htmlspecialchars($row['nom'] ?? '') . '</td>
                        <td>' . htmlspecialchars($row['prenom'] ?? '') . '</td>
                    </tr>';
    } elseif ($table_id === "cam-tab") {
        $output .= '<tr>
                        <td>' . htmlspecialchars($row['immatriculation'] ?? '') . '</td>
                        <td>' . htmlspecialchars($row['type'] ?? '') . '</td>
                        <td>' . htmlspecialchars($row['poids_total'] ?? '') . '</td>
                    </tr>';
    } elseif ($table_id === "march-tab") {
        $output .= '<tr>
                        <td>' . htmlspecialchars($row['num_marchandise'] ?? '') . '</td>
                        <td>' . htmlspecialchars($row['nom'] ?? '') . '</td>
                        <td>' . htmlspecialchars($row['type_camion_requis'] ?? '') . '</td>
                        <td>' . htmlspecialchars($row['poids'] ?? '') . ' kg</td>
                        <td>' . htmlspecialchars($row['date_transport'] ?? '') . '</td>
                        <td>' . htmlspecialchars($row['ville_depart'] ?? '') . '</td>
                        <td>' . htmlspecialchars($row['ville_arrivee'] ?? '') . '</td>
                    </tr>';
    }
}

// Si aucun résultat n'est trouvé, afficher un message
if ($output === "") {
    $output = '<tr><td colspan="7" class="text-center">Aucune donnée trouvée</td></tr>';
}

echo $output;
exit;

    } catch (Exception $e) {
        die('Erreur : ' . $e->getMessage());
    }

    exit; // Arrêter l'exécution du script après avoir renvoyé la réponse
}
else {
    var_dump($_POST);
}

} catch (Exception $e) {
    die('Erreur : ' . $e->getMessage());
}
?>
