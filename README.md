# Nolhan.BILYJ_browseShop

Bonjour et bienvenue dans mon projet Criticlick.
Ce projet à été réalisé dans le cadre de ma première année d'étude en Diplôme Universitaire d'Informatique à l'UHA 4.0 de Mulhouse.
Ce projet nous à été donnée à après notre arrivé à l'école pour nous permettre d'acquérir des compétences tels que :
    - Les languages : HTML / CSS / JavaScript / PHP / SQL / Bash (Script de déploiement du projet)
    - L'utilisation de serveur local (MAMP / XAMPP)
    - La mobilisation d'API
    - L'utilisation de Git



Sommaire

	•	Prérequis
	•	Déploiement du Projet
	•	Utilisation du site Web
	•	Page d’Administration et Modération

Prérequis

Avant de déployer le projet, assurez-vous que les éléments suivants sont installés sur votre machine :

	•	XAMPP : Une plateforme de développement PHP qui inclut Apache, MySQL et PHP. Vous pouvez le télécharger à partir de ce lien.
	•	Git : Un système de contrôle de version pour cloner le projet depuis le dépôt GitLab.
	•	PHP : Assurez-vous que PHP est installé et accessible via la ligne de commande.
	•	Ubuntu : Assurez-vous que la machine sur laquelle vous vous trouvez est équipée de cet OS.

Déploiement du projet

	1.	Téléchargez le script de déploiement :
	    •	Télécharger DeployCriticlick.sh.
	2.	Rendez le script exécutable :
	    •	Ouvrez un terminal et utilisez cd [Chemin vers le fichier] pour vous rendre dans le dossier où se trouve le script de déploiement.
	    •	Exécutez la commande suivante : chmod +x DeployCriticlick.sh.
	3.	Lancez le script :
	    •	Utilisez la commande ./DeployCriticlick.sh.

Ce script effectuera les actions suivantes :

	•	Vérifier si XAMPP est installé.
	•	Démarrer les serveurs Apache et MySQL de XAMPP.
	•	Créer le dossier du projet s’il n’existe pas déjà.
	•	Cloner le projet depuis le dépôt GitLab ou mettre à jour le projet s’il existe déjà.
	•	Lancer un fichier PHP pour initialiser la base de données et remplir les données depuis l’API de l’UHA.
	•	Ouvrir votre site dans un navigateur à l’adresse http://localhost/criticlick/index.php.

Utilisation du site Web

Une fois le site déployé, voici comment naviguer parmi les fonctionnalités principales :

	1.	Navbar & Footer : Pour naviguer entre les pages principales. La Navbar permet également, une fois connecté, d’accéder à la création d’une fiche produit et d’une catégorie.
	2.	Page d’accueil : Affiche les aspects principaux du site, y compris les catégories présentes, pour accéder aux fiches produits concernées.
	3.	Page de contact : Contient un formulaire permettant aux utilisateurs de contacter l’administrateur du site par e-mail.
	4.	Page À propos : Présente des informations générales sur le site et explique aux utilisateurs le fonctionnement du site et son but en quelques étapes simples.
	5.	Page fiches produits : Permet aux utilisateurs de voir tous les produits avec des options de tri par catégorie et une barre de recherche pour trouver un produit par nom, ainsi qu’une pagination pour ne pas charger trop d’éléments à la fois.
	6.	Page détails d’une fiche produit : Affiche dynamiquement les informations d’un produit sélectionné et permet éventuellement de laisser un commentaire.
	7.	Page de connexion/création de compte : Permet aux utilisateurs de créer un compte ou de se connecter.
	8.	Page de création d’article : Accessible uniquement pour les utilisateurs connectés, permet de créer une fiche produit avec les informations suivantes :
        •	Nom du produit
        •	Description du produit
        •	URL d’une image du produit
        •	Avis de l’utilisateur
        •	Site d’achat du produit
        •	Prix d’achat du produit

Page d’Administration et Modération

L’administrateur dispose d’une interface dédiée pour :

	•	Valider ou rejeter les fiches produits et catégories avant leur publication publique.
	•	Gérer les catégories des produits et les fiches produits elles-mêmes.
	•	Supprimer ou modifier les fiches et catégories existantes en fonction des politiques du site.
	•	Réinitialiser la base de données pour remettre le site et ses informations à leur état initial.

Pour vous connecter en tant qu’administrateur, voici les informations nécessaires :

	•	Username : Administrateur
	•	Email : admin@criticlick.com
	•	MDP : mdpadmin
 
