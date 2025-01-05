<?php
// Démarrer la session
session_start();

// Détruire toutes les variables de session
$_SESSION = array();

session_destroy();

// Rediriger vers la page d'accueil après déconnexion
header("Location: /criticlick/index.php");
exit();
?>