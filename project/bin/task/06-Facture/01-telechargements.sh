#!/bin/bash

. bin/task.inc

DESCRIPTION="Statistiques de téléchargement des factures"

. bin/task_start.inc

mkdir -m 777 $TASK_DIR/tmp 2> /dev/null

EXPORT_FILE=tmp/facture_telechargement_statistiques_$(date +%Y%m%d%H%M%S).csv

curl -s "http://$COUCHDBDOMAIN:$COUCHDBPORT/$COUCHDBBASE/_design/facture/_view/etablissement?include_docs=true" | jq -c '.rows[]' | jq -c '[.doc.date_facturation,.doc.telechargee]' | sort | grep -v null | cut -d "," -f 1 |  sort | uniq -c | sed 's/\["//' | sed 's/"//g' | sed -r 's/^[ ]+//' | awk -F ' ' '{ print $2 ";" $1 }' | sort > $TASK_DIR/$EXPORT_FILE.telechargees
curl -s "http://$COUCHDBDOMAIN:$COUCHDBPORT/$COUCHDBBASE/_design/facture/_view/etablissement?include_docs=true" | jq -c '.rows[]' | jq -c '[.doc.date_facturation,.doc.telechargee]' | sort | grep null | cut -d "," -f 1 |  sort | uniq -c | sed 's/\["//' | sed 's/"//g' | sed -r 's/^[ ]+//' | awk -F ' ' '{ print $2 ";" $1 }' | sort > $TASK_DIR/$EXPORT_FILE.pastelechargees

echo "Date;Factures téléchargées;Fatures non téléchargées" > $TASK_DIR/$EXPORT_FILE
join -t ";" -1 1 -2 1 -a 1 $TASK_DIR/$EXPORT_FILE.telechargees $TASK_DIR/$EXPORT_FILE.pastelechargees -r >> $TASK_DIR/$EXPORT_FILE

echo "[Télécharger le fichier]($TASK_URL/$EXPORT_FILE?$(date +%Y%m%d%H%M%S))"