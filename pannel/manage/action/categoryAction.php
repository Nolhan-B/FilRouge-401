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
        $categorie_id = $_POST['categorie_id'];

        if ($action === 'delete') {
            // Supprimer la catégorie
            $stmt = $pdo->prepare("DELETE FROM categorie WHERE id = :id");
            $stmt->execute(['id' => $categorie_id]);
            echo "
            <script>
                alert('La catégorie a été supprimée avec succès.');
                window.location.href = '/criticlick/pannel/manage/manageCategory.php';
            </script>";
            exit();
        } elseif ($action === 'unpublish') {
            // Dépublier la catégorie
            $stmt = $pdo->prepare("UPDATE categorie SET published = 0 WHERE id = :id");
            $stmt->execute(['id' => $categorie_id]);
            echo "
            <script>
                alert('La catégorie a été enlevée des publiées avec succès.');
                window.location.href = '/criticlick/pannel/manage/manageCategory.php';
            </script>";
            exit();
        }
    }
} catch (PDOException $e) {
    echo "Erreur : " . htmlspecialchars($e->getMessage());
}
?>