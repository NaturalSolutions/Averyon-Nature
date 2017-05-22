# La plateforme web de l'Aveyron-Nature (Drupal 8)
![Aveyron Nature](https://raw.githubusercontent.com/gitkyo/Aveyron-Nature-Web/master/themes/bootstrap_aveyron/images/aveyron.gif  "Aveyron Nature")

- Le répertoire "doc" contient toutes les instructions (configuration, instllation, gestion de la base...)
- Le répertoire "modules/custom" contient les modules personalisé.

## Installation de la dépendance geoPhp pour le module geofield
Le tag --ignore-platform-reqs permet d'ignorer la version de php.  

>composer require "phayes/geophp" --ignore-platform-reqs  

Executer cette commande à la racine du projet lors d'une nouvelle installation ou une mise à jour du coeur  

# Démo
[http://151.80.132.63/aveyron-demo/](http://151.80.132.63/aveyron-demo/)

# Documentation
[Documentation officiel D8](https://www.drupal.org/docs/8)
[Convention de nommage](https://www.drupal.org/node/318)
[Drupal Bootstrap Documentation](http://drupal-bootstrap.org/api/bootstrap)

## Fond de carte IGN
Pré-requis :
- Installer le module contrib Geofield et Leaflet
- Installer le module custom layer_leaflet

Créer un affichage de type map :
- Ajouter un champ de type geofield
- Dans "Gérer l'affichage" du champ, choisissez le format "Leaflet Map" puis sélectionner dans les paramètres le type de "Carte Leaflet" IGN GRIDSYSTEM.

# Some tips

	- Voir les log apache : tail -f /var/log/apache2/error.log
	- Afficher le contenu d'une variable dans twig : <pre>{{  dump(infoFold5) }}</pre>
	- Charger une image avec un style prédéfinit depuis un uri : $myPicture = entity_load('image_style', '[nom_machine_du_style]')->buildUrl([uri_image]);
	- Requette simple : $query = db_query("[ma_requette_sql]]"); $titleFold4 = $query->fetchAll();
