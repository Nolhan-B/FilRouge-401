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

    // Requête pour récupérer les catégories non publiées avec le créateur
    $stmt = $pdo->prepare('
        SELECT categorie.*, utilisateur.username 
        FROM categorie 
        JOIN utilisateur ON categorie.utilisateur_id = utilisateur.id 
        WHERE categorie.published = 0
    ');
    $stmt->execute();
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    echo 'Erreur de connexion à la base de données : ' . htmlspecialchars($e->getMessage());
    $categories = [];
}
?>

<!DOCTYPE html>
<html lang='fr'>
<head>
    <link rel='stylesheet' href='../../css/navbar.css'>
    <link rel='stylesheet' href='../../css/footer.css'>
    <link rel='stylesheet' href='catCheck.css'>
    <link href='https://fonts.googleapis.com/css2?family=League+Spartan:wght@100..900&display=swap' rel='stylesheet'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>CritiClick - Vérifier les catégories</title>
</head>
<body>

<?php include '../../components/navbar.php'; ?>

<main>
    <section class='validation'>
        <h1 style='padding-top:150px;'>Vérifier les catégories</h1>

        <div class='validation-container'>
        <?php
            // Affichage des résultats
            if (!empty($categories)) {
                foreach ($categories as $index => $categorie) {
                    echo '
                    <div class="container-item"> 
                        <p><strong>Catégorie :</strong> ' . htmlspecialchars($categorie['nom']) . '</p>
                        <p><strong>Tag de Filtrage :</strong> ' . htmlspecialchars($categorie['filtreTag']) . '</p>
                        <p><strong>Créateur :</strong> ' . htmlspecialchars($categorie['username']) . '</p>
                        
                        <div class="buttons">
                            <form method="POST" action="/criticlick/pannel/preCheck/articleAction/CategoryAction.php" style="display:inline;">
                                <input type="hidden" name="id" value="' . $categorie['id'] . '">
                                <input type="hidden" name="action" value="delete_category">
                                <button type="submit">Supprimer</button>
                            </form>
                    
                            <form method="POST" action="/criticlick/pannel/preCheck/articleAction/CategoryAction.php" style="display:inline;">
                                <input type="hidden" name="id" value="' . $categorie['id'] . '">
                                <input type="hidden" name="action" value="publish_category">
                                <button type="submit">Publier</button>
                            </form>
                        </div>
                    </div>';
                }
            } else {
                echo '<p>Aucune demande disponible.</p>';
            }
        ?>
    </section>
</main>

<?php include '../../components/footer.php'; ?>

</body>
</html>