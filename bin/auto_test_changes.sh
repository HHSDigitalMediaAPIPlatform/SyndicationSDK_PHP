#!/bin/sh
fswatch src/:tests/ '
echo -n -e "\033]0; ? Test \007"; 
T=$(phpunit tests/LiveTest);
grep --quiet FAILURE <<< $T;
F="${?}";
grep --quiet "Fatal Error" <<< $T;
E="${?}"
if [ $F -eq "0" ] || [ $E -eq "0" ]; then 
    echo -n -e "\033]0; 💩  Fail \007"; 
else 
    echo -n -e "\033]0; 👍  Pass \007"; 
    ./vendor/phpdocumentor/phpdocumentor/bin/phpdoc.php -d ./src/ -t ./docs/ --template="responsive-twig";
fi;
echo -e "$T";
';
#bin/push_to_drupal_module.sh;
#if [ -n $(grep --quiet FAILURE <<< $T) ] || [ -n $(grep --quiet "Fatal Error" <<< $T) ]; then 
#fswatch src/ "composer install"
#    echo -n -e "\033]0; ✗ Fail \007"; 
#    echo -n -e "\033]0; ✓ Pass \007"; 
