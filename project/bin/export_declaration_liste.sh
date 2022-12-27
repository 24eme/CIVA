. bin/config.inc

TYPE_DECLARATION=$1
PERIODE=$2

if ! test "$TYPE_DECLARATION"; then
    echo "Type de déclaration DR ou DS"
    exit;
fi

if ! test "$PERIODE"; then
    echo "La campagne est requise"
    exit;
fi

echo "campagne ; cvi ; date de validation ; date de modification ; date dépot en mairie ; étape"

curl -s http://$COUCHDBDOMAIN:$COUCHDBPORT/$COUCHDBBASE/_design/STATS/_view/DR?reduce=false | grep "\-$PERIODE" | sed 's/"//g' | sed 's/{id://' | sed 's/\]//' |sed 's/,value:1}//' | sed 's/key:\[//' |sed 's/DR-//' | sed -r "s/-$PERIODE//" | sed 's/null//g' | sed 's/,/;/g' | sed -r 's/.?$//' | sed -r 's/;$//' | awk -F ';' '{ print $2 ";" $1 ";" $5 ";" $8 ";" $7 ";" $6 }' 