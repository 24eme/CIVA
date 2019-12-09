#!/bin/bash

. bin/config.inc

ANNEE=$1
FILE_TIERS=data/import/Tiers/Tiers-last
FILE_RECOLTANTSANSDR=/tmp/$(date +%Y%m%d%H%M%S)_export_recoltant_sans_dr.csv
FILE_ETABLISSEMENTINFO=/tmp/$(date +%Y%m%d%H%M%S)_export_etablissement_info.csv

if ! test "$ANNEE"; then
echo "L'année de déclaration de récolte est requise"
exit;
fi

if [ ! -e $FILE_TIERS ]; then
echo "Le fichier $FILE_TIERS n'existe pas"
exit;
fi


cat $FILE_TIERS | awk -F ',' '{ if($36 == "\"O\"") { next; } print $24 ";" $58 ";" $38 ";" $7 ";" $12 ";" $18 ";" $41 }' | sed 's/"//g' | grep "^O;" | cut -d ";" -f 2,3,4,5,6,7 | grep -E "^6" | sed -r 's/[ ]+/ /' | while read ligne; do CVI=$(echo $ligne | cut -d ";" -f 1); curl -s $COUCHDBDOMAIN:$COUCHDBPORT/$COUCHDBBASE/DR-$CVI-$ANNEE | grep "not_found" | sed "s|^|$ligne|" | cut -d "{" -f 1; done | sort -t ";" -k 1,1 > $FILE_RECOLTANTSANSDR

curl -s "$COUCHDBDOMAIN:$COUCHDBPORT/$COUCHDBBASE/_design/etablissement/_view/all?reduce=false" | cut -d "," -f 9,17,18 | sed 's/"//g' | sed 's/,null/,/g' | grep -E '^[0-9]+' | sed 's/,/;/g' | sort -t ";" -k 1,1 > $FILE_ETABLISSEMENTINFO

echo "CVI;NOM;COOP;TYPE;TELEPHONE;EMAIL"

join -t ";" -1 1 -2 1 -a 1 $FILE_RECOLTANTSANSDR $FILE_ETABLISSEMENTINFO | awk -F ';' '{ print $1 ";" $3 ";" $4 ";" $5 ";" $8 ";" $7 }'

rm $FILE_RECOLTANTSANSDR $FILE_ETABLISSEMENTINFO
