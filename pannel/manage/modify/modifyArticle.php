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
        $productId = intval($_GET['id']); // Convertit l'ID en entier
    
        // Prépare et exécute la requête pour récupérer un produit spécifique
        $stmt = $pdo->prepare("SELECT * FROM article WHERE id = :id");
        $stmt->bindParam(':id', $productId, PDO::PARAM_INT);
        $stmt->execute();
    
        // Récupère le produit correspondant
        $produitSelectionne = $stmt->fetch(PDO::FETCH_ASSOC);
    
        // Si le produit est trouvé, récupérer les tags associés
        if ($produitSelectionne) {
            // Prépare et exécute la requête pour récupérer les tags associés à ce produit
            $tagStmt = $pdo->prepare("
                SELECT t.nom, t.id 
                FROM tag t 
                JOIN article_tag at ON t.id = at.tag_id 
                WHERE at.article_id = :id
            ");
            $tagStmt->bindParam(':id', $productId, PDO::PARAM_INT);
            $tagStmt->execute();
    
            // Récupère les tags associés
            $tags = $tagStmt->fetchAll(PDO::FETCH_ASSOC);
    
            // Récupérer tous les tags disponibles
            $allTagsStmt = $pdo->query("SELECT * FROM tag");
            $allTags = $allTagsStmt->fetchAll(PDO::FETCH_ASSOC);
        }
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

    <h1>Soumettre une fiche produit</h1>

    <?php 
    // Initialisation des variables
    $selectedCategory = '';
    $nom = '';
    $description = '';
    $image = '';
    $selectedTags = [];

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
    
        // Si toutes les validations sont correctes
        if ($categoryErr === '*' && $nomErr === '*' && $descErr === '*' && $imageErr === '*' && $tagsErr === '*') {
            try {
                // Récupérer l'ID de l'utilisateur connecté
                $idLoggedUser = $_SESSION['user_id'];
    
                if ($productId) {
                    // Si un ID de produit est fourni, on fait une mise à jour
                    $stmt = $pdo->prepare("
                        UPDATE article 
                        SET nom = :nom, utilisateur_id = :utilisateur_id, description = :description, image = :image, categorie_id = :categorie_id 
                        WHERE id = :id
                    ");
    
                    $stmt->bindParam(':nom', $nom);
                    $stmt->bindParam(':utilisateur_id', $idLoggedUser);
                    $stmt->bindParam(':description', $description);
                    $stmt->bindParam(':image', $image);
                    $stmt->bindParam(':categorie_id', $selectedCategory);
                    $stmt->bindParam(':id', $productId);
                    $stmt->execute();
    
                    // Mise à jour des tags
                    // Supprimer les anciens tags associés à l'article
                    $pdo->prepare("DELETE FROM article_tag WHERE article_id = ?")->execute([$productId]);
    
                    // Insérer les nouveaux tags
                    foreach ($selectedTags as $tagId) {
                        $pdo->prepare("INSERT INTO article_tag (article_id, tag_id) VALUES (?, ?)")
                            ->execute([$productId, $tagId]);
                    }
    
                    echo "L'article a été mis à jour avec succès.";
                } else {
                    // Si aucun ID de produit, faire une insertion (logique d'origine)
                    $data = [
                        'nom' => $nom,
                        'utilisateur_id' => $idLoggedUser,
                        'description' => $description,
                        'image' => $image,
                        'categorie_id' => $selectedCategory
                    ];
    
                    insertData($pdo, 'article', $data);
                    $articleId = $pdo->lastInsertId();
    
                    foreach ($selectedTags as $tagId) {
                        $pdo->prepare("INSERT INTO article_tag (article_id, tag_id) VALUES (?, ?)")
                            ->execute([$articleId, $tagId]);
                    }
    
                    echo "L'article a été créé avec succès.";
                }
            } catch (PDOException $e) {
                echo "Erreur lors de l'enregistrement : " . $e->getMessage();
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
        <input id="nom" type="text" name="nom" value="<?php echo isset($produitSelectionne['nom']) ? htmlspecialchars($produitSelectionne['nom']) : $nom; ?>">
    </div>

    <div class="submit-subContainer">
        <div class="errorContainer">
            <label for="description">Description :</label>
            <?php if ($descErr): ?>
                <p class="errorMSG" style="color: red;"><?php echo $descErr; ?></p>
            <?php endif; ?>
        </div>
        <textarea id="description" name="description"><?php echo isset($produitSelectionne['description']) ? htmlspecialchars($produitSelectionne['description']) : $description; ?></textarea>
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
                    <?php echo (isset($produitSelectionne['categorie_id']) && $produitSelectionne['categorie_id'] == $categorie['id']) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($categorie['nom'], ENT_QUOTES); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="submit-subContainer">
        <div style="display: flex; gap: 4px">
            <label>Assigner des tags :</label>
            <?php if ($tagsErr): ?>
                <p class="errorMSG" style="color: red;"><?php echo $tagsErr; ?></p>
                <?php endif; ?>
        </div>
        
        <div class="checkbox-container">
            <?php foreach ($allTags as $tag): ?>
                <div>
                    <input type="checkbox" id="tag_<?php echo $tag['id']; ?>" name="tags[]" value="<?php echo htmlspecialchars($tag['id'], ENT_QUOTES); ?>" 
                        <?php echo (isset($selectedTags) && in_array($tag['id'], array_column($tags, 'id'))) ? 'checked' : ''; ?>>
                    <label for="tag_<?php echo $tag['id']; ?>"><?php echo htmlspecialchars($tag['nom'], ENT_QUOTES); ?></label>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="submit-subContainer">
    <label for="image">URL de l'image :</label>
    <?php if ($imageErr): ?>
        <p class="errorMSG" style="color: red;"><?php echo $imageErr; ?></p>
    <?php endif; ?>
    <input id="image" type="text" name="image" value="<?php echo isset($produitSelectionne['image']) ? htmlspecialchars($produitSelectionne['image']) : $image; ?>" oninput="updateImagePreview()">
    <img id="image-preview" src="" alt="Aperçu de l'image" style="max-width: 200px; display: none;">
</div>

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

    <div class="submit-subContainer">
        <button type="submit" class="button">Modifier</button>
    </div>
</form>
</section>
</main>

<?php include '../../../components/footer.php'; ?>
</body>

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

</html>