#!/bin/bash

. bin/task.inc

DESCRIPTION="Export CSV des contrats non cloturés"

. bin/task_start.inc

mkdir -m 777 $TASK_DIR/tmp > /dev/null

EXPORT_FILE=tmp/export_contrats_non_clotures.csv

bash bin/export_contrats_non_clotures.sh > $TASK_DIR/$EXPORT_FILE

echo "[Télécharger le fichier]($TASK_URL/$EXPORT_FILE?$(date +%Y%m%d%H%M%S))"
