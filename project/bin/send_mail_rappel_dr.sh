#!/bin/bash

. bin/config.inc

ANNEE=$1
FILE_TIERS=data/import/Tiers/Tiers-last
FILE_RECOLTANTSANSDR=/tmp/$(date +%Y%m%d%H%M%S)_export_rappel_recoltant_sans_dr.csv
MAIL_FROM="Webmaster Vinsalsace.pro <ne_pas_repondre@civa.fr>"
MAIL_REPLYTO="$2"
MAIL_SUBJECT="Relance pour absence de DR"
MAIL_BODY="Bonjour,\n\nLa Déclaration de Récolte 'Papier' a définitivement disparue en 2017 et vous devez donc désormais télé-déclarer votre Récolte sur le Portail du CIVA.\n\nÀ ce jour nous n'avons pas enregistré de saisie pour la déclaration de Récolte 2020.\n\n
Suite à quelques soucis techniques, et exceptionnellement cette année, vous devrez télé-déclarer votre Récolte AVANT le vendredi 11 décembre 23h59.\n\nVous pouvez effectuer la saisie en cliquant sur le lien suivant <https://declaration.vinsalsace.pro> \n\nCordialement, \n\nDominique WOLFF"

if ! test "$ANNEE"; then
echo "L'année de déclaration de récolte est requise en 1er argument"
exit;
fi

if ! test "$MAIL_REPLYTO"; then
echo "Un email de reply to est requis en 2ème argument"
exit;
fi

echo "Envoi des mails pour les récoltants n'ayant pas démarré leur DR"

bash bin/export_recoltant_sans_dr.sh $ANNEE | grep -v ";COP;" > $FILE_RECOLTANTSANSDR

echo "Using file $FILE_RECOLTANTSANSDR"

cat $FILE_RECOLTANTSANSDR | cut -d ";" -f 6 | grep "@" | sort | uniq | while read email ; do
	echo "Sending $email ...";
	echo -e $MAIL_BODY | mail -s "$MAIL_SUBJECT" -r "$MAIL_FROM" -S replyto="$MAIL_REPLYTO" $email;
	echo "Sended $email";
	sleep 2;
done

echo "Envoi des mails de récoltant n'ayant pas terminé leur DR"

php symfony email:dr-validation $ANNEE --application=civa
