# Aveyron-Nature-Web
La plateforme web de l'Aveyron-Nature (Drupal 8)

- Le répertoire "themes" contient le(s) themes versionné
- Le répertoire "doc" contient toutes les instructions
- Le répertoire "modules" contient les modules personalisé.
- Le répertoire "ressources" contient les exports de configuration

# Liste des modules utilisé (www/modules)

admin_toolbar 8.x-1.17
backup_migrate 8.x-4.0-alpha1
captcha 8.x-1.0-alpha1
devel 8.x-1.0-alpha1
geolocation 8.x-1.8
honeypot 8.x-1.23
metatag 8.x-1.0-beta11
recaptcha 8.x-2.2
xmlsitemap 8.x-1.0-alpha2

# Documentation 
https://www.drupal.org/docs/8

  - Convention de nommage : https://www.drupal.org/node/318

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



