#!/bin/bash

. $(dirname $0)/config.inc

if ! test $WORKINGDIRDISTANT
then
	WORKINGDIRDISTANT=$WORKINGDIR
fi

rsync -aO $WORKINGDIR"/data/import/" $COUCHDISTANTHOST":"$WORKINGDIRDISTANT"/data/import"
rsync -aO $WORKINGDIR"/data/upload/" $COUCHDISTANTHOST":"$WORKINGDIRDISTANT"/data/upload"
rsync -aO $WORKINGDIR"/data/pdf/" $COUCHDISTANTHOST":"$WORKINGDIRDISTANT"/data/pdf"
rsync -aO $WORKINGDIR"/data/mercuriales/" $COUCHDISTANTHOST":"$WORKINGDIRDISTANT"/data/mercuriales"
rsync -aO $WORKINGDIR"/data/export/" $COUCHDISTANTHOST":"$WORKINGDIRDISTANT"/data/export"
rsync -aO $WORKINGDIR"/web/helpPdf/" $COUCHDISTANTHOST":"$WORKINGDIRDISTANT"/web/helpPdf"
rsync -aO $WORKINGDIR"/web/documents/" $COUCHDISTANTHOST":"$WORKINGDIRDISTANT"/web/documents"
rsync -aO $WORKINGDIR"/web/doc-edi/" $COUCHDISTANTHOST":"$WORKINGDIRDISTANT"/web/doc-edi"
rsync -aO --include="*.pdf" --exclude="*" $WORKINGDIR"/web/images/" $COUCHDISTANTHOST":"$WORKINGDIRDISTANT"/web/images"
rsync -aO $WORKINGDIR"/web/mise_a_disposition/" $COUCHDISTANTHOST":"$WORKINGDIRDISTANT"/web/mise_a_disposition"
