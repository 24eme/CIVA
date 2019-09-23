#!/bin/bash

. bin/task.inc

TASK_DIR=$(pwd)/$TASK_DIR
EXPORT_DIR=ds/xml
LINK="$TASK_URL/$EXPORT_DIR/?C=M;O=D"
DESCRIPTION="Export des DS en XML [Voir tous les exports]($LINK)"

. bin/task_start.inc

mkdir -m 777 -p $TASK_DIR/$EXPORT_DIR > /dev/null

php symfony export:ds-xml-civa "$DS_PERIODE" $TASK_DIR/$EXPORT_DIR "$(date +%Y%m%d%H%M%S)"
