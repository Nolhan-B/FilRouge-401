<?php 
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: /login.php');
    exit();
}

if ($_SESSION['is_admin'] == 0) {
    header('Location: /index.php');
}

$host = 'localhost'; 
$dbname = 'criticlick'; 
$username = 'root'; 
$password = '';  

try {
    // Connexion à la base de données
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Requête pour récupérer les articles non publiés avec la catégorie et le créateur
    $stmt = $pdo->prepare('
        SELECT a.*, c.nom AS categorie_nom, u.username AS auteur
        FROM article a
        JOIN categorie c ON a.categorie_id = c.id
        JOIN utilisateur u ON a.utilisateur_id = u.id
        WHERE a.published = :published
    ');
    $stmt->execute(['published' => 0]);
    $produits = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Récupérer les tags pour chaque article sans utiliser de référence
    foreach ($produits as $index => $produit) {  // Utilisation d'un index ici
        $tagStmt = $pdo->prepare('
            SELECT t.nom
            FROM tag t
            JOIN article_tag at ON t.id = at.tag_id
            WHERE at.article_id = :article_id
        ');
        $tagStmt->execute(['article_id' => $produit['id']]);
        $produits[$index]['tags'] = $tagStmt->fetchAll(PDO::FETCH_COLUMN);
    }
} catch (PDOException $e) {
    echo 'Erreur de connexion à la base de données : ' . htmlspecialchars($e->getMessage());
    $produits = [];
}
?>

<!DOCTYPE html>
<html lang='fr'>
<head>
    <link rel='stylesheet' href='../../css/navbar.css'>
    <link rel='stylesheet' href='../../css/footer.css'>
    <link rel='stylesheet' href='articleCheck.css'>
    <link href='https://fonts.googleapis.com/css2?family=League+Spartan:wght@100..900&display=swap' rel='stylesheet'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>CritiClick - Vérifier les fiches produits</title>
</head>
<body>

<?php include '../../components/navbar.php'; ?>

<main>
    <section class='validation'>
        <h1 style='padding-top:150px;'>Vérifier les fiches produits</h1>

        <div class='validation-container'>
        <?php
            // Affichage des résultats
            if (!empty($produits)) {
                foreach ($produits as $produit) {
                    echo '
                    <div class="container-item">
                        <h2 class="container-item-name">' . htmlspecialchars($produit['nom']) . '</h2>
                        <p class="container-item-text">' . htmlspecialchars($produit['description']) . '</p>
                        <p><strong>Catégorie :</strong> ' . htmlspecialchars($produit['categorie_nom']) . '</p>
                        <p><strong>Créé par :</strong> ' . htmlspecialchars($produit['auteur']) . '</p>
                        
                        <div class="container-item-tags">
                            <strong>Tags :</strong>
                            <div class="tags-wrapper">';
                            if (!empty($produit['tags'])) {
                                foreach ($produit['tags'] as $tag) {
                                    echo '<span class="tag">' . htmlspecialchars($tag) . '</span> ';
                                }
                            } else {
                                echo 'Aucun tag';
                            }
                    echo '  </div>
                        </div>
                    <img class="container-item-image" src="' . htmlspecialchars($produit['image']) . '"></img>

                    <div class="buttons">
                    <form method="POST" action="/criticlick/pannel/preCheck/articleAction/articleAction.php" style="display:inline;">
                        <input type="hidden" name="article_id" value="' . $produit['id'] . '">
                        <input type="hidden" name="action" value="delete">
                        <button type="submit">Supprimer</button>
                    </form>
            
                    <form method="POST" action="/criticlick/pannel/preCheck/articleAction/articleAction.php" style="display:inline;">
                        <input type="hidden" name="article_id" value="' . $produit['id'] . '">
                        <input type="hidden" name="action" value="publish">
                        <button type="submit">Publier</button>
                    </form>
                </div>

                    </div>';
                }
            } else {
                echo '<p>Aucun demande disponible.</p>';
            }
        ?>
    </section>
</main>

<?php include '../../components/footer.php'; ?>

</body>
</html>