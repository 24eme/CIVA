. bin/config.inc

# Suppression des anciens comptes
bash bin/delete_from_view.sh "http://$COUCHDBDOMAIN:$COUCHDBPORT/$COUCHDBBASE/_design/COMPTE/_view/tous"

# Import des vues
cd ..
mkdir .views
make
cd project

bash bin/delete_from_view.sh "http://$COUCHDBDOMAIN:$COUCHDBPORT/$COUCHDBBASE/_design/etablissement/_view/all?reduce=false";
bash bin/delete_from_view.sh "http://$COUCHDBDOMAIN:$COUCHDBPORT/$COUCHDBBASE/_design/societe/_view/all?reduce=false";
bash bin/delete_from_view.sh "http://$COUCHDBDOMAIN:$COUCHDBPORT/$COUCHDBBASE/_design/compte/_view/all?reduce=false"

# Import des Société / Établissement / Compte

php symfony import:TiersMigration data/import/Tiers/Tiers-20160711
#php symfony import:TiersMigration data/import/Tiers/Tiers-clotures-20160711

# Récriture des tiers en établissement dans les contrats
curl -s "http://$COUCHDBDOMAIN:$COUCHDBPORT/$COUCHDBBASE/_design/VRAC/_view/tous" | cut -d "," -f 1 | sed 's/{"id":"//' | sed 's/"//' | grep "VRAC" | sed 's/^/php symfony maintenance:mutualisation-compte-remplacement-doc /' | bash

curl -s "http://$COUCHDBDOMAIN:$COUCHDBPORT/$COUCHDBBASE/_all_docs" | grep "ANNUAIRE" | cut -d "," -f 1 |sed 's/{"id":"//' | sed 's/"//' | sed 's/^/php symfony maintenance:mutualisation-compte-remplacement-doc /' | bash
