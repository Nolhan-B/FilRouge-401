<?php session_start();
// Connexion à la base de données
$dsn = 'mysql:host=localhost;dbname=criticlick;charset=utf8';
$username = 'db_user';
$password = 'rootmdp';

try {
    $pdo = new PDO($dsn, $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion à la base de données: " . $e->getMessage());
}

// Vérifier si le formulaire a été soumis
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Récupérer les données du formulaire
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Vérifier si l'utilisateur existe déjà
    $stmt = $pdo->prepare("SELECT * FROM utilisateur WHERE email = :email");
    $stmt->execute(['email' => $email]);
    if ($stmt->rowCount() > 0) {
        echo "Un utilisateur avec cet email existe déjà.";
    } else {
        // Hacher le mot de passe pour des raisons de sécurité
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Insérer l'utilisateur dans la base de données
        $stmt = $pdo->prepare("INSERT INTO utilisateur (username, email, password) 
                               VALUES (:username, :email, :password)");
        $stmt->execute([
            'username' => $username,
            'email' => $email,
            'password' => $hashed_password
        ]);

        echo "Compte créé avec succès !";
        // Redirection vers la page de connexion ou autre
        header("Location: /criticlick/login.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
	<link rel="stylesheet" href="css/register.css">
    <link rel="stylesheet" href="css/navbar.css">
    <link rel="stylesheet" href="css/footer.css">
    
    <link href="https://fonts.googleapis.com/css2?family=League+Spartan:wght@100..900&display=swap" rel="stylesheet">

	<meta name="viewport" content="width=device-width,initial-scale=1.0,minimum-scale=1.0,maximum-scale=1.0">
    <title>CritiClick - Fil rouge</title>
</head>
<body>
<?php include 'components/navbar.php'; ?>
	<div class="container">
		<div class="contact-box">
			<div class="left"></div>
			<form class="right" action="register.php" method="POST">
				<h2>Créer votre compte</h2>
				<input type="text" name="username" required class="field" placeholder="Nom d'utilisateur">
				<input type="email" name="email" required class="field" placeholder="exemple@email.fr">
				<input type="password" name="password" required class="field" placeholder="Mot de passe">
				<button type="submit" class="btn">S'enregistrer</button>
				<p>Déjà un compte ? <a href="/criticlick/login.php">Connectez vous</a> dès maintenant.</p>
			</form>
		</div>
	</div>
<?php include 'components/footer.php'; ?>
</body>
</html>