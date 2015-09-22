#!/bin/bash

. bin/config.inc

echo "Type de la DS;DS déclaré;Login;Email;CVI;CIVABA;Catégorie;Qualité;Nom;Commune;Compte ID;Tiers ID;Mail Envoyé;Commentaires";

curl -s "http://$COUCHDBDOMAIN:$COUCHDBPORT/$COUCHDBBASE/_design/COMPTE/_view/tous" | cut -d "," -f 1 | sed 's/{"id":"//' | sed 's/"//' | grep "COMPTE" | while read ligne  
do
    php symfony ds:declarant "201507" $ligne
done

