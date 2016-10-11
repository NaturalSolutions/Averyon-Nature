# Aveyron-Nature-Web
La plateforme web de l'Aveyron-Nature (Drupal 8)

- Le répertoire "themes" contient le(s) themes versionné
- Le répertoire "updates" contient toutes les instructions
- Le répertoire "modules" contient les modules personalisé.

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



