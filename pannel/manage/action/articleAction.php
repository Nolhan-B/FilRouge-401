<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['is_admin'] == 0) {
    header('Location: /criticlick/index.php');
    exit();
}

$host = 'localhost'; 
$dbname = 'criticlick'; 
$username = 'root'; 
$password = '';  

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $action = $_POST['action'];
        $article_id = $_POST['article_id'];

        if ($action === 'delete') {
            // Supprimer l'article
            $stmt = $pdo->prepare("DELETE FROM article WHERE id = :id");
            $stmt->execute(['id' => $article_id]);
            echo "
            <script>
                alert('L\\'article a été supprimé avec succès.');
                window.location.href = '/criticlick/pannel/manage/manageArticle.php';
            </script>";
            exit();
        } elseif ($action === 'unpublish') {
            // Publier l'article
            $stmt = $pdo->prepare("UPDATE article SET published = 0 WHERE id = :id");
            $stmt->execute(['id' => $article_id]);
            echo "
            <script>
                alert('L\\'article a été enlevé des publiés avec succès.');
                window.location.href = '/criticlick/pannel/manage/manageArticle.php';
            </script>";
            exit();
        }
    }
} catch (PDOException $e) {
    echo "Erreur : " . htmlspecialchars($e->getMessage());
}
?>