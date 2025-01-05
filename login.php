<?php
// Démarrer une session
session_start();

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
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Vérifier si l'utilisateur existe
    $stmt = $pdo->prepare("SELECT * FROM utilisateur WHERE email = :email");
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Vérifier que l'utilisateur existe et que le mot de passe est correct
    if ($user && password_verify($password, $user['password'])) {
        // Stocker les informations de l'utilisateur dans la session
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['is_admin'] = $user['is_admin'];

        echo "Connexion réussie !";
        // Redirection vers la page d'accueil ou tableau de bord
        header("Location: index.php");
        exit();
    } else {
        echo "Email ou mot de passe incorrect.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
	<link rel="stylesheet" href="css/login.css">
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
			<form class="right" action="login.php" method="POST">
				<h2>Se connecter</h2>

				<?php if (isset($_SESSION['username'])): ?>
					<p>Bienvenue, <?php echo htmlspecialchars($_SESSION['username']); ?> !</p>
				<?php endif; ?>
				
				<input type="email" name="email" required class="field" placeholder="exemple@email.fr">
				<input type="password" name="password" required class="field" placeholder="Mot de passe">
				<button type="submit" class="btn">Se connecter</button>
				<p>Pas encore de compte ? <a href="register.php">Créez votre compte</a> dès maintenant.</p>
			</form>
		</div>
	</div>
<?php include 'components/footer.php'; ?>
</body>
</html>

