<?php
session_start();
header('Content-Type: application/json');

$host = 'localhost'; 
$db = 'criticlick'; 
$username = 'root'; 
$password = '';  

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); 

    $stmt = $pdo->prepare("SELECT * FROM article WHERE published = 1"); 
    $stmt->execute();

    $produits = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($produits);
} catch (PDOException $e) {
    echo json_encode(['error' => "Erreur de connexion : " . htmlspecialchars($e->getMessage())]);
    exit;
}
?>