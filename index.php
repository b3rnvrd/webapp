<?php
session_start();

try {
    // Connexion à la base de données
    $db = new PDO('mysql:host=localhost;dbname=logistique;charset=utf8', 'TP', '', [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
} catch (Exception $e) {
    die('Erreur : ' . $e->getMessage());
}

// Vérification du formulaire soumis
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = isset($_POST['username']) ? trim($_POST['username']) : '';
    $password = isset($_POST['password']) ? trim($_POST['password']) : '';

    if (!empty($username) && !empty($password)) {
        $stmt = $db->prepare("SELECT password FROM login WHERE username = :username");
        $stmt->bindParam(':username', $username, PDO::PARAM_STR);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Comparaison en clair du mot de passe
        if ($user && $password == $user['password']) {
            $_SESSION['user'] = $username;
            header('Location: index.php'); // Recharge la page pour afficher la gestion de flotte
            exit;
        } else {
            $error = "Identifiants incorrects.";
        }
    } else {
        $error = "Veuillez remplir tous les champs.";
    }
}

// Vérification si l'utilisateur est connecté
$estConnecte = isset($_SESSION['user']);
?>

    <!-- Liens -->

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion Flotte</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="icon" type="image/x-icon" href="front/images/camion.ico">
    <link rel="stylesheet" href="front/css/styles.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>

    <!-- Connexion échouée -->
<?php if (!$estConnecte) : ?>
    <div class="container mt-5">
        <h2 class="text-center">Connexion</h2>
        <form method="POST" id="login-form">
            <div class="mb-3">
                <label for="username" class="form-label">Nom d'utilisateur</label>
                <input type="text" class="form-control" id="username" name="username" autocomplete="username" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Mot de passe</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <?php if (!empty($error)) { echo "<div class='alert alert-danger'>$error</div>"; } ?>
            <button type="submit" class="btn btn-primary w-100">Se connecter</button>
        </form>
    </div>

        <!-- Connexion réussie -->

<?php else : ?>

    <!-- Bandeau de connexion -->
    <section id="title">
        <div class="container-fluid">
            <div class="row bg-image">
                <div class="container mt-3">
                    <div class="row">
                        <div class="col-lg-4">
                            <h2 class="text-center">Bienvenue, <?= htmlspecialchars($_SESSION['user']) ?> !</h2>
                        </div>
                        <div class="col-lg-6">
                        </div>
                        <div class="col-lg-2">
                            <a href="logout.php" class="btn btn-danger">Déconnexion</a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                </div>
                <div class="col-lg-6">
                    <h1>Gestion de la flotte</h1>
                </div>
                </div>
            </div>
        </div>
    </section>


    <div class="container">
        <!-- Row pour les Chauffeurs -->
        <div class="row g-4">
            <div class="col-lg-12 col-sm-12 pricing-column">
                <div class="card">
                    <div class="card-header">
                        <h3>Les Chauffeurs</h3>
                    </div>
                    <div class="card-body">
                        <!-- Première row : image -->
                        <div class="row g-0 img-fluid">
                            <div class="col-12 text-center">
                                <img src="front/images/chauffeur.jpg" class="img-fluid img-chauffeurs" alt="Chauffeurs">
                            </div>
                        </div>
                        <!-- Deuxième row : tableau des chauffeurs -->
                        <div class="row mt-3">
                            <div class="col-12">
                                <table class="table table-bordered attributs" id="chauff-tab">
                                    <thead class="table-light">
                                        <tr>
                                            <th>N° de permis</th>
                                            <th>Nom</th>
                                            <th>Prénom</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        try {
                                            // Requête pour récupérer les chauffeurs
                                            $req = $db->prepare("SELECT num_permis, nom, prenom FROM Chauffeur");
                                            $req->execute();
                                        } catch (Exception $e) {
                                            die('Erreur :' . $e->getMessage());
                                        }
                                        // Affichage des résultats dans le tableau
                                        while ($chauffeur = $req->fetch()) {
                                        ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($chauffeur['num_permis']); ?></td>
                                                <td><?php echo htmlspecialchars($chauffeur['nom']); ?></td>
                                                <td><?php echo htmlspecialchars($chauffeur['prenom']); ?></td>
                                            </tr>
                                        <?php
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <form method="POST" data-table="chauff-tab">
                            <div class="row mt-3">
                                <div class="col-4 text-center">
                                    <select class="form-select" name="attribut" aria-label="Default select example">
                                        <option selected value="num_permis">N° de permis</option>
                                        <option value="nom">Nom</option>
                                        <option value="prenom">Prénom</option>
                                    </select>
                                </div>
                                <div class="col-5 text-center">
                                    <input class="form-control" type="text" name="donnee" placeholder="Entrez votre donnée ici. Attention, le texte est sensible à la casse" aria-label=".form-control-sm example">
                                </div>
                                <div class="col-3 text-center">
                                        <button class="btn btn-primary mx-auto w-100">Trier</button>
                                </div>
                            </div>
                        </form>                                        
                    </div>
                </div>
            </div>
        </div>

        <!-- Row pour les Affectations -->
        <div class="row g-4">
            <div class="col-lg-12 col-sm-12 pricing-column">
                <div class="card">
                    <div class="card-header">
                        <h3>Les Affectations</h3>
                    </div>
                    <div class="card-body">
                        <!-- Première row : image -->
                        <div class="row g-0 img-fluid">
                            <div class="col-12 text-center">
                                <img src="front/images/affectation.jpg" class="img-fluid img-affectations" alt="Affectations">
                            </div>
                        </div>
                        <!-- Deuxième row : tableau des affectations -->
                        <div class="row mt-3">
                            <div class="col-12">
                                <table class="table table-bordered attributs" id="affec-tab">
                                    <thead class="table-light">
                                        <tr>
                                            <th>N° d'affectation</th>
                                            <th>Date</th>
                                            <th>N° de permis</th>
                                            <th>Immatriculation</th>
                                            <th>N° de marchandise</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                        try {
                                            // Requête pour récupérer les chauffeurs
                                            $req = $db->prepare("SELECT id_affectation, date, num_permis, immatriculation, num_marchandise FROM Affectation");
                                            $req->execute();
                                        } catch (Exception $e) {
                                            die('Erreur :' . $e->getMessage());
                                        }
                                        // Affichage des résultats dans le tableau
                                        while ($affectation = $req->fetch()) {
                                        ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($affectation['id_affectation']); ?></td>
                                                <td><?php echo htmlspecialchars($affectation['date']); ?></td>
                                                <td><?php echo htmlspecialchars($affectation['num_permis']); ?></td>
                                                <td><?php echo htmlspecialchars($affectation['immatriculation']); ?></td>
                                                <td><?php echo htmlspecialchars($affectation['num_marchandise']); ?></td>
                                            </tr>
                    <?php
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <form method="POST" data-table="affec-tab">
                        <div class="row mt-3">
                            <div class="col-4 text-center">
                                <select class="form-select" name="attribut" aria-label="Default select example">
                                    <option selected value="id_affectation">N° d'affectation</option>
                                    <option value="date">Date</option>
                                    <option value="num_permis">N° de permis</option>
                                    <option value="immatriculation">Immatriculation</option>
                                    <option value="num_marchandise">N° de marchandise</option>
                                </select>
                            </div>
                            <div class="col-5 text-center">
                                <input class="form-control" type="text" name="donnee" placeholder="Entrez votre donnée ici. Attention, le texte est sensible à la casse" aria-label=".form-control-sm example">
                            </div>
                            <div class="col-3 text-center">
                                    <button class="btn btn-primary mx-auto w-100">Trier</button>
                            </div>
                        </div>
                        </form>                                        

                    </div>
                </div>
            </div>
        </div>

        <!-- Row pour les Camions -->
        <div class="row g-4">
            <div class="col-lg-12 col-sm-12 pricing-column">
                <div class="card">
                    <div class="card-header">
                        <h3>Les Camions</h3>
                    </div>
                    <div class="card-body">
                        <!-- Première row : image -->
                        <div class="row g-0 img-fluid">
                            <div class="col-12 text-center">
                                <img src="front/images/camion.jpeg" class="img-fluid img-camions" alt="Camions">
                            </div>
                        </div>
                        <!-- Deuxième row : tableau des camions -->
                        <div class="row mt-3">
                            <div class="col-12">
                                <table class="table table-bordered attributs" id="cam-tab">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Immatriculation</th>
                                            <th>Type</th>
                                            <th>Poids total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        try {
                                            // Requête pour récupérer les camions
                                            $req = $db->prepare("SELECT immatriculation, type, poids_total FROM Camion");
                                            $req->execute();
                                        } catch (Exception $e) {
                                            die('Erreur :' . $e->getMessage());
                                        }
                                        // Affichage des résultats dans le tableau
                                        while ($camion = $req->fetch()) {
                                        ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($camion['immatriculation']); ?></td>
                                                <td><?php echo htmlspecialchars($camion['type']); ?></td>
                                                <td><?php echo htmlspecialchars($camion['poids_total']); ?> kg</td>
                                            </tr>
                                        <?php
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                                <form method="POST" data-table="cam-tab">
                        <div class="row mt-3">
                            <div class="col-4 text-center">
                                <select class="form-select" name="attribut" aria-label="Default select example">
                                    <option selected value="immatriculation">Immatriculation</option>
                                    <option value="type">Type de camion</option>
                                    <option value="poids_total">Poids total</option>
                                </select>
                            </div>
                            <div class="col-5 text-center">
                                <input class="form-control" type="text" name="donnee" placeholder="Entrez votre donnée ici. Attention, le texte est sensible à la casse" aria-label=".form-control-sm example">
                            </div>
                            <div class="col-3 text-center">
                                    <button class="btn btn-primary mx-auto w-100">Trier</button>
                                     
                            </div>
                        </div>
                                                    </form>   
                                                </div>
                </div>
            </div>
        </div>

        <!-- Row pour les Marchandises -->
        <div class="row g-4">
            <div class="col-lg-12 col-sm-12 pricing-column">
                <div class="card">
                    <div class="card-header">
                        <h3>Les Marchandises</h3>
                    </div>
                    <div class="card-body">
                        <!-- Première row : image -->
                        <div class="row g-4 img-fluid">
                            <div class="col-12 text-center">
                                <img src="front/images/marchandises.jpg" class="img-fluid img-marchandises" alt="Marchandises">
                            </div>
                        </div>

                        <!-- Deuxième row : tableau des marchandises -->
                        <div class="row mt-3">
                            <div class="col-12">
                                <table class="table table-bordered attributs" id="march-tab">
                                    <thead class="table-light">
                                        <tr>
                                            <th>N° de marchandise</th>
                                            <th>Nom</th>
                                            <th>Type de camion</th>
                                            <th>Poids (kg)</th>
                                            <th>Date de transport</th>
                                            <th>Ville de départ</th>
                                            <th>Ville d'arrivée</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        try {
                                            // Requête pour récupérer les marchandises
                                            $req = $db->prepare("SELECT num_marchandise, nom, type_camion_requis, poids, date_transport, ville_depart, ville_arrivee FROM Marchandise");
                                            $req->execute();
                                        } catch (Exception $e) {
                                            die('Erreur :' . $e->getMessage());
                                        }
                                        // Affichage des résultats dans le tableau
                                        while ($marchandise = $req->fetch()) {
                                        ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($marchandise['num_marchandise']); ?></td>
                                                <td><?php echo htmlspecialchars($marchandise['nom']); ?></td>
                                                <td><?php echo htmlspecialchars($marchandise['type_camion_requis']); ?></td>
                                                <td><?php echo htmlspecialchars($marchandise['poids']); ?> kg</td>
                                                <td><?php echo htmlspecialchars($marchandise['date_transport']); ?></td>
                                                <td><?php echo htmlspecialchars($marchandise['ville_depart']); ?></td>
                                                <td><?php echo htmlspecialchars($marchandise['ville_arrivee']); ?></td>
                                            </tr>
                                        <?php
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <form method="POST" data-table="march-tab">

                        <div class="row mt-3">
                            <div class="col-4 text-center">
                                <select class="form-select" name="attribut" aria-label="Default select example">
                                    <option selected value="num_marchandise">N° de marchandise</option>
                                    <option value="nom">Nom</option>
                                    <option value="type_camion_requis">Type de camion</option>
                                    <option value="poids">Poids</option>
                                    <option value="date_transport">Date de transport</option>
                                    <option value="ville_depart">Ville de départ</option>
                                    <option value="ville_arrivee">Ville d'arrivée</option>
                                </select>
                            </div>
                            <div class="col-5 text-center">
                                <input class="form-control" type="text" name="donnee" placeholder="Entrez votre donnée ici. Attention, le texte est sensible à la casse" aria-label=".form-control-sm example">
                            </div>
                            <div class="col-3 text-center">
                                    <button class="btn btn-primary mx-auto w-100">Trier</button>
                            </div>
                        </div>
                        </form>                                         
                    </div>
                </div>
            </div>
        </div>

        <!-- Row pour les questions du TP -->

        <div class="row g-4">
            <div class="col-lg-12 col-sm-12 pricing-column">
                <div class="card">
                    <div class="card-header">
                        <h3>Questions du TP</h3>
                    </div>
                    <div class="card-body">
                        <!-- Première row : image -->
                        <div class="row g-4 img-fluid">
                            <div class="col-12 text-center">
                                <img src="front/images/questions.png" class="img-fluid img-questions" alt="Questions">
                            </div>
                        </div>

                        <form method="POST" question-form="question1" id="Q1">
                            <div class="row mt-3">
                            <div class="col-4 text-center">
                                <select class="form-select" name="type_de_camion" aria-label="Default select example">
                                        <option selected value="frigo">Frigo</option>
                                        <option value="palette">Palette</option>
                                        <option value="citerne">Citerne</option>
                                        <option value="plateau">Plateau</option>
                                    </select>                                   
                            </div>
                                <div class="col-4 text-center">
                                    <input class="form-control" type="text" name="ville_d_arrivee" placeholder="Ville d'arrivée" aria-label=".form-control-sm example">
                                </div>
                                <div class="col-4 text-center">
                                        <button class="btn btn-primary mx-auto w-100">Rechercher</button>
                                </div>
                            </div>
                        </form>

                        <form method="POST" question-form="question2" id="Q2">
                            <div class="row mt-3">
                            <div class="col-3 text-center">
                                    <input class="form-control" type="text" name="immatriculation" placeholder="Immatriculation" aria-label=".form-control-sm example">
                                </div>
                                <div class="col-3 text-center">
                                    <select class="form-select" name="type_de_camion" aria-label="Default select example">
                                        <option selected value="frigo">Frigo</option>
                                        <option value="palette">Palette</option>
                                        <option value="citerne">Citerne</option>
                                        <option value="plateau">Plateau</option>
                                    </select>                                </div>
                                <div class="col-3 text-center">
                                    <input class="form-control" type="text" name="poids_total" placeholder="Poids total" aria-label=".form-control-sm example">
                                </div>
                                <div class="col-3 text-center">
                                        <button class="btn btn-primary mx-auto w-100">Ajouter</button>
                                </div>
                            </div>
                        </form>   

                        <form method="POST"  id="Q3">
                            <div class="row mt-3">
                            <div class="col-2 text-center">
                                    <input class="form-control" type="text" name="num_permis" placeholder="N° de permis" aria-label=".form-control-sm example">
                                </div>
                                <div class="col-3 text-center">
                                    <input class="form-control" type="text" name="nom" placeholder="Nom" aria-label=".form-control-sm example">
                                </div>
                                <div class="col-3 text-center">
                                    <input class="form-control" type="text" name="prenom" placeholder="Prénom" aria-label=".form-control-sm example">
                                </div>
                                <div class="col-2 text-center">
                                        <button question-form="question31" class="btn btn-primary mx-auto w-100">Modifier</button>
                                </div>
                                <div class="col-2 text-center">
                                        <button question-form="question32" class="btn btn-primary mx-auto w-100">Supprimer</button>
                                </div>
                            </div>
                        </form>
                        
                        <form method="POST" question-form="question4" id="Q4">
                            <div class="row mt-3">
                            <div class="col-4 text-center">
                                <select class="form-select" name="type_de_camion" aria-label="Default select example">
                                    <option selected value="frigo">Frigo</option>
                                    <option value="palette">Palette</option>
                                    <option value="citerne">Citerne</option>
                                    <option value="plateau">Plateau</option>
                                </select>
                            </div>
                                <div class="col-4 text-center">
                                    <input class="form-control" type="text" name="date" placeholder="Date (jj-mm-aa)" aria-label=".form-control-sm example">
                                </div>
                                <div class="col-4 text-center">
                                        <button class="btn btn-primary mx-auto w-100">Localiser</button>
                                </div>
                            </div>
                        </form>   
                        <table class="table table-bordered attributs" id="question1-tab">
                                    <thead class="table-light" id="question1-head">
                                    </thead>
                                    <tbody id="question1-body">
                                    </tbody>
                        </table>
                        <table class="table table-bordered attributs" id="question2-tab">
                                    <thead class="table-light" id="question2-head">
                                    </thead>
                                    <tbody id="question2-body">
                                    </tbody>
                        </table>  
                        <table class="table table-bordered attributs" id="question3-tab">
                                    <thead class="table-light" id="question3-head">
                                    </thead>
                                    <tbody id="question3-body">
                                    </tbody>
                        </table>  
                        <table class="table table-bordered attributs" id="question3-tab">
                                    <thead class="table-light" id="question1-head">
                                    </thead>
                                    <tbody id="question3-body">
                                    </tbody>
                        </table>  
                    </div>
                </div>
            </div>
        </div>

    </div> 
    <?php endif; ?>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js" crossorigin="anonymous"></script>
    <script src="back/main.js" crossorigin="anonymous"></script>
    <script src="back/questions.js" crossorigin="anonymous"></script>



</body>
</html>