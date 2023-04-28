#!/bin/bash

. bin/task.inc

DESCRIPTION="Export JSON douane des SV12 de la campagne $DR_CAMPAGNE"

. bin/task_start.inc

mkdir -m 777 $TASK_DIR/tmp > /dev/null

EXPORT_FILE=tmp/export_sv12_"$DR_CAMPAGNE"_$(date +%Y%m%d%H%M%S).json

php symfony sv:export-json SV12 "$DR_CAMPAGNE" > "$TASK_DIR/$EXPORT_FILE"

echo "[Télécharger le fichier]($TASK_URL/$EXPORT_FILE?$(date +%Y%m%d%H%M%S))"
