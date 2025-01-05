<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: /criticlick/login.php');
    exit();
}

if ($_SESSION['is_admin'] == 0) {
    header('Location: /criticlick/login.php');
    exit();
}

$dsn = 'mysql:host=localhost;charset=utf8';
$username = 'root'; 
$password = ''; 

try {
    // Connexion à MySQL sans base de données spécifique pour pouvoir la créer/supprimer
    $pdo = new PDO($dsn, $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // configure l’attribut d’erreur de PDO pour lancer des exceptions en cas d’erreurs, facilite le débogage

    // Fonction pour exécuter un fichier SQL
    function executeSQLFile($pdo, $filePath) {
        // Lecture du fichier SQL
        $sql = file_get_contents($filePath);
        if ($sql === false) {
            die("Erreur lors de la lecture du fichier SQL.");
        }

        // Exécution des requêtes SQL
        $pdo->exec($sql);
    }

    // Chemin vers le fichier SQL
    $sqlFilePath = "./CreateBDD.sql"; 

    // Exécute le fichier SQL pour initialiser la base de données
    executeSQLFile($pdo, $sqlFilePath);

    echo "Base de données réinitialisée avec succès.<br>";
    

    // Fonction pour récupérer les données avec cURL
   function fetchDataWithCurl($url) {
        $ch = curl_init(); 

        curl_setopt($ch, CURLOPT_URL, $url); 
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); 
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true); 
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            echo 'Erreur cURL : ' . curl_error($ch);
            return null;
        }

        curl_close($ch); 

        return json_decode($response, true); // Retourner les données décodées (JSON -> tableau PHP)
    }


    // URL des API
    $produitsApiUrl = "https://filrouge.uha4point0.fr/V2/browseShop/produits";
    $categoriesApiUrl = "https://filrouge.uha4point0.fr/V2/browseShop/categories";

    $produits = fetchDataWithCurl($produitsApiUrl);
    $categories = fetchDataWithCurl($categoriesApiUrl);




    // Fonction pour insérer les données dans la base de données
    function insertData($pdo, $table, $data) {
        $columns = implode(", ", array_keys($data));
        $placeholders = ":" . implode(", :", array_keys($data));

        $sql = "INSERT INTO $table ($columns) VALUES ($placeholders)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($data);
    }

    insertData($pdo, 'utilisateur', data: [
        'username' => 'Administrateur',
        'email' => 'admin@criticlick.com',
        'password' => password_hash('mdpadmin', PASSWORD_DEFAULT),
        'is_admin' => '1',
    ]);

    foreach ($categories as $categorie) {
        insertData($pdo, 'categorie', [
            'nom' => htmlspecialchars($categorie['nom'], ENT_QUOTES, 'UTF-8'),
            'utilisateur_id' => '1',
            'description' => htmlspecialchars($categorie['description'], ENT_QUOTES, 'UTF-8'),
            'filtreTag' => htmlspecialchars($categorie['filtreTag'], ENT_QUOTES, 'UTF-8'),
            'published' => '1',
            'nbrProduits' => htmlspecialchars($categorie['nbrProduits'], ENT_QUOTES, 'UTF-8'),
        ]);
    }
    
    // Récupérer tous les tags existants
    $stmt = $pdo->query("SELECT id, nom FROM tag");
    $existingTags = $stmt->fetchAll(PDO::FETCH_KEY_PAIR); // Renvoie un tableau associatif avec 'nom' comme clé et 'id' comme valeur

    foreach ($produits as $produit) {
        // Insertion de l'article
        insertData($pdo, 'article', [
            'nom' => htmlspecialchars($produit['nom'], ENT_QUOTES, 'UTF-8'),
            'utilisateur_id' => '1',
            'description' => htmlspecialchars($produit['description'], ENT_QUOTES, 'UTF-8'),
            'categorie_id' => htmlspecialchars($produit['categorie'], ENT_QUOTES, 'UTF-8'),
            'image' => htmlspecialchars($produit['image'], ENT_QUOTES, 'UTF-8'),
            'published' => '1',
        ]);

        // Récupérer l'ID de l'article inséré
        $articleId = $pdo->lastInsertId();

        // Vérifier si le produit contient des tags
        if (!empty($produit['tag']) && is_array($produit['tag'])) {
            foreach ($produit['tag'] as $tag) {
                $tag = htmlspecialchars($tag, ENT_QUOTES, 'UTF-8'); // Nettoyage du tag
                
                // Vérifier si le tag existe déjà dans le tableau des tags récupérés
                if (!isset($existingTags[$tag])) {
                    // Si le tag n'existe pas, on l'insère
                    insertData($pdo, 'tag', ['nom' => $tag]);
                    $tagId = $pdo->lastInsertId();
                    $existingTags[$tag] = $tagId; // Ajouter le nouveau tag dans le tableau
                } else {
                    $tagId = $existingTags[$tag]; // Récupérer l'ID du tag existant
                }

                // Insérer la relation dans la table article_tag
                insertData($pdo, 'article_tag', [
                    'article_id' => $articleId,
                    'tag_id' => $tagId
                ]);
            }
        }
    }

    $avisJson = file_get_contents('../apiAvis.json');
    $avis = json_decode($avisJson, true);

    foreach ($avis as $avisItem) {
        insertData($pdo, 'avis', [
            'utilisateur_id' => $avisItem['utilisateur_id'],
            'article_id' => $avisItem['article_id'],
            'note' => $avisItem['note'],
            'commentaire' => htmlspecialchars($avisItem['commentaire'], ENT_QUOTES, 'UTF-8'),
            'lien_achat' => htmlspecialchars($avisItem['lien_achat'], ENT_QUOTES, 'UTF-8'),
            'prix' => $avisItem['prix'],
        ]);
    }

    session_destroy();

    echo "
    <script>
        alert('BDD réinitialisé avec succès, merci de vous connecter à nouveau.');
        window.location.href = '/criticlick/index.php'; // Redirige vers la page d'accueil
    </script>";

} catch (PDOException $e) {
    die("Erreur lors de la connexion ou de l'exécution : " . $e->getMessage());
}
?>