#!/bin/bash

. bin/task.inc

DESCRIPTION="Statistiques de téléchargement des factures"

. bin/task_start.inc

mkdir -m 777 $TASK_DIR/tmp 2> /dev/null

EXPORT_FILE=tmp/facture_telechargement_statistiques_$(date +%Y%m%d%H%M%S).csv

echo "Date;Nombre de factures téléchargés" > $TASK_DIR/$EXPORT_FILE
curl -s "http://$COUCHDBDOMAIN:$COUCHDBPORT/$COUCHDBBASE/_design/facture/_view/etablissement?include_docs=true" | jq -c '.rows[]' | jq -c '[.doc.telechargee]' | sort | uniq -c | cut -d '"' -f 1,2 | grep -v null | sed 's/\[/;/' | awk -F ';' '{ print $2 ";" $1 }' | sed 's/"//' | sed 's/ //g' | sort -r >> $TASK_DIR/$EXPORT_FILE

echo "[Télécharger le fichier]($TASK_URL/$EXPORT_FILE?$(date +%Y%m%d%H%M%S))"