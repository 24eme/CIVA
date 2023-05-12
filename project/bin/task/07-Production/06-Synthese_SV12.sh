#!/bin/bash

. bin/task.inc

DESCRIPTION="Synthese de l'export JSON douane des SV12 de la campagne $DR_CAMPAGNE"

. bin/task_start.inc

mkdir -m 777 $TASK_DIR/tmp > /dev/null

JSON_FILE=tmp/export_sv12_"$DR_CAMPAGNE"_$(date +%Y%m%d%H%M%S).json
EXPORT_FILE=tmp/synthese_sv12_"$DR_CAMPAGNE"_$(date +%Y%m%d%H%M%S).csv

php symfony sv:export-json SV11 "$DR_CAMPAGNE" > "$TASK_DIR/$JSON_FILE"
php symfony sv:synthese-json SV11 "$TASK_DIR/$JSON_FILE" > "$TASK_DIR/$EXPORT_FILE"

echo "[Télécharger le fichier]($TASK_URL/$EXPORT_FILE?$(date +%Y%m%d%H%M%S))"
