<link rel="stylesheet" href="../css/navbar.css" />
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" />
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=League+Spartan:wght@100..900&display=swap" rel="stylesheet">

<header class="header">
    <div class="header-container">
        <h1 class="header-title"> <a href="/criticlick/index.php">CritiClick</a></h1>
        <div class="header-navigation">
            <a title="Page d'accueil du site" href="/criticlick/index.php">Accueil</a>
            <a title="Page avec tous les produits répertoriés" href="/criticlick/products.php">Produits répertoriés</a>
            <a title="Page à propos" href="/criticlick/AboutUs.php">À propos</a>
            <a href="/criticlick/contact.php">Contact</a>
        </div>

        <div class="header-option">

        <div class="header-option-container">
          <span id="user-icon" class="material-symbols-outlined">account_circle</span>
          <div id="user-menu" class="header-option-user-menu">
              <?php if (isset($_SESSION['username'])): ?>
                  <?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1): ?>
                      <a href="/criticlick/pannel/pannel.php" class="header-option-user-menu-option">Pannel Admin</a>
                      <div style="background-color: black; height: 1px; width: 100%"></div>
                  <?php endif; ?>
                      <a href="/criticlick/publishProduct.php" class="header-option-user-menu-option">Soumettre Fiche</a>
                      <a href="/criticlick/publishCategory.php" class="header-option-user-menu-option">Soumettre Catégorie</a>
                      <a href="/criticlick/publishTag.php" class="header-option-user-menu-option">Soumettre Tag</a>
                      <div style="background-color: black; height: 1px; width: 100%"></div>
                      <a href="/criticlick/logout.php" class="header-option-user-menu-option sign-out">Déconnexion</a>
              <?php else: ?>
                  <a href="/criticlick/login.php" class="header-option-user-menu-option">Connexion</a>
                  <a href="/criticlick/register.php" class="header-option-user-menu-option">Créer un compte</a>
              <?php endif; ?>
          </div>
        </div>

            <div class="header-option-container">
                <span id="menu-icon" class="material-symbols-outlined menu">menu</span>
                
                <div id="menu-user-menu" class="header-option-user-menu">
                    <p class="header-option-user-menu-option">Menu</p>
                    <div style="background-color: black; height: 1px; width: 100%"></div>
                    <a class="header-option-user-menu-option" title="Page d'accueil du site" href="/criticlick/index.php">Accueil</a>
                    <a class="header-option-user-menu-option" title="Page avec tous les produits répertoriés" href="/criticlick/products.php">Produits répertoriés</a>
                    <a class="header-option-user-menu-option" title="Page à propos" href="/criticlick/AboutUs.php">À propos</a>
                    <a class="header-option-user-menu-option" href="/criticlick/contact.php">Contact</a>
                    <div style="background-color: black; height: 1px; width: 100%"></div>
                </div>
            </div>

            <div class="header-option-container">
                <span class="material-symbols-outlined">search</span>
            </div>

        </div>
    </div>
</header>

<script>
document.addEventListener("DOMContentLoaded", () => {
  // Gestion du menu utilisateur avec l'image
  const userIcon = document.getElementById('user-icon');
  const userMenu = document.getElementById('user-menu');
  const userMenuOptions = document.querySelectorAll('#user-menu .header-option-user-menu-option');

  // Gestion du menu utilisateur avec le span "menu"
  const menuIcon = document.getElementById('menu-icon');
  const userMenu2 = document.getElementById('menu-user-menu');
  const userMenuOptions2 = document.querySelectorAll('#menu-user-menu .header-option-user-menu-option');

  const toggleUserMenu = (event) => {
    event.stopPropagation();
    userMenu.classList.toggle('open');

    // Si le menu 2 est ouvert, on le ferme
    if (userMenu2.classList.contains('open')) {
      userMenu2.classList.remove('open');
    }
  };

  const toggleMenu2 = (event) => {
    event.stopPropagation();
    userMenu2.classList.toggle('open');

    // Si le menu 1 est ouvert, on le ferme
    if (userMenu.classList.contains('open')) {
      userMenu.classList.remove('open');
    }
  };

  const closeUserMenu = () => {
    userMenu.classList.remove('open');
  };

  const closeMenu2 = () => {
    userMenu2.classList.remove('open');
  };

  userIcon.addEventListener('click', toggleUserMenu);
  menuIcon.addEventListener('click', toggleMenu2);

  userMenuOptions.forEach(option => {
    option.addEventListener('click', closeUserMenu);
  });

  userMenuOptions2.forEach(option => {
    option.addEventListener('click', closeMenu2);
  });

  document.addEventListener('click', (event) => {
    if (!userMenu.contains(event.target) && !userIcon.contains(event.target)) {
      closeUserMenu();
    }
    if (!userMenu2.contains(event.target) && !menuIcon.contains(event.target)) {
      closeMenu2();
    }
  });

  window.addEventListener('scroll', () => {
    closeUserMenu();
    closeMenu2();
  });
});
</script>