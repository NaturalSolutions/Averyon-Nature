# La plateforme web de l'Aveyron-Nature (Drupal 8.2.7)
![Aveyron Nature](https://raw.githubusercontent.com/gitkyo/Aveyron-Nature-Web/master/themes/bootstrap_aveyron/images/aveyron.gif  "Aveyron Nature")

- Le répertoire "themes" contient le(s) themes versionné
- Le répertoire "doc" contient toutes les instructions (configuration, instllation, gestion de la base...)
- Le répertoire "modules" contient les modules personalisé.
- Le répertoire "ressources" contient les exports de configuration

# Liste des modules utilisé (www/modules)

[admin_toolbar 8.x-1.18](https://ftp.drupal.org/files/projects/admin_toolbar-8.x-1.18.zip)  
[backup_migrate 8.x-4.0-alpha1](https://ftp.drupal.org/files/projects/backup_migrate-8.x-4.0-alpha1.zip)  
[captcha 8.x-1.0-beta1](https://ftp.drupal.org/files/projects/captcha-8.x-1.0-beta1.zip)  
[devel 8.x-1.0-rc1](https://ftp.drupal.org/files/projects/devel-8.x-1.0-rc1.zip)  
[geolocation 8.x-1.10](https://ftp.drupal.org/files/projects/geolocation-8.x-1.10.zip)  
[honeypot 8.x-1.23](https://ftp.drupal.org/files/projects/honeypot-8.x-1.23.zip)  
[xmlsitemap 8.x-1.0-alpha2](https://ftp.drupal.org/files/projects/xmlsitemap-8.x-1.0-alpha2.zip)  
[Geofield 8.x-1.0-alpha2](https://ftp.drupal.org/files/projects/geofield-8.x-1.0-alpha2.zip)  
[Leaflet 8.x-1.0-beta1](https://ftp.drupal.org/files/projects/leaflet-8.x-1.0-beta1.zip)


## Installation de la dépendance geoPhp pour le module geofield
Le tag --ignore-platform-reqs permet d'ignorer la version de php.
>composer require "phayes/geophp" --ignore-platform-reqs
Executer cette commande à la racine du projet

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
