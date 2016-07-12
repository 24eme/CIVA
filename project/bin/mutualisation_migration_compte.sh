. bin/config.inc

bash bin/delete_from_view.sh "http://$COUCHDBDOMAIN:$COUCHDBPORT/$COUCHDBBASE/_design/etablissement/_view/all?reduce=false"; bash bin/delete_from_view.sh "http://$COUCHDBDOMAIN:$COUCHDBPORT/$COUCHDBBASE/_design/societe/_view/all?reduce=false"; bash bin/delete_from_view.sh "http://$COUCHDBDOMAIN:$COUCHDBPORT/$COUCHDBBASE/_design/compte/_view/all?reduce=false"

php symfony import:TiersMigration data/import/Tiers/Tiers-20160411
