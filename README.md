---
layout: post
title:  "Second Projet: Matcha!"
date:   2018-06-07 20:7:20 +0200
categories: website
excerpt_separator: <!--more-->
img: /assets/matcha1.png
---
## Description

Basé sur le micro-framework slim3, ceci est un site de rencontres sur le theme rick&morty. Les interactions entre utilisateurs sont au coeur du projet !
<!--more-->

![screenshot](/assets/matcha1.png)

## lien

- [site matcha](https://www.jgroc-de.me/)
- [code final sur github](https://github.com/jgroc-de/matcha)
- L'envoi de mails a été désactivé: votre compte sera donc activé même si votre adresse mail est bidon (ce que j'encourage ici).

## features

- page de login/signup/reset password
- page de profil pour chaque utilisateur. Sur sa propre page, on peut ajouter tag/photos et modifier sa localisation. Celle-ci est illustrée via l'api google map
- page de recherche permettant soit de browser une sélection de profils (sélectionner par date de connection, distance à vous, tag en commun, popularité), soit defaire des recherches par nom ou critères.
- page de chat permettant de chatter avec ses amis uniquement
- page de configuration: modification des données personnelles, mot de passe, demande des données stockées en base de donnée et suppression deprofil
- page de logout
- page de contact
- système de websocket pour délivrer des notifications quand:
    - un autre utilisateur regarde votre profil
    - un uilisateur fait ou accepte une demande d'amitié
    - un utilisateur consulté est en ligne
    - un ami est en ligne
    - un ami a démarré une discussion
    - chat

![screenshot](/assets/matcha3.png)

## Objectifs pédagogique

- [x] *Micro-framework*
- [x] Comptes utilisateur avancés
- [x] Web socket
- [x] Géolocalisation
- [x] Sécurité / Validation de données 

## Langages:

| | Back-end | Front-end | bdd |
| :---: | :--- | :--- | :--- |
| langage | PHP | HTML, CSS, JS | MySQL |
| framework | [Slim3](https://www.slimframework.com/) | [twig](https://twig.symfony.com/), [w3.css](https://www.w3schools.com/w3css/), [VanillaJS](http://vanilla-js.com/) ||

## Librairies:

| categorie | bibliotheque/api |
| :---: | :--- |
| websocket | [Ratchet](http://socketo.me/) avec [zeroMQ](http://zeromq.org/) |
| geolocalisation | [geoip2](https://www.maxmind.com/fr/geoip-demo) |
| map | API Google Maps |
| faker | [fzaninotto/faker](https://github.com/fzaninotto/Faker) |

![screenshot](/assets/matcha2.png)

## Contraintes Techniques, composants interdits:

- ORM ou ODM
- validateur de données
- gestion de comptes utilisateurs
- gestion de votre base de données
