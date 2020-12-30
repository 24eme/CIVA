#!/bin/bash

. bin/task.inc

TASK_DIR=$(pwd)/$TASK_DIR
EXPORT_DIR=dr/xml
LINK="$TASK_URL/$EXPORT_DIR/?C=M;O=D"
DESCRIPTION="Export des DR en XML pour le CIVA [Voir tous les exports]($LINK)"

. bin/task_start.inc

mkdir -m 777 -p $TASK_DIR/$EXPORT_DIR > /dev/null

php symfony export:dr-xml $DR_CAMPAGNE Civa > /dev/null

sed -i 's#<L1>1B001S 9,1B001S</L1>#<L1>1B001S 9</L1>#g' $WORKINGDIR/data/export/dr/xml/DR-$DR_CAMPAGNE-Civa.xml

cp $WORKINGDIR/data/export/dr/xml/DR-$DR_CAMPAGNE-Civa.xml $TASK_DIR/$EXPORT_DIR/"$(date +%Y%m%d%H%M%S)"_DR-"$DR_CAMPAGNE"-Civa.xml

echo "[Voir les fichiers]($LINK)"
