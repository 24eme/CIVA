#!/bin/bash

. bin/task.inc

EXPORT_DIR=contrat/export_vrac_db2
LINK="$TASK_URL/$EXPORT_DIR/?C=N;O=D"

DESCRIPTION="Export DB2 des derniers contrats vracs et leurs modifications\n
            [Voir tous les exports]($LINK)"

. bin/task_start.inc

mkdir -m 777 -p $TASK_DIR/$EXPORT_DIR > /dev/null

php symfony export:vrac $TASK_DIR/$EXPORT_DIR

echo "[Voir les fichiers]($LINK)"