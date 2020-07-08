#!/bin/bash

. bin/task.inc

DESCRIPTION="Export des emails des télédeclarants pour la DR $DR_CAMPAGNE"

. bin/task_start.inc

mkdir -m 777 $TASK_DIR/tmp > /dev/null

EXPORT_FILE=tmp/export_email_teledeclarants_dr_$DR_CAMPAGNE.csv

bash bin/export_email_teledeclarant $DR_CAMPAGNE > $TASK_DIR/$EXPORT_FILE

echo "[Télécharger le fichier]($TASK_URL/$EXPORT_FILE?$(date +%Y%m%d%H%M%S))"
