#!/bin/bash

. $(echo $0 | sed 's/[^\/]*$//')config.inc

DATADB2FILEACTIF=$1
DATADB2FILECLOTURE=$(echo -n $1 | sed 's|/TIERS_|/CLOTURES_|')

if ! test "DATADB2FILEACTIF"; then
    echo "Le chemin vers le fichier des tiers actif doit être spécifié en 1er argument"
    exit;
fi

if [ ! -f $DATADB2FILEACTIF ]
then
    echo "Le fichier des tiers actifs $DATADB2FILEACTIF n'existe pas"
    exit 0
fi

if [ ! -f $DATADB2FILECLOTURE ]
then
    echo "Le fichier des tiers actifs $DATADB2FILECLOTURE n'existe pas"
    exit 0
fi

bash bin/build_tiers_file_from_db2.sh $DATADB2FILEACTIF $DATADB2FILECLOTURE

TMPTIERSFILE=/tmp/tiers_$(date +%Y%m%d%H%M%S)

php symfony tiers:db2-csv data/import/Tiers/Tiers-last > $TMPTIERSFILE

php symfony societe:import-csv $TMPTIERSFILE

php symfony etablissement:import-csv $TMPTIERSFILE

php symfony compte:import-csv $TMPTIERSFILE
