#!/bin/bash

echo "Script désactivé, il faut passer par l'interface web"
exit;

. $(echo "$0" | sed 's/[^\/]*$//')config.inc

if [ ! -f "$1" ]
then
    echo "Le fichier TIECPT $1 n'existe pas ou doit être spécifier en 1er argument"
    exit 0
fi

cut "$BASEDIR/$PATH_MISEADISPO_CIVA/export/bi/export_bi_societes.utf8.csv" -d ";" -f 4,6 | sed 's/"//g' | awk -F ';' '{ print "s/^" $2 ",/" $1 ",/" }' > /tmp/tiers_2_societe.sed

sed -f /tmp/tiers_2_societe.sed "$1" > /tmp/TIECPT.tmp

cd "$GIILDA_BASEDIR"
    php symfony import:MandatSepa /tmp/TIECPT.tmp --application=civa
cd -
