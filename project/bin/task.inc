#!/bin/bash

TASK_DIR=web/mise_a_disposition/taches
TASK_URL=http://declaration.dev.vinsalsace.pro/mise_a_disposition/taches

if [ "$1" != "run" ] 
then
    echo $DESCRIPTION
    exit;
fi