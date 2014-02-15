#!/bin/sh
fswatch src/:tests:/ "phpunit tests"
#fswatch src/ "composer install"
