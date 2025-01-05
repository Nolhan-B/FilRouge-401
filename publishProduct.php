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
    $stmt = $pdo->query("SELECT id, nom, filtreTag FROM categorie WHERE published = 1");
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Erreur de connexion à la base de données : " . $e->getMessage();
    $categories = [];
}

try {
    // Requête pour récupérer les tags
    $stmt = $pdo->query("SELECT id, nom FROM tag");
    $tags = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Erreur de connexion à la base de données : " . $e->getMessage();
    $tags = [];
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

    <h1>Soumettre une fiche produit</h1>

    <?php 
    // Initialisation des variables
    $selectedCategory = '';
    $nom = '';
    $description = '';
    $image = '';
    $selectedTags = '';

    $categoryErr = '*';
    $nomErr = '*';
    $descErr = '*';
    $imageErr = '*';
    $tagsErr = '*';

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Validation de la catégorie sélectionnée
        if (empty($_POST["categoryFilter"])) {
            $categoryErr = "* Vous devez choisir au moins une catégorie.";
        } else {
            $selectedCategory = htmlspecialchars($_POST["categoryFilter"]);
        }
    
        // Validation des tags
        if (empty($_POST["tags"])) {
            $tagsErr = "* Vous devez choisir au moins deux tags.";
        } else {
            $selectedTags = $_POST["tags"];
            if (count($selectedTags) < 2 || count($selectedTags) > 4) {
                $tagsErr = "* Vous devez choisir entre 2 et 4 tags.";
            }
        }
    
        if (empty($_POST["nom"])) {
            $nomErr = "* Le nom est requis";
        } else if (strlen($_POST["nom"]) <= 6) {
            $nomErr = "* Le nom est trop court";
        } else {
            $nom = htmlspecialchars($_POST["nom"]);
        }
    
        if (empty($_POST["description"])) {
            $descErr = "* La description est requise";
        } else if (strlen($_POST["description"]) <= 50) {
            $descErr = "* La description est trop courte";
        } else {
            $description = htmlspecialchars($_POST["description"]);
        }
    
        if (empty($_POST["image"])) {
            $imageErr = "* L'URL de l'image est requise";
        } else {
            $image = htmlspecialchars($_POST["image"]);
        }
    
        // Si toutes les validations sont correctes, insérer dans la base de données
        if ($categoryErr === '*' && $nomErr === '*' && $descErr === '*' && $imageErr === '*' && $tagsErr === '*') {
            try {
                // Récupérer l'ID de l'utilisateur connecté
                $idLoggedUser = $_SESSION['user_id'];

                // Préparer les données à insérer
                $data = [
                    'nom' => $nom,
                    'utilisateur_id' => $idLoggedUser,
                    'description' => $description,
                    'image' => $image,
                    'categorie_id' => $selectedCategory
                ];

                // Insérer l'article dans la table article
                insertData($pdo, 'article', $data);
                $articleId = $pdo->lastInsertId(); // Récupérer l'ID du dernier article inséré

                // Insérer les tags dans la table de relation article_tag
                foreach ($selectedTags as $tagId) {
                    $pdo->prepare("INSERT INTO article_tag (article_id, tag_id) VALUES (?, ?)")
                        ->execute([$articleId, $tagId]);
                }

                // Redirection après insertion
                echo "Bravo, c'est envoyé";
                exit();
            } catch (PDOException $e) {
                echo "Erreur lors de l'insertion : " . $e->getMessage();
            }
        }
    }
    ?>

    <form action="" class="submit-container" method="post">
        <div class="submit-subContainer">
            <label for="nom">Nom de l'article :</label>
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
            <label for="categoryFilter">Assigner une catégorie :</label>
            <?php if ($categoryErr): ?>
                <p class="errorMSG" style="color: red;"><?php echo $categoryErr; ?></p>
            <?php endif; ?>

            <select class="categoryFilter" id="categoryFilter" name="categoryFilter">
                <option value="">Choisir une catégorie</option> 
                <?php foreach ($categories as $categorie): ?>
                    <option value="<?php echo htmlspecialchars($categorie['id'], ENT_QUOTES); ?>" 
                        <?php echo ($selectedCategory === htmlspecialchars($categorie['id'], ENT_QUOTES)) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($categorie['nom'], ENT_QUOTES); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="submit-subContainer">
            <label for="tags">Assigner des tags :</label>
            <?php if ($tagsErr): ?>
                <p class="errorMSG" style="color: red;"><?php echo $tagsErr; ?></p>
            <?php endif; ?>

            <div class="checkbox-container">
                <?php foreach ($tags as $tag): ?>
                    <div>
                        <input type="checkbox" id="tag_<?php echo $tag['id']; ?>" name="tags[]" value="<?php echo htmlspecialchars($tag['id'], ENT_QUOTES); ?>">
                        <label for="tag_<?php echo $tag['id']; ?>"><?php echo htmlspecialchars($tag['nom'], ENT_QUOTES); ?></label>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        
        <div class="submit-subContainer">
            <label for="image">Image (URL) :</label>
            <?php if ($imageErr): ?>
                <p class="errorMSG" style="color: red;"><?php echo $imageErr; ?></p>
            <?php endif; ?>
            <input id="image" type="text" name="image" value="<?php echo $image; ?>" oninput="updateImagePreview()">
            <img id="image-preview" class="image-preview" src="<?php echo $image; ?>" alt="Aperçu de l'image">
        </div>

        <button type="submit">Envoyer</button>
    </form>
</section>
</main>

<?php include 'components/footer.php'; ?>

<script>
    function updateImagePreview() {
        const imageInput = document.getElementById('image');
        const imagePreview = document.getElementById('image-preview');
        
        const imageUrl = imageInput.value;
        if (imageUrl) {
            imagePreview.src = imageUrl;
            imagePreview.style.display = 'block'; // Afficher l'aperçu si l'URL est valide
        } else {
            imagePreview.src = ''; 
            imagePreview.style.display = 'none'; // Masquer l'aperçu si l'input est vide
        }
    }
</script>
</body>
</html>