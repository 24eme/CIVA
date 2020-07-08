#!/bin/bash

. bin/task.inc

DESCRIPTION="Export de la liste des DR pour la campagne $DR_CAMPAGNE"

. bin/task_start.inc

mkdir -m 777 $TASK_DIR/tmp > /dev/null

EXPORT_FILE=tmp/export_declaration_liste_dr_$DR_CAMPAGNE.csv

bash bin/export_declaration_liste.sh DR $DR_CAMPAGNE > $TASK_DIR/$EXPORT_FILE

echo "[Télécharger le fichier]($TASK_URL/$EXPORT_FILE?$(date +%Y%m%d%H%M%S))"
