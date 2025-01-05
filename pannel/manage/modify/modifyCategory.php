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
    // Connexion à la base de données
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Récupérer toutes les catégories disponibles
    $categoriesStmt = $pdo->query("SELECT * FROM categorie");
    $categories = $categoriesStmt->fetchAll(PDO::FETCH_ASSOC); // Récupérer toutes les catégories

    if (isset($_GET['id'])) {
        $categoryId = intval($_GET['id']); // Convertit l'ID en entier
    
        // Prépare et exécute la requête pour récupérer une catégorie spécifique
        $stmt = $pdo->prepare("SELECT * FROM categorie WHERE id = :id");
        $stmt->bindParam(':id', $categoryId, PDO::PARAM_INT);
        $stmt->execute();
    
        // Récupère la catégorie correspondante
        $categorieSelectionnee = $stmt->fetch(PDO::FETCH_ASSOC);
    }

} catch (PDOException $e) {
    echo "<p>Erreur de connexion : " . htmlspecialchars($e->getMessage()) . "</p>";
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <link rel="stylesheet" href="./modifyArticle.css">
    <link rel="stylesheet" href="/criticlick/css/navbar.css">
    <link rel="stylesheet" href="/criticlick/css/footer.css">
    
    <link href="https://fonts.googleapis.com/css2?family=League+Spartan:wght@100..900&display=swap" rel="stylesheet">
    <meta name="viewport" content="width=device-width,initial-scale=1.0,minimum-scale=1.0,maximum-scale=1.0">
    <title>CritiClick - Fil rouge</title>
</head>
<body>

<?php include '../../../components/navbar.php'; ?>

<main>
<section class="submit">

    <h1>Soumettre une fiche catégorie</h1>

    <?php 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validation du nom
    if (empty($_POST["nom"])) {
        $nomErr = "* Le nom est requis";
    } else if (strlen($_POST["nom"]) <= 4) {
        $nomErr = "* Le nom est trop court";
    } else {
        $nom = htmlspecialchars($_POST["nom"]);

        // Exclure l'enregistrement actuel de la vérification (lors d'une mise à jour)
        if (isset($categoryId)) {
            // Vérifier si le nom existe déjà dans une autre catégorie
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM categorie WHERE nom = :nom AND id != :id");
            $stmt->execute(['nom' => $nom, 'id' => $categoryId]);
        } else {
            // Vérifier si le nom existe déjà (pour l'ajout d'une nouvelle catégorie)
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM categorie WHERE nom = :nom");
            $stmt->execute(['nom' => $nom]);
        }
        
        $nomExists = $stmt->fetchColumn();

        if ($nomExists) {
            $nomErr = "* Ce nom existe déjà, veuillez en choisir un autre";
        } else {
            $nomErr = "*"; // Pas d'erreur
        }
    }

    // Validation du tag
    if (empty($_POST["tag"])) {
        $tagErr = "* Le tag est requis";
    } else if (strpos($_POST["tag"], ' ') !== false) {
        $tagErr = "* Le tag doit être un seul mot sans espaces";
    } else {
        $tag = htmlspecialchars($_POST["tag"]);

        // Exclure l'enregistrement actuel de la vérification (lors d'une mise à jour)
        if (isset($categoryId)) {
            // Vérifier si le tag existe déjà dans une autre catégorie
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM categorie WHERE filtreTag = :tag AND id != :id");
            $stmt->execute(['tag' => $tag, 'id' => $categoryId]);
        } else {
            // Vérifier si le tag existe déjà (pour l'ajout d'une nouvelle catégorie)
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM categorie WHERE filtreTag = :tag");
            $stmt->execute(['tag' => $tag]);
        }

        $tagExists = $stmt->fetchColumn();
        
        if ($tagExists) {
            $tagErr = "* Ce tag existe déjà, veuillez en choisir un autre";
        } else {
            $tagErr = "*";
        }
    }

    // Si tout est validé
    if ($nomErr === '*' && $descErr === '*' && $tagErr === '*') {
        try {
            // Récupérer l'ID de l'utilisateur connecté
            $idLoggedUser = $_SESSION['user_id'];

            if ($categoryId) {
                // Mettre à jour la catégorie si l'ID est présent
                $stmt = $pdo->prepare("
                    UPDATE categorie 
                    SET nom = :nom, description = :description, filtreTag = :tag
                    WHERE id = :id
                ");
                
                $stmt->bindParam(':nom', $nom);
                $stmt->bindParam(':description', $description);
                $stmt->bindParam(':tag', $tag);
                $stmt->bindParam(':id', $categoryId);
                $stmt->execute();
                
                echo "La catégorie a été mise à jour avec succès.";
            } 
        } catch (PDOException $e) {
            echo "Erreur lors de l'enregistrement : " . $e->getMessage();
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
        <!-- Préremplir le champ nom avec la valeur de la catégorie sélectionnée -->
        <input id="nom" type="text" name="nom" value="<?php echo isset($categorieSelectionnee['nom']) ? htmlspecialchars($categorieSelectionnee['nom']) : $nom; ?>">
    </div>

    <div class="submit-subContainer">
        <div class="errorContainer">
            <label for="description">Description :</label>
            <?php if ($descErr): ?>
                <p class="errorMSG" style="color: red;"><?php echo $descErr; ?></p>
            <?php endif; ?>
        </div>
        <!-- Préremplir le champ description avec la valeur de la catégorie sélectionnée -->
        <textarea id="description" name="description"><?php echo isset($categorieSelectionnee['description']) ? htmlspecialchars($categorieSelectionnee['description']) : $description; ?></textarea>
    </div>

    <div class="submit-subContainer">
        <label for="tag">Tag de filtrage :</label>
        <?php if ($tagErr): ?>
            <p class="errorMSG" style="color: red;"><?php echo $tagErr; ?></p>
        <?php endif; ?>
        <!-- Préremplir le champ tag avec la valeur de la catégorie sélectionnée -->
        <input id="tag" type="text" name="tag" value="<?php echo isset($categorieSelectionnee['filtreTag']) ? htmlspecialchars($categorieSelectionnee['filtreTag']) : $tag; ?>">
    </div>

    <button type="submit">Envoyer</button>
</form>
</section>
</main>

<?php include '../../../components/footer.php'; ?>
</body>

</html>