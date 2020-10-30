
. bin/config.inc

cat $PATH_MISEADISPO_CIVA/ds/*csv | head -n 1 > data/ds.csv
for csv in $PATH_MISEADISPO_CIVA/ds/*csv ; do
    tail -n +2 $csv
done >> data/ds.csv

echo -n "TYPE;ANNEE;" > data/dr.csv
cat $PATH_MISEADISPO_CIVA/tmp/export_drs_2019* | head -n 1 >> data/dr.csv
ls $PATH_MISEADISPO_CIVA/tmp/ | grep export_drs_2 | grep -vE '2014|2015|2016' | awk -F '_' '{print $1"_"$2"_"$3}'  | sort -u | while read file ; do
    ls -rt  $PATH_MISEADISPO_CIVA"/tmp/"$file* | tail -n 1 ;
done | while read file ; do
    ANNEE=$(echo $file | sed 's/.*export_drs_//' | sed 's/_.*//')
    tail -n +2 $file  | sed 's/^/DR;'$ANNEE';/';
done >> data/dr.csv

if test -f $METABASE_SQLITE ; then
    if test "$METABASE_SQLITE"; then
        cp $METABASE_SQLITE $METABASE_SQLITE".tmp"
        python $BASEDIR"/bin/csv2sql.py" $METABASE_SQLITE".tmp"
        mv -f $METABASE_SQLITE".tmp" $METABASE_SQLITE
    fi
fi
