#!/bin/bash

. bin/config.inc

curl -s "http://$COUCHDBDOMAIN:$COUCHDBPORT/$COUCHDBBASE/_design/COMPTE/_view/tous" | cut -d "," -f 1 | sed 's/{"id":"//' | sed 's/"//' | grep "COMPTE" > /tmp/comptes

while read ligne  
do
    php symfony ds:send-mail-ouverture "201307" $ligne
done < /tmp/comptes

