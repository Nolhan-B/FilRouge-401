<?php session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: /login.php');
    exit();
}

if ($_SESSION['is_admin'] == 0) {
    header('Location: ./index.php');
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <link rel="stylesheet" href="pannel.css">
    <link rel="stylesheet" href="../css/navbar.css">
    <link rel="stylesheet" href="../css/footer.css">

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <?php include '../components/navbar.php'; ?>

<main>

<section class="pannel">

    <div class="pannel-container">


        <form class="pannel-choice" action="../SQL/init.php" method="POST">
            <p class="pannel-choice-title">Réinitialiser la base de donnée</p>
            <button title="Réinitialiser la base de donnée" class="pannel-choice-btn" type="submit">Lancer le processus</button>
        </form>

        <div style="background-color: rgb(211, 255, 211);" class="pannel-container-wrapper">
            <div class="pannel-choice">
                <p class="pannel-choice-title">Articles en attente de publications</p>
                    <a href="/criticlick/pannel/preCheck/articleCheck.php">
                        <button title="Voir les articles en suspend" class="pannel-choice-btn">Voir</button>
                    </a>
                </button>
            </div>
            <div class="pannel-choice">
                <p class="pannel-choice-title">Catégories en attente de publications</p>
                    <a href="/criticlick/pannel/preCheck/catCheck.php">
                        <button title="Voir les catégories en suspend" class="pannel-choice-btn">Voir</button>
                    </a>
            </div>
        </div>

        <div style="background-color: rgb(255, 179, 179);" class="pannel-container-wrapper">
            <div class="pannel-choice">
                    <p class="pannel-choice-title">Gérer les articles</p>
                        <a href="/criticlick/pannel/manage/manageArticle.php">
                            <button title="Voir les articles en suspend" class="pannel-choice-btn">
                                Voir
                            </button>
                        </a>
                </div>
                <div class="pannel-choice">
                    <p class="pannel-choice-title">Gérer les catégories</p>
                    <a href="/criticlick/pannel/manage/manageCategory.php">
                        <button title="Voir les catégories en suspend" class="pannel-choice-btn">Voir</button>
                    </a>
                    
                </div>
        </div>
        
    </div>

</section>
    

</main>

    <?php include '../components/footer.php'; ?>
</body>
</html>