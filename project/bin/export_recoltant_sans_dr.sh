#!/bin/bash

. bin/config.inc

ANNEE=$1

if ! test "$ANNEE"; then
echo "L'année de déclaration de récolte est requise"
exit;
fi

echo "CVI;NOM;EMAIL;TELEPHONE"
curl -s "$COUCHDBDOMAIN:$COUCHDBPORT/$COUCHDBBASE/_design/etablissement/_view/all?reduce=false" | grep ACTIF | grep PRODUCTEUR | cut -d "," -f 9,11,17,18 | sed 's/,"value":\[/;/' | sed 's/"//g' | sed 's/,null/,/g' | grep -E '^[0-9]+' | sed 's/,/;/g' | sort -t ";" -k 1,1 | grep -E "^6(7|8)" | sort | uniq > /tmp/recoltant

curl -s "$COUCHDBDOMAIN:$COUCHDBPORT/$COUCHDBBASE/_design/compte/_view/tags" | grep droits | grep '"teledeclaration_dr"' | cut -d "," -f 1 | sed 's/{"id":"COMPTE-//' | sed 's/"//' | grep -E "^6" | sort | uniq > /tmp/compte_droit_dr

join -t ";" -1 1 -2 1 /tmp/recoltant /tmp/compte_droit_dr | while read line; do CVI=$(echo -n $line | cut -d ";" -f 1); curl -s $COUCHDBDOMAIN:$COUCHDBPORT/$COUCHDBBASE/DR-$CVI-$ANNEE | grep "not_found" && echo $line; done | grep -v not_found

rm /tmp/recoltant /tmp/compte_droit_dr
