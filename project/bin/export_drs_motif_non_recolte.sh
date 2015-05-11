#!/bin/bash

. bin/config.inc

ANNEE=$1

if ! test "$ANNEE"; then
    echo "L'année de déclaration de récolte est requise"
    exit;
fi

echo '"motif";"non recolté";"CVI recoltant";"nom recoltant";"appellation";"lieu";"cepage";"vtsgn";"denomination";"superficie";"volume";"dont volume a detruire";"superficie totale";"volume total";"volume a detruire total";"date de creation";"date de validation";"validateur";"hash_produit"'

bash bin/export_drs_csv.sh $ANNEE | grep ';"NON RECOLTE";'