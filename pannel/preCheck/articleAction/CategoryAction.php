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

    // Si la requête est de type POST, gérer les actions sur les articles ou catégories
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $action = $_POST['action'];
        $id = $_POST['id'];

        if ($action === 'delete_category') {
            // Supprimer la catégorie
            $stmt = $pdo->prepare("DELETE FROM categorie WHERE id = :id");
            $stmt->execute(['id' => $id]);
            echo "<script>
                    alert('La catégorie a été supprimée avec succès.');
                    window.location.href = '/criticlick/pannel/preCheck/catCheck.php';
                  </script>";
            exit();
        } elseif ($action === 'publish_category') {
            // Publier la catégorie
            $stmt = $pdo->prepare("UPDATE categorie SET published = 1 WHERE id = :id");
            $stmt->execute(['id' => $id]);
            echo "<script>
                    alert('La catégorie a été publiée avec succès.');
                    window.location.href = '/criticlick/pannel/preCheck/catCheck.php';
                  </script>";
            exit();
        }
    }
} catch (PDOException $e) {
    echo "Erreur : " . htmlspecialchars($e->getMessage());
}
?>