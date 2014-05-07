#!/bin/bash

. bin/config.inc

DESCRIPTION="Export DB2 des derniers contrats vracs et leurs modifications"

. bin/task.inc

EXPORT_DIR=contrat/export_vrac_db2

mkdir -p $TASK_DIR/$EXPORT_DIR > /dev/null

php symfony export:vrac $TASK_DIR/$EXPORT_DIR

echo "Télécharger les fichiers : $TASK_URL/$EXPORT_DIR"