#!/bin/bash

. bin/config.inc

if [ -z $1 ]
then
      echo "Il faut spécifier le(s) droit(s) en argument";
      exit;
fi

curl -s "http://$COUCHDBDOMAIN:$COUCHDBPORT/$COUCHDBBASE/_design/compte/_view/all" | grep "COMPTE" | while read ligne
do
    COMPTEID=$(echo $ligne | cut -d',' -f1 | cut -d'"' -f4);
    for DROIT in "$@"
    do
        php symfony compte:add-droit $COMPTEID $DROIT --force-add-node="0"
    done
done
