#!/bin/bash

. bin/config.inc

ANNEE=$1
FILE_TIERS=data/import/Tiers/Tiers-last

if ! test "$ANNEE"; then
echo "L'année de déclaration de récolte est requise"
exit;
fi



if [ ! -e $FILE_TIERS ]; then
echo "Le fichier $FILE_TIERS n'existe pas"
exit;
fi

echo "CVI;TELEPHONE;NOM;COOP;TYPE;EMAIL"

cat $FILE_TIERS | awk -F ',' '{ if($36 == "\"O\"") { next; } print $24 ";" $58 ";" $38 ";" $7 ";" $12 ";" $18 ":" $41 }' | sed 's/"//g' | grep "^O;" | cut -d ";" -f 2,3,4,5,6,7 | grep -E "^6" | sed -r 's/[ ]+/ /' | while read ligne; do CVI=$(echo $ligne | cut -d ";" -f 1); curl -s $COUCHDBDOMAIN:$COUCHDBPORT/$COUCHDBBASE/DR-$CVI-$ANNEE | grep "not_found" | sed "s|^|$ligne|" | cut -d "{" -f 1; done
