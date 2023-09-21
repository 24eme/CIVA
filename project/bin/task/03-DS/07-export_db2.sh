#!/bin/bash

. bin/task.inc

TASK_DIR=$(pwd)/$TASK_DIR
EXPORT_DIR=ds/db2
LINK="$TASK_URL/$EXPORT_DIR/?C=M;O=D"
DESCRIPTION="Export des DS pour DB2 [Voir tous les exports]($LINK)"

. bin/task_start.inc

mkdir -m 777 -p $TASK_DIR/$EXPORT_DIR > /dev/null

php -d "memory_limit=512M" symfony export:ds-civa "$DS_PERIODE" $TASK_DIR/$EXPORT_DIR "propriete"
php -d "memory_limit=512M" symfony export:ds-civa "$DS_PERIODE" $TASK_DIR/$EXPORT_DIR "negoce"

echo "[Voir les fichiers]($LINK)"
