#!/bin/bash

. bin/task.inc

DESCRIPTION="Statistiques des déclarations de Stocks propriété pour la période $DS_PERIODE"

. bin/task_start.inc

mkdir -m 777 $TASK_DIR/tmp > /dev/null

EXPORT_FILE=tmp/stats_propriete_ds.csv
echo "$DS_PERIODE"
echo "$TASK_DIR/$EXPORT_FILE"
php -d memory_limit=1024M symfony ds:stats-stocks "$DS_PERIODE" "propriete"  > $TASK_DIR/$EXPORT_FILE

echo "[Télécharger le fichier]($TASK_URL/$EXPORT_FILE)"
