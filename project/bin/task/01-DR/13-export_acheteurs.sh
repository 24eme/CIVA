#!/bin/bash

. bin/task.inc

DESCRIPTION="Export des acheteurs de la DR"

. bin/task_start.inc

mkdir -m 777 $TASK_DIR/tmp > /dev/null

EXPORT_FILE=tmp/export_acheteurs.csv

echo "Type;CVI;Nom;Commune" > $TASK_DIR/$EXPORT_FILE

curl -s "http://$COUCHDBDOMAIN:$COUCHDBPORT/$COUCHDBBASE/_design/ACHAT/_view/qualite" | cut -d "," -f 2,4,5,6 | sed 's/"key":\["//' | sed 's/"value":{"cvi":"//' | sed 's/"nom":"//' | sed 's/"commune":"//' | sed 's/"}}//' | sed 's/"//g' | sed 's/,/;/g' | grep ";" >> $TASK_DIR/$EXPORT_FILE

echo "[Télécharger le fichier]($TASK_URL/$EXPORT_FILE?$(date +%Y%m%d%H%M%S))"
