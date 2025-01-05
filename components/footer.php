<link rel="stylesheet" href="../css/footer.css" />
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" />
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=League+Spartan:wght@100..900&display=swap" rel="stylesheet">

<footer>
    <a class="CritiClick" href="/">Criticlick</a>

    <div class="footer-element">
        <a href="/criticlick/index.php" class="footer-element-line title">Accueil</a>
        <a class="footer-element-line" href="/criticlick/index.php#hiwscroller">Comment ça marche?</a>
        <a class="footer-element-line" href="/criticlick/index.php#products">Recommandation</a>
    </div>
    
    <?php if (isset($_SESSION['username'])): ?>
        <div class="footer-element">
        <p class="footer-element-line title">Contribuer</p>
        <a class="footer-element-line" href="/criticlick/publishProduct.php">Soumettre Fiche</a>
        <a class="footer-element-line" href="/criticlick/publishCategory.php">Soumettre Catégorie</a>
    </div>
    <?php else: ?><?php endif; ?>

    <div class="footer-element">
        <a href="/criticlick/products.php" class="footer-element-line title">Produits répertoriés</a>
    </div>


    <div class="footer-element">
        <a href="/criticlick/AboutUs.php" class="footer-element-line title">À propos</a>
    </div>

    <div class="footer-element">
        <a href="/criticlick/contact.php" class="footer-element-line title">Contact</a>
    </div>

    <div class="footer-element">
        <p class="footer-element-line title">Authentification</p>

        <?php if (isset($_SESSION['username'])): ?>
            <a href="/criticlick/logout.php" class="footer-element-line">Déconnexion</a>
        <?php else: ?>
            <a href="/criticlick/register.php" class="footer-element-line">Créer un compte</a>
            <a href="/criticlick/login.php" class="footer-element-line">Connexion</a>
        <?php endif; ?>
    </div>

    <div class="footer-lowbar">Criticlick 2024© - Tous droits réservés</div>
</footer>