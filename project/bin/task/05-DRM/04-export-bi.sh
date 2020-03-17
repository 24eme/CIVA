#!/bin/bash

. bin/task.inc

TASK_DIR=$(pwd)/$TASK_DIR
EXPORT_DIR=export/bi
LINK="$TASK_URL/$EXPORT_DIR/"
DESCRIPTION="Export BI"

. bin/task_start.inc

echo "[Voir les fichiers]($LINK)"
