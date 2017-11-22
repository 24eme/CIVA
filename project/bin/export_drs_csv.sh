#!/bin/bash

. bin/config.inc

ANNEE=$1

if ! test "$ANNEE"; then
    echo "L'année de déclaration de récolte est requise"
    exit;
fi

echo '"CVI acheteur";"nom acheteur";"CVI recoltant";"nom recoltant";"appellation";"lieu";"cepage";"vtsgn";"denomination";"superficie";"volume";"dont volume a detruire";"superficie totale";"volume total";"volume a detruire total";"dont vci";"vci total";"date de validation";"date de modification";"validateur";"hash_produit"'

curl -s "http://$COUCHDBDOMAIN:$COUCHDBPORT/$COUCHDBBASE/_design/STATS/_view/DR?reduce=false" | grep "\[\"$ANNEE\",true,true," | grep -Eo "DR-[0-9]+-[0-9]+" | php symfony export:dr-csv
