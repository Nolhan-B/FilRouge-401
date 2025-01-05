<?php
session_start();

$host = 'localhost'; 
$db = 'criticlick'; 
$username = 'root'; 
$password = '';  

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); 

    if (isset($_GET['id'])) {
        $productId = intval($_GET['id']);

        $stmt = $pdo->prepare("SELECT * FROM article WHERE id = :id AND published = 1");
        $stmt->bindParam(':id', $productId, PDO::PARAM_INT);
        $stmt->execute();

        $produitSelectionne = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($produitSelectionne) {
            $tagStmt = $pdo->prepare(
                "SELECT t.nom 
                 FROM tag t 
                 JOIN article_tag at ON t.id = at.tag_id 
                 WHERE at.article_id = :id"
            );
            $tagStmt->bindParam(':id', $productId, PDO::PARAM_INT);
            $tagStmt->execute();

            $tags = $tagStmt->fetchAll(PDO::FETCH_ASSOC);
        }
    }
} catch (PDOException $e) {
    echo "<p>Erreur de connexion : " . htmlspecialchars($e->getMessage()) . "</p>";
}

function insertData($pdo, $table, $data) {
    $columns = implode(", ", array_keys($data));
    $placeholders = ":" . implode(", :", array_keys($data));
    $sql = "INSERT INTO $table ($columns) VALUES ($placeholders)";
    $stmt = $pdo->prepare($sql);

    foreach ($data as $key => $value) {
        $stmt->bindValue(":$key", $value);
    }

    if ($stmt->execute()) {
        return $pdo->lastInsertId();
    }
    return false;
}

$note = '';
$prix = '';
$commentaire = '';
$lienAchat = '';

$noteErr = '*';
$prixErr = '*';
$commentaireErr = '*';
$lienAchatErr = '*';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validation de la note
    if (empty($_POST["note"])) {
        $noteErr = "* La note est requise";
    } else if ($_POST["note"] < 0 || $_POST["note"] > 5) {
        $noteErr = "* La note doit être entre 0 et 5";
    } else {
        $note = intval($_POST["note"]);
        $noteErr = "*"; // Pas d'erreur
    }

    // Validation du prix
    if (empty($_POST["prix"])) {
        $prixErr = "* Le prix est requis";
    } else if (!is_numeric($_POST["prix"])) {
        $prixErr = "* Le prix doit être un nombre valide";
    } else {
        $prix = floatval($_POST["prix"]);
        $prixErr = "*"; // Pas d'erreur
    }

    // Validation du commentaire
    if (empty($_POST["commentaire"])) {
        $commentaireErr = "* Le commentaire est requis";
    } else if (strlen($_POST["commentaire"]) < 10) {
        $commentaireErr = "* Le commentaire est trop court";
    } else {
        $commentaire = htmlspecialchars($_POST["commentaire"]);
        $commentaireErr = "*"; // Pas d'erreur
    }

    // Validation du lien d'achat
    if (!empty($_POST["lienAchat"])) {
        if (!filter_var($_POST["lienAchat"], FILTER_VALIDATE_URL)) {
            $lienAchatErr = "* Le lien doit être une URL valide";
        } else {
            $lienAchat = htmlspecialchars($_POST["lienAchat"]);
            $lienAchatErr = "*";
        }
    }

    // Si toutes les validations sont correctes
    if ($noteErr === '*' && $prixErr === '*' && $commentaireErr === '*' && $lienAchatErr === '*') {
        try {
            $idLoggedUser = $_SESSION['user_id'];

            // Préparation des données
            $data = [
                'note' => $note,
                'utilisateur_id' => $idLoggedUser,
                'article_id' => $productId,
                'prix' => $prix,
                'commentaire' => $commentaire,
                'lien_achat' => $lienAchat 
            ];

            // Insertion dans la base de données (table avis)
            $insertedId = insertData($pdo, 'avis', $data);

            if ($insertedId) {
                echo "Merci pour votre commentaire !";
                exit();
            } else {
                echo "Erreur lors de l'insertion.";
            }
        } catch (PDOException $e) {
            echo "Erreur lors de l'insertion : " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="css/ProductDetails.css">
    <link rel="stylesheet" href="css/navbar.css">
    <link rel="stylesheet" href="css/footer.css">
    <link href="https://fonts.googleapis.com/css2?family=League+Spartan:wght@100..900&display=swap" rel="stylesheet">
    <meta name="viewport" content="width=device-width,initial-scale=1.0,minimum-scale=1.0,maximum-scale=1.0">
    <title>CritiClick - Fil rouge</title>
</head>
<body>
    <?php include 'components/navbar.php'; ?>

    <section class="hero-product">
        <?php if (isset($produitSelectionne) && $produitSelectionne): ?>
            <div style="margin-top: 100px;" class='products-item'>
                <div class='products-item-info'>
                    <h2>Image</h2>
                    <img class='products-item-picture' src="<?= htmlspecialchars($produitSelectionne['image']) ?>" alt='Image du produit' class='product-image'>
                </div>

                <div class='products-item-info'>
                    <h2>Nom</h2>
                    <h2 class='products-item-name'><?= htmlspecialchars($produitSelectionne['nom']) ?></h2>
                </div>

                <div class='products-item-info'>
                    <h2>Description</h2>
                    <p class='products-item-text'><?= htmlspecialchars($produitSelectionne['description']) ?></p>
                </div>

                <div class='products-item-info'>
                    <h2>Tags</h2>
                    <div class='products-item-text'>
                        <?php foreach ($tags as $tag): ?>
                            <div><?= htmlspecialchars($tag['nom']) ?></div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </section>

    <section class="com">
        <?php if (!isset($_SESSION['username'])): ?>
            <div class="products-item">
                <div class="com-textarea">
                    Vous souhaitez poster un avis à propos de cet article ?
                    <a href="/criticlick/login.php">
                        <button>Se connecter</button>
                    </a>
                </div>
            </div>
        <?php else: ?>
            <form action="" class="send-com" method="post">
                <h1>Rédiger un commentaire</h1>
                <input type="hidden" name="action" value="insert">
                <input type="hidden" name="article_id" value="<?= htmlspecialchars($productId); ?>">

                <div class="form-option">
                    <label for="note">Note sur 5 :</label>
                    <?php if ($noteErr): ?>
                        <p class="errorMSG" style="color: red;"><?php echo $noteErr; ?></p>
                    <?php endif; ?>
                    <select name="note" id="note">
                        <option value="0">0</option>
                        <option value="1">1</option>
                        <option value="2">2</option>
                        <option value="3">3</option>
                        <option value="4">4</option>
                        <option value="5">5</option>
                    </select>
                </div>

                <div class="form-option">
                    <div class="form-option-upper">
                        <label for="prix">Prix à l'achat (en €) :</label>
                        <?php if ($prixErr): ?>
                            <p class="errorMSG" style="color: red;"><?php echo $prixErr; ?></p>
                        <?php endif; ?>
                    </div>
                    <input name="prix" id="prix" type="number" value="<?php echo $prix; ?>">
                </div>

                <div class="form-option">
                    <label for="commentaire">Commentaire :</label>
                    <?php if ($commentaireErr): ?>
                        <p class="errorMSG" style="color: red;"><?php echo $commentaireErr; ?></p>
                    <?php endif; ?>
                    <textarea name="commentaire" id="commentaire"><?php echo $commentaire; ?></textarea>
                </div>

                <div class="form-option">
                    <div class="form-option-upper">
                        <label for="lienAchat">Lien vers le site d'achat :</label>
                        <?php if ($lienAchatErr): ?>
                            <p class="errorMSG" style="color: red;"><?php echo $lienAchatErr; ?></p>
                        <?php endif; ?>
                    </div>
                    <input name="lienAchat" id="lienAchat" type="text" value="<?php echo $lienAchat; ?>">
                </div>

                <button class="form-option-button" type="submit">Envoyer</button>
            </form>
        <?php endif; ?>
    </section>


<?php

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); 

    if (isset($_GET['id'])) {
        $productId = intval($_GET['id']);

// Récupération des avis pour l'article
$avis = [];
if ($produitSelectionne) {
    $avisStmt = $pdo->prepare(
        "SELECT a.id, a.utilisateur_id, a.note, a.commentaire, a.lien_achat, a.prix, a.date_avis, u.username 
         FROM avis a 
         JOIN utilisateur u ON a.utilisateur_id = u.id 
         WHERE a.article_id = :id"
    );
    $avisStmt->bindParam(':id', $productId, PDO::PARAM_INT);
    $avisStmt->execute();
    $avis = $avisStmt->fetchAll(PDO::FETCH_ASSOC);
}
}
} catch (PDOException $e) {
    echo "<p>Erreur de connexion : " . htmlspecialchars($e->getMessage()) . "</p>";
}

?>

<section class="comDisplay">
    <div class="com-container">
        <h1>Espaces commentaires</h1>
        <?php if (!empty($avis)): ?>
            <?php foreach ($avis as $avisItem): ?>
                <div class="comment">

                <?php if ($_SESSION['user_id'] == $avisItem['utilisateur_id'] || $_SESSION['is_admin'] == 1): ?>
                    <form class='comment-delete' action="ProductAvisDelete.php" method="post">
                        <input type="hidden" name="comment_id" value="<?= htmlspecialchars($avisItem['id']); ?>">
                        <input type="hidden" name="article_id" value="<?= htmlspecialchars($productId); ?>">
                        
                        <button type="submit" name="delete_comment">Supprimer</button>
                    </form>
                <?php endif; ?>
                    <div class="comment-subwrapper">
                        <p class="comment-username"><?= htmlspecialchars($avisItem['username']); ?></p>
                        <p>
                            <?php 
                            $note = intval($avisItem['note']);
                            for ($i = 0; $i < 5; $i++): 
                                if ($i < $note): ?>
                                    <span class="material-symbols-outlined" style="color: #FFC107;">grade</span>
                                    <?php else: ?>
                                        <span class="material-symbols-outlined" style="color: lightgray;">grade</span>
                                        <?php endif; 
                            endfor; 
                            ?>
                        </p>
                    </div>

                    <p><strong>Acheté à </strong> <?= htmlspecialchars($avisItem['prix']); ?> €</p>
                    
                    <p><strong>Commentaire :</strong> <?= htmlspecialchars($avisItem['commentaire']); ?></p>
                    <?php if (!empty($avisItem['lien_achat'])): ?>
                        <a href="<?= htmlspecialchars($avisItem['lien_achat']); ?>" target="_blank">
                            <button class="lien_achat">Voir</button>
                        </a>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>Aucun commentaire pour cet article.</p>
        <?php endif; ?>
    </div>
</section>

    <?php include 'components/footer.php'; ?>
</body>
</html>