#!/bin/bash

. $(echo $0 | sed 's/[^\/]*$//')config.inc

DATADB2FILE=$1

if ! test "DATADB2FILE"; then
    echo "Le chemin vers le fichier des tiers actif doit être spécifié en 1er argument"
    exit;
fi

if [ ! -f $DATADB2FILE ]
then
    echo "Le fichier de tier $DATADB2FILE n'existe pas"
    exit 0
fi

TMPTIERSFILE=/tmp/tiers_pontuel_$(date +%Y%m%d%H%M%S)

php symfony tiers:db2-csv $DATADB2FILE > $TMPTIERSFILE

php symfony societe:import-csv $TMPTIERSFILE
php symfony etablissement:import-csv $TMPTIERSFILE
php symfony compte:import-csv $TMPTIERSFILE
