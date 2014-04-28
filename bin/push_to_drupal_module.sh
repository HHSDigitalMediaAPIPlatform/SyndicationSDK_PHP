#!/bin/bash

cp dist/lib/*.lib.php  ~/Sites/drupal-synd/sites/all/modules/syndicated_content/lib/
#cp -r docs             ~/Sites/drupal-synd/sites/all/modules/syndicated_content/lib/docs
#chmod -R 775           ~/Sites/drupal-synd/sites/all/modules/syndicated_content/lib/docs
echo "Syndication API moved into drupal module (clobber-mode)"
