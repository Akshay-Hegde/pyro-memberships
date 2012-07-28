#!/bin/sh
phpdoc run -d . --ignore view/,assets/ -t /var/www/docs.homestead.dk/pyrocms/teams
