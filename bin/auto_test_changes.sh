#!/bin/sh
fswatch src/:tests/ '
echo -n -e "\033]0; ? Test \007"; 
T=$(phpunit tests/);
if [ $(grep --quiet FAILURE <<< $T) ] || [ $(grep --quiet Error <<< $T) ]; then 
    echo -n -e "\033]0; 💩  Fail \007"; 
else 
    echo -n -e "\033]0; 👍  Pass \007"; 
fi;
echo -e "$T";
';
#fswatch src/ "composer install"
#    echo -n -e "\033]0; ✗ Fail \007"; 
#    echo -n -e "\033]0; ✓ Pass \007"; 
