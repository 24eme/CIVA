#!/bin/bash

. bin/config.inc

ANNEE=$1

if ! test "$ANNEE"; then
    echo "L'année de déclaration de récolte est requise"
    exit;
fi

echo '"CVI acheteur";"nom acheteur";"CVI recoltant";"nom recoltant";"appellation";"lieu";"cepage";"vtsgn";"denomination";"superficie";"volume";"dont volume a detruire";"superficie totale";"volume total";"volume a detruire total";"date de creation";"date de validation";"validateur"'

bash bin/export_drs_csv.sh $ANNEE | grep -v '"CVI acheteur"' | grep -v "mentionVT" | grep -v "mentionSGN" | grep "detail" | cut -d ";" -f -18 | grep -Ei "(séléction|selection|grain| nobles|vendange|tardive|sgn|vt|v\.t|s\.g\.n)"
