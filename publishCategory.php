<?php 
session_start(); 

if (!isset($_SESSION['user_id'])) {
    header('Location: /login.php');
    exit(); 
}

$host = 'localhost'; 
$dbname = 'criticlick'; 
$username = 'root'; 
$password = '';  

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Requête pour récupérer les catégories
    $stmt = $pdo->query("SELECT nom, filtreTag FROM categorie");
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Erreur de connexion à la base de données : " . $e->getMessage();
    $categories = [];
}


function insertData($pdo, $table, $data) {
    $columns = implode(", ", array_keys($data));
    $placeholders = ":" . implode(", :", array_keys($data));

    $sql = "INSERT INTO $table ($columns) VALUES ($placeholders)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($data);
}

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <link rel="stylesheet" href="css/publishCategory.css">
    <link rel="stylesheet" href="css/publishProduct.css">
    <link rel="stylesheet" href="css/navbar.css">
    <link rel="stylesheet" href="css/footer.css">
    
    <link href="https://fonts.googleapis.com/css2?family=League+Spartan:wght@100..900&display=swap" rel="stylesheet">
    <meta name="viewport" content="width=device-width,initial-scale=1.0,minimum-scale=1.0,maximum-scale=1.0">
    <title>CritiClick - Fil rouge</title>
</head>
<body>

<?php include 'components/navbar.php'; ?>

<main>
<section class="submit">

    <h1>Soumettre une category</h1>

    <?php 

    $nom = '';
    $description = '';
    $tag = '';

    $nomErr = '*';
    $descErr = '*';
    $tagErr = '* Utilisez UN mot simple et concis qui décris la catégorie';
    

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Validation du nom
        if (empty($_POST["nom"])) {
            $nomErr = "* Le nom est requis";
        } else if (strlen($_POST["nom"]) <= 4) {
            $nomErr = "* Le nom est trop court";
        } else {
            $nom = htmlspecialchars($_POST["nom"]);
        
            // Vérifier si le nom existe déjà dans la base de données (si nécessaire)
            $stmt = $pdo->prepare("SELECT * FROM categorie WHERE nom = :nom");
            $stmt->execute(['nom' => $nom]);
            $nomExists = $stmt->fetchColumn();
        
            if ($nomExists) {
                $nomErr = "* Ce nom existe déjà, veuillez en choisir un autre";
            } else {
                $nomErr = "*"; // Pas d'erreur
            }
        }

        if (empty($_POST["tag"])) {
            $tagErr = "* Le tag est requis";
        } else if (strpos($_POST["tag"], ' ') !== false) {
            $tagErr = "* Le tag doit être un seul mot sans espaces";
        } else {
            $tag = htmlspecialchars($_POST["tag"]);
        
            // Vérifier si le tag existe déjà dans la base de données
            $stmt = $pdo->prepare("SELECT * FROM categorie WHERE filtreTag = :tag");
            $stmt->execute(['tag' => $tag]);
            $tagExists = $stmt->fetchColumn();
            
            if ($tagExists) {
                $tagErr = "* Ce tag existe déjà, veuillez en choisir un autre";
            } else {
                $tagErr = "*";
            }

        }

        // Validation de la description
        if (empty($_POST["description"])) {
            $descErr = "* La description est requise";
        } else if (strlen($_POST["description"]) <= 50) {
            $descErr = "* La description est trop courte";
        } else {
            $description = htmlspecialchars($_POST["description"]);
        }

        // Si toutes les validations sont correctes
        if ($nomErr === '*' && $descErr === '*' && $tagErr === '*') {
            try {
                $idLoggedUser = $_SESSION['user_id'];

                // Préparation des données
                $data = [
                    'nom' => $nom,
                    'utilisateur_id' => $idLoggedUser,
                    'description' => $description,
                    'filtreTag' => $tag  // Le tag est valide ici
                ];

                // Insertion dans la base de données
                insertData($pdo, 'categorie', $data);

                echo "Bravo, la catégorie a été soumise avec succès.";
                exit();
            } catch (PDOException $e) {
                echo "Erreur lors de l'insertion : " . $e->getMessage();
            }
        }
    }
    ?>


    <form action="" class="submit-container" method="post">
        <div class="submit-subContainer">
            <label for="nom">Nom de la catégorie :</label>
            <?php if ($nomErr): ?>
                <p class="errorMSG" style="color: red;"><?php echo $nomErr; ?></p>
            <?php endif; ?>
            <input id="nom" type="text" name="nom" value="<?php echo $nom; ?>">
        </div>

        <div class="submit-subContainer">
            <div class="errorContainer">
                <label for="description">Description :</label>
                <?php if ($descErr): ?>
                    <p class="errorMSG" style="color: red;"><?php echo $descErr; ?></p>
                <?php endif; ?>
            </div>
            <textarea id="description" name="description"><?php echo $description; ?></textarea>
        </div>

        <div class="submit-subContainer">
            <label for="tag">Tag de filtrage :</label>
            <?php if ($tagErr): ?>
                <p class="errorMSG" style="color: red;"><?php echo $tagErr; ?></p>
            <?php endif; ?>
            <input id="tag" type="text" name="tag" value="<?php echo $tag; ?>">
        </div>
        
        </div>


        <button type="submit">Envoyer</button>
    </form>
</section>
</main>

<?php include 'components/footer.php'; ?>

</body>
</html>