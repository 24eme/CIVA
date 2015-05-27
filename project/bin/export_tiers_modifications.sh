#!/bin/bash

. bin/config.inc

PATH_EXPORT=$1

if ! test "$PATH_EXPORT"; then
    echo "Chemin d'export pour les fichiers manquant"
    exit;
fi

PATH_SEQUENCE=data/export/tiers/tiers_modifications.num_sequence

if [ ! -e $PATH_SEQUENCE ]
then
    echo "Le fichier d'enregistrement du numéro de séquence n'existe pas"
    exit
fi

NUM_SEQUENCE=$(cat data/export/tiers/tiers_modifications.num_sequence)

if [ ! $NUM_SEQUENCE ]
then
    echo "Le numéro de séquence est vide"
    exit
fi

echo "Export des modifications depuis le numéro de séquence $NUM_SEQUENCE"

DATE_EXPORT=$(date +%Y%m%d-%H%M%S)
FILE_EXPORT_COMPLET=$PATH_EXPORT/tiers-modifications-$DATE_EXPORT-complet.csv

echo '"_id";"type";"db2/num";"db2/no_stock";"cvi";"civaba";"cvi_acheteur";"intitule";"no_accises";"nom";"commune";"declaration_commune";"declaration_insee";"email";"siren";"siret";"telephone";"fax";"qualite";"categorie";"cave_cooperative";"web";"exploitant/adresse";"exploitant/code_postal";"exploitant/commune";"exploitant/date_naissance";"exploitant/nom";"exploitant/sexe";"exploitant/telephone";"siege/adresse";"siege/code_postal";"siege/commune";"siege/insee_commune"' > $FILE_EXPORT_COMPLET

php symfony export:tiers-modifications-csv --flag_revision=true $NUM_SEQUENCE | sed 's/* ()//g' | grep '*' >> $FILE_EXPORT_COMPLET

echo "$(cat $FILE_EXPORT_COMPLET | grep -v '^"_id"' | wc -l | cut -d " " -f 1) tiers modifié(s)"

bash bin/postexport_tiers_modifications_sans_emails.sh $FILE_EXPORT_COMPLET > $PATH_EXPORT/tiers-modifications-$DATE_EXPORT-infos.csv
bash bin/postexport_tiers_modifications_avec_emails.sh $FILE_EXPORT_COMPLET > $PATH_EXPORT/tiers-modifications-$DATE_EXPORT-email.csv

curl -s -X GET "http://$COUCHDBDOMAIN:$COUCHDBPORT/$COUCHDBBASE" | grep -Eo '"update_seq":[0-9]+,' | sed 's/"update_seq"://' | sed 's/,//' > data/export/tiers/tiers_modifications.num_sequence