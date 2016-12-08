#!/bin/bash

. bin/config.inc

PATH_EXPORT=$1

if ! test "$PATH_EXPORT"; then
    echo "Chemin d'export pour les fichiers manquant"
    exit;
fi

DATE_EXPORT=$(date +%Y%m%d-%H%M%S)
FILE_EXPORT_COMPLET=$PATH_EXPORT/tiers-modifications-$DATE_EXPORT-complet.csv

php symfony export:etablissements-modifications > $FILE_EXPORT_COMPLET

echo "$(cat $FILE_EXPORT_COMPLET | grep -E "\*" | wc -l) établissements modifié(s)"

bash bin/postexport_tiers_modifications_sans_emails.sh $FILE_EXPORT_COMPLET > $PATH_EXPORT/tiers-modifications-$DATE_EXPORT-infos.csv
bash bin/postexport_tiers_modifications_avec_emails.sh $FILE_EXPORT_COMPLET > $PATH_EXPORT/tiers-modifications-$DATE_EXPORT-email.csv
