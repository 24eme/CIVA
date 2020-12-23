#!/bin/bash

. bin/task.inc

TASK_DIR=$(pwd)/$TASK_DIR
EXPORT_DIR=dr/xml
LINK="$TASK_URL/$EXPORT_DIR/?C=M;O=D"
DESCRIPTION="Export des DR en XML pour les Douanes [Voir tous les exports]($LINK)"

. bin/task_start.inc

mkdir -m 777 -p $TASK_DIR/$EXPORT_DIR > /dev/null

php symfony export:dr-xml $DR_CAMPAGNE Civa > /dev/null

cp $WORKINGDIR/data/export/dr/xml/DR-$DR_CAMPAGNE-Civa.xml $TASK_DIR/$EXPORT_DIR/"$(date +%Y%m%d%H%M%S)"_DR-"$DR_CAMPAGNE"-Civa.xml

echo "[Voir les fichiers]($LINK)"
