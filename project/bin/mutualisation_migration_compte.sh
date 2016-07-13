. bin/config.inc

bash bin/delete_from_view.sh "http://$COUCHDBDOMAIN:$COUCHDBPORT/$COUCHDBBASE/_design/etablissement/_view/all?reduce=false"; bash bin/delete_from_view.sh "http://$COUCHDBDOMAIN:$COUCHDBPORT/$COUCHDBBASE/_design/societe/_view/all?reduce=false"; bash bin/delete_from_view.sh "http://$COUCHDBDOMAIN:$COUCHDBPORT/$COUCHDBBASE/_design/compte/_view/all?reduce=false"

# Import des Société / Établissement / Compte

php symfony import:TiersMigration data/import/Tiers/Tiers-20160711
php symfony import:TiersMigration data/import/Tiers/Tiers-clotures-20160711


# Récriture des tiers en établissement dans les contrats
curl -s "http://$COUCHDBDOMAIN:$COUCHDBPORT/$COUCHDBBASE/_design/VRAC/_view/tous" | cut -d "," -f 1 | sed 's/{"id":"//' | sed 's/"//' | grep "VRAC" | sed 's/^/php symfony maintenance:mutualisation-compte-remplacement-doc /' | bash

curl -s "http://$COUCHDBDOMAIN:$COUCHDBPORT/$COUCHDBBASE/_all_docs" | grep "ANNUAIRE" | cut -d "," -f 1 |sed 's/{"id":"//' | sed 's/"//' | sed 's/^/php symfony maintenance:mutualisation-compte-remplacement-doc /' | bash

# Import de la conf au format mutualisé
curl -sX DELETE "http://$COUCHDBDOMAIN:$COUCHDBPORT/$COUCHDBBASE/CONFIGURATION"?rev=$(curl -sX GET "http://$COUCHDBDOMAIN:$COUCHDBPORT/$COUCHDBBASE/CONFIGURATION" | grep -Eo '"_rev":"[a-z0-9-]+"' | sed 's/"//g' | sed 's/_rev://')

php symfony import:configuration-mutualisation CONFIGURATION data/configuration

curl -sX DELETE "http://$COUCHDBDOMAIN:$COUCHDBPORT/$COUCHDBBASE/CURRENT"?rev=$(curl -sX GET "http://$COUCHDBDOMAIN:$COUCHDBPORT/$COUCHDBBASE/CURRENT" | grep -Eo '"_rev":"[a-z0-9-]+"' | sed 's/"//g' | sed 's/_rev://')

curl -s -X PUT -d '{ "_id": "CURRENT", "type": "Current", "configurations": { "2000-08-01": "CONFIGURATION" } }' http://$COUCHDBDOMAIN:$COUCHDBPORT/$COUCHDBBASE/CURRENT

cd ..
mkdir .views
make
cd project
