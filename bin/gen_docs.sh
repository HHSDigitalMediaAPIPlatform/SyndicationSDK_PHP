#!/bin/sh

./vendor/phpdocumentor/phpdocumentor/bin/phpdoc.php -d ./src/ -t ./docs/ --template='responsive-twig'
