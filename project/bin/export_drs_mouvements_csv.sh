#!/bin/bash

. bin/config.inc

ANNEE=$1

if ! test "$ANNEE"; then
    echo "L'année de déclaration de récolte est requise"
    exit;
fi

echo "type;annee;identifiant;cvi;nom;appellation;lieu;cepage;vtsgn;lieudit;denomination;type mouvement;quantite;identifiant acheteur;cvi acheteur;nom acheteur;doc id"

bash bin/export_drs_csv.sh $ANNEE | grep -v "hash_produit" | awk -v campagne="$ANNEE" -F ";" -f bin/dr2drmouvements.awk | sort | uniq
