<?php

class ExportDS extends ExportMiseAdispo
{

    protected function getExportNodeName() {

        return 'dss';
    }

    protected function getDocumentFolder() {

        return 'declarations_de_stocks';
    }

    protected function getFileExportClassName() {

        return 'FileExportDSPdf';
    }

    protected function getFileName($file_export) {

        return 'DS-'.$file_export->getDocument()->identifiant.'-'.$file_export->getDocument()->periode.'.pdf';
    }

}