<?php

class ExportDR extends ExportMiseAdispo
{

	 protected function getExportNodeName() {

        return 'drs';
    }

    protected function getDocumentFolder() {

        return 'declarations_de_recolte';
    }

    protected function getFileExportClassName() {

        return 'FileExportDRPdf';
    }

    protected function getFileName($file_export) {

        return $file_export->getDocument()->_id.'.pdf';
    }

}