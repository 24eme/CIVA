#!/bin/bash

. bin/config.inc

PERIODE=$1
OPTIONS=$2

curl -s "http://$COUCHDBDOMAIN:$COUCHDBPORT/$COUCHDBBASE/_design/etablissement/_view/all?reduce=false" | cut -d "," -f 1 | sed 's/{"id":"//' | sed 's/"//' | grep "ETABLISSEMENT" | while read ligne
do
    php symfony ds:send-mail-rappel $PERIODE $ligne propriete $OPTIONS
    php symfony ds:send-mail-rappel $PERIODE $ligne negoce $OPTIONS
done
