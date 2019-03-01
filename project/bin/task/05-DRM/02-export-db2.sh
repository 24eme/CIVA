#!/bin/bash

. bin/task.inc

TASK_DIR=$(pwd)/$TASK_DIR
EXPORT_DIR=drm/export_drm_db2
LINK="$TASK_URL/$EXPORT_DIR/?C=M;O=D"
DESCRIPTION="Export des DRM pour DB2 [Voir tous les exports]($LINK)"

. bin/task_start.inc

mkdir -m 777 -p $TASK_DIR/$EXPORT_DIR > /dev/null

cd $GIILDA_DIR

mkdir -m 777 -p $TASK_DIR/$EXPORT_DIR > /dev/null
php symfony drm:export-db2 $TASK_DIR/$EXPORT_DIR --application=civa
echo "[Voir les fichiers]($LINK)"
