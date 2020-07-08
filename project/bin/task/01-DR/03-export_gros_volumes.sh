#!/bin/bash

. bin/task.inc

DESCRIPTION="Export CSV des produits de la DR $DR_CAMPAGNE dont le volume est supérieur ou égal à 1000"

. bin/task_start.inc

mkdir -m 777 $TASK_DIR/tmp > /dev/null

EXPORT_FILE=tmp/export_drs_gros_volumes_"$DR_CAMPAGNE"_$(date +%Y%m%d%H%M%S).csv

bash bin/export_drs_gros_volumes.sh $DR_CAMPAGNE > $TASK_DIR/$EXPORT_FILE

echo "[Télécharger le fichier]($TASK_URL/$EXPORT_FILE?$(date +%Y%m%d%H%M%S))"
