#!/bin/bash

. bin/task.inc

EXPORT_DIR=contrat/export_bouteille_db2
LINK="$TASK_URL/$EXPORT_DIR/?C=N;O=D"

DESCRIPTION="Export DB2 des derniers contrats bouteilles et leurs modifications\n
            [Voir tous les exports]($LINK)"

. bin/task_start.inc

mkdir -m 777 -p $TASK_DIR/$EXPORT_DIR > /dev/null

php symfony export:bouteille $TASK_DIR/$EXPORT_DIR

echo "[Voir les fichiers]($LINK)"