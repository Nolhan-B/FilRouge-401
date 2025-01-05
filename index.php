<?php session_start();
// Fonction pour récupérer des données avec cURL

$host = 'localhost'; // Hôte de la base de données
$db = 'criticlick'; // Remplace par le nom de ta base de données
$username = 'root'; // Nom d'utilisateur
$password = ''; // Mot de passe

try {
    // Créer une instance de PDO
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // Gérer les erreurs avec des exceptions
} catch (PDOException $e) {
    echo "Erreur de connexion : " . htmlspecialchars($e->getMessage());
    exit;
}

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <link rel="stylesheet" href="css/index.css" />
    <link rel="stylesheet" href="css/navbar.css">
    <link rel="stylesheet" href="css/footer.css">
    
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=League+Spartan:wght@100..900&display=swap" rel="stylesheet">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CritiClick - Fil rouge</title>
</head>

<body>

<?php include 'components/navbar.php'; ?>

<main>
<section class="hero-Section">
    <div class="hero-container">
        <h1 class="hero-title">CritiClick</h1>
        <h1 class="hero-title2">CritiClick</h1>
        <img class="hero-bg" src="img/indexbg.webp" alt="Fond d'écran d'accueil, représente un grenier plein d'affaires">
        <button onclick="scrollToHiw()" class="hero-cta">Comment ça marche ?</button>
    </div>

    <script>
        function scrollToHiw() {
            const element = document.getElementById("hiwscroller");
            const offset = -150;
            const elementPosition = element.getBoundingClientRect().top;
            const offsetPosition = elementPosition + window.pageYOffset + offset;

            window.scrollTo({
                top: offsetPosition,
                behavior: "smooth"
            });
        }
    </script>

</section>


<section id="hiwscroller" class="hiw">
    <h1 class="hiw-title">Comment ça marche ?</h1>
    <div class="hiw-container">
        <div class="hiw-item">
            <p class="hiw-item-title">Besoin d'avis?</p>
            <p class="hiw-item-desc">Vous envisagez d’acheter quelque chose en ligne mais avez des doutes sur votre satisfaction potentielle ? Consultez les avis des utilisateurs CritiClick pour faire le bon choix et acheter en toute confiance.</p>
        </div>
        <div class="hiw-item">
            <p class="hiw-item-title">Envie d'aider ?</p>
            <p class="hiw-item-desc">Vous souhaitez partager votre expérience sur un produit acheté en ligne ? Rejoignez la communauté CritiClick en <a class="hiw-item-desc-a" href="signin.php">Créant un compte</a> ou en <a class="hiw-item-desc-a" href="login.php">vous connectant</a>. Publiez vos avis, notez les produits que vous avez testés, et aidez les autres utilisateurs à faire des choix éclairés en toute confiance.</p>
        </div>
    </div>
</section>


<section id="products" class="products">
    <h1>Découvrir par catégories</h1>
    <div class="products-container" id="products-container">
    <?php
        $query = "SELECT * FROM categorie WHERE published = 1"; // Modifie la condition si nécessaire
        $stmt = $pdo->prepare($query);
        $stmt->execute();

        // Récupérer les résultats
        $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Afficher les résultats
        if ($categories && is_array($categories)) {
            foreach ($categories as $categorie) {
                echo "
                <div class='products-item'>
                    <h2 class='products-item-name'>" . htmlspecialchars($categorie['nom']) . "</h2>
                    <p class='products-item-text'>" . htmlspecialchars($categorie['description']) . "</p>
                    <a href='products.php?category=" . strtolower(urlencode(htmlspecialchars($categorie['filtreTag']))) . "&search='>
                        <button class='products-item-button'>Découvrir</button>
                    </a>
                </div>
                ";
            }
        } else {
            echo "<p>Aucune catégorie disponible.</p>";
        }
    ?>
    </div>
</section>

</main>

<?php include 'components/footer.php'; ?>

</body>
</html>