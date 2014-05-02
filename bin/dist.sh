#!/bin/sh

mkdir -p ./dist/classes
mkdir -p ./dist/lib

cp -p ./src/*.class.php ./dist/classes/
echo "php ./bin/libify.php"
php ./bin/libify.php
