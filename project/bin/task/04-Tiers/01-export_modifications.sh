#!/bin/bash

. bin/task.inc

EXPORT_DIR=tiers/modifications
LINK="$TASK_URL/$EXPORT_DIR/?C=M;O=D"

DESCRIPTION="Export des modifications tiers\n
            [Voir tous les exports]($LINK)"

. bin/task_start.inc

mkdir -m 777 -p $TASK_DIR/$EXPORT_DIR > /dev/null

bash bin/export_tiers_modifications.sh $TASK_DIR/$EXPORT_DIR

echo "[Voir les fichiers]($LINK)"