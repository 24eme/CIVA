
. bin/config.inc

cat $PATH_MISEADISPO_CIVA/ds/*csv | head -n 1 > data/ds.csv
for csv in $PATH_MISEADISPO_CIVA/ds/*csv ; do
    tail -n +2 $csv
done >> data/ds.csv

cat $PATH_MISEADISPO_CIVA/tmp/export_drs_2* | head -n 1 > data/dr.csv
ls $PATH_MISEADISPO_CIVA/tmp/ | grep export_drs_2 | awk -F '_' '{print $1"_"$2"_"$3}'  | sort -u | while read file ; do
    ls -rt  $PATH_MISEADISPO_CIVA"/tmp/"$file* | tail -n 1 ;
done | while read file ; do
    tail -n +2 $file ;
done >> data/dr.csv
