#!/bin/bash

. bin/config.inc

PERIODE=$1
OPTIONS=$2

echo "Type de la DS;Teledeclarant N-1;PDF Brouillon;DS N-1;Login;Email;CVI;CIVABA;Catégorie;Qualité;Nom;Commune;Compte ID;Tiers ID;Mail Envoyé;Commentaires";

curl -s "http://$COUCHDBDOMAIN:$COUCHDBPORT/$COUCHDBBASE/_design/etablissement/_view/all?reduce=false" | cut -d "," -f 1 | sed 's/{"id":"//' | sed 's/"//' | grep "ETABLISSEMENT" | while read ligne
do
    php symfony ds:send-mail-ouverture $PERIODE $ligne $OPTIONS
done
