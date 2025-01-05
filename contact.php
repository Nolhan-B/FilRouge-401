<?php session_start(); ?>
<!DOCTYPE html>
<html>
<head>
	<link rel="stylesheet" href="css/contact.css">
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
			<form class="right">
				<h2>Contactez nous</h2>
				<input type="text" required class="field" placeholder="NOM Prénom">
				<input type="text" required class="field" placeholder="exemple@email.fr">
				<input type="text" required class="field" placeholder="Sujet">
				<textarea required placeholder="Message" class="field"></textarea>
				<button type="submit" class="btn">Envoyer</button>
			</form>
		</div>
	</div>

<?php include 'components/footer.php'; ?>
</body>
</html>