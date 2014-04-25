#!/bin/sh

mkdir -p ./dist/classes
mkdir -p ./dist/lib

cp -p ./src/*.class.php ./dist/classes/
php ./bin/libify.php
