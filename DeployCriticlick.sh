#!/bin/bash

# Variables
PROJECT_DIR="/opt/lampp/htdocs/criticlick"  # Répertoire de ton projet dans XAMPP
REPO_URL="https://github.com/Nolhan-B/FilRouge-401.git"
XAMPP_LOCALHOST_URL="http://localhost/"  # URL locale de ton site via XAMPP
XAMPP_CMD="/opt/lampp/lampp"  # Commande pour gérer XAMPP
XAMPP_DOWNLOAD_URL="https://www.apachefriends.org/fr/download.html"  # URL de téléchargement de XAMPP
PHP_INIT_FILE="SQL/init.php"  # Ton fichier PHP pour initialiser la base de données

# Fonction pour afficher un message de succès ou d'erreur
function notify {
  if [ $? -eq 0 ]; then
    echo "Le site a bien été déployé."
    echo "Accédez à votre site ici : $XAMPP_LOCALHOST_URL"
  else
    echo "Une erreur est survenue lors du déploiement."
    exit 1
  fi
}

# Vérification si XAMPP est installé
if [ ! -d "/opt/lampp" ]; then
  echo "XAMPP n'est pas installé sur votre système."
  echo "Téléchargez XAMPP depuis $XAMPP_DOWNLOAD_URL"
  xdg-open $XAMPP_DOWNLOAD_URL
  exit 1  # Quitte le script, car XAMPP est nécessaire
fi

# Lancement des serveurs XAMPP
echo "Démarrage des serveurs XAMPP..."
sudo "$XAMPP_CMD" start  # Démarre Apache et MySQL
sleep 5  # Attends que les serveurs démarrent correctement

# Vérification si le dossier criticlick existe déjà
if [ ! -d "$PROJECT_DIR" ]; then
  echo "Le dossier 'criticlick' n'existe pas, création du dossier..."
  sudo mkdir -p "$PROJECT_DIR"  # Crée le dossier criticlick
fi

# Navigation dans le dossier du projet
cd "$PROJECT_DIR" || exit

# Clonage ou mise à jour du projet depuis GitLab
if [ -d ".git" ]; then
  echo "Le projet existe déjà. Mise à jour..."
  sudo git pull origin main  # Met à jour le projet depuis GitLab
else
  echo "Téléchargement du projet depuis GitLab..."
  sudo git clone $REPO_URL .  # Télécharge le projet dans le dossier actuel
  notify
fi

# Lancer le fichier PHP d'initialisation de la base de données
if [ -f "$PHP_INIT_FILE" ]; then
  echo "Initialisation de la base de données via $PHP_INIT_FILE..."
  php "$PHP_INIT_FILE"  # Exécute le fichier PHP pour initialiser la base de données
  notify
else
  echo "Le fichier d'initialisation $PHP_INIT_FILE est introuvable."
  exit 1
fi

# Lancement du site avec XAMPP
echo "Votre site est en cours de déploiement via XAMPP..."
xdg-open $XAMPP_LOCALHOST_URL  # Ouvre l'URL dans le navigateur

echo "Appuyez sur [Entrée] pour arrêter les serveurs XAMPP et quitter."
read -r 
sudo "$XAMPP_CMD" stop  # Arrête les serveurs XAMPP
echo "Script terminé. Serveurs XAMPP arrêtés."