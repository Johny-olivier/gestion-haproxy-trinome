# Gestion HAProxy

## Prérequis

- PHP 8.1 ou supérieur
- Composer
- Git

## 1. Récupérer le projet

    git clone https://github.com/Johny-olivier/gestion-haproxy-trinome
    cd gestion-haproxy-trinome

## 2. Installer les dépendances

    composer install

## 3. Démarrer l'application

Depuis la racine du projet :

    php -S 0.0.0.0:8080 -t public public/index.php

Ouvrir dans le navigateur :

http://localhost:8080

Pour accéder depuis un autre ordinateur du même réseau :

http://ADRESSE_IP_DU_PC:8080