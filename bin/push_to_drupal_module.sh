#!/bin/bash

cp src/*.class.php  ~/Sites/drupal-synd/sites/all/modules/syndicated_content/lib/
cp -rp docs         ~/Sites/drupal-synd/sites/all/modules/syndicated_content/lib/docs
echo "Syndication API moved into drupal module (clobber-mode)"
