<?php
session_start();

$host = 'localhost'; 
$db = 'criticlick'; 
$username = 'root'; 
$password = '';  

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_comment'])) {
    $commentId = intval($_POST['comment_id']);
    $redirectID = intval($_POST['article_id']);
    $userId = $_SESSION['user_id'];


    try {
        $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Vérifier si l'utilisateur est l'auteur ou un administrateur
        $checkStmt = $pdo->prepare("SELECT utilisateur_id FROM avis WHERE id = :id");
        $checkStmt->bindParam(':id', $commentId, PDO::PARAM_INT);
        $checkStmt->execute();
        $comment = $checkStmt->fetch(PDO::FETCH_ASSOC);

        // Ajoutez la condition avant de supprimer le commentaire
        if ($comment && ($comment['utilisateur_id'] === $userId || $_SESSION['is_admin'] == 1)) {
            // Suppression du commentaire
            $deleteStmt = $pdo->prepare("DELETE FROM avis WHERE id = :id");
            $deleteStmt->bindParam(':id', $commentId, PDO::PARAM_INT);
            $deleteStmt->execute();
            echo"
            <script>
                alert('Le commentaire a été supprimé avec succès.');
                window.location.href = '/criticlick/ProductDetails.php?id=".$redirectID."';
            </script>
            ";
        } else {
            echo"
            <script>
                alert('Vous n\\avez rien à faire içi.');
                window.location.href = '/criticlick/ProductDetails.php?id=".$redirectID."';
            </script>
            ";
        }
            exit();
    } catch (PDOException $e) {
        echo "Erreur lors de la suppression : " . htmlspecialchars($e->getMessage());
    }
}
?>