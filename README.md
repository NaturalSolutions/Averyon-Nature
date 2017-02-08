# La plateforme web de l'Aveyron-Nature (Drupal 8)
![Aveyron Nature](https://raw.githubusercontent.com/gitkyo/Aveyron-Nature-Web/master/themes/bootstrap_aveyron/images/aveyron.gif  "Aveyron Nature")

- Le répertoire "themes" contient le(s) themes versionné
- Le répertoire "doc" contient toutes les instructions
- Le répertoire "modules" contient les modules personalisé.
- Le répertoire "ressources" contient les exports de configuration

# Liste des modules utilisé (www/modules)

[admin_toolbar 8.x-1.17](https://ftp.drupal.org/files/projects/admin_toolbar-8.x-1.17.zip)  
[backup_migrate 8.x-4.0-alpha1](https://ftp.drupal.org/files/projects/backup_migrate-8.x-4.0-alpha1.zip)  
[captcha 8.x-1.0-alpha1](https://ftp.drupal.org/files/projects/captcha-8.x-1.0-alpha1.zip)  
[devel 8.x-1.0-alpha1](https://ftp.drupal.org/files/projects/devel-8.x-1.0-alpha1.zip)  
[geolocation 8.x-1.8](https://ftp.drupal.org/files/projects/geolocation-8.x-1.8.zip)  
[honeypot 8.x-1.23](https://ftp.drupal.org/files/projects/honeypot-8.x-1.23.zip)  
[metatag 8.x-1.0-beta11](https://ftp.drupal.org/files/projects/metatag-8.x-1.0-beta11.zip)  
[recaptcha 8.x-2.2](https://ftp.drupal.org/files/projects/recaptcha-8.x-2.2.zip)  
[xmlsitemap 8.x-1.0-alpha2](https://ftp.drupal.org/files/projects/xmlsitemap-8.x-1.0-alpha2.zip)  
[Geofield 8.x-1.0-alpha2](https://ftp.drupal.org/files/projects/geofield-8.x-1.0-alpha2.zip)

## Installation de la dépendance geoPhp pour le module geofield
Le tag --ignore-platform-reqs permet d'ignorer la version de php.
>composer require "phayes/geophp" --ignore-platform-reqs


# Démo
[http://151.80.132.63/aveyron-demo/](http://151.80.132.63/aveyron-demo/)

# Documentation
[Documentation officiel D8](https://www.drupal.org/docs/8)  
[Convention de nommage](https://www.drupal.org/node/318)  
[Drupal Bootstrap Documentation](http://drupal-bootstrap.org/api/bootstrap)  

# Debug tips

   - Voir les log apache : tail -f /var/log/apache2/error.log
   - Augmenter "memory_limit" dans le php.ini
   - Example de phpinfo.php

'''
<?php

// Affiche toutes les informations, comme le ferait INFO_ALL
phpinfo();

// Affiche uniquement le module d'information.
// phpinfo(8) fournirait les mêmes informations.
phpinfo(INFO_MODULES);

?>
'''
