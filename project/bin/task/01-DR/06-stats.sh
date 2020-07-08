#!/bin/bash

. bin/task.inc

DESCRIPTION="Statistiques des déclarations de Récolte $DR_CAMPAGNE"

. bin/task_start.inc

mkdir -m 777 $TASK_DIR/tmp > /dev/null

EXPORT_FILE=tmp/dr_statistiques_"$DR_CAMPAGNE"_$(date +%Y%m%d%H%M%S).csv

php symfony dr:stats-recolte $DR_CAMPAGNE | sed 's/\./,/g' > $TASK_DIR/$EXPORT_FILE

echo "[Télécharger le fichier]($TASK_URL/$EXPORT_FILE?$(date +%Y%m%d%H%M%S))"
