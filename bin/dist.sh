#!/bin/sh

cp -p ./src/*.class.php ./dist/classes/
php ./bin/libify.php
