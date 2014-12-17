#!/bin/bash

. bin/config.inc

ANNEE=$1

if ! test "$ANNEE"; then
    echo "L'année de déclaration de récolte est requise"
    exit;
fi

echo '"CVI acheteur";"non acheteur";"CVI recoltant";"nom recoltant";"appellation";"lieu";"cepage";"vtsgn";"denomination";"volume"'

bash bin/export_drs_csv.sh $ANNEE | cut -d ";" -f 1,2,3,4,5,6,7,8,9,11 | grep -v "TOTAL" | grep -E ";[0-9]{4,8}+"