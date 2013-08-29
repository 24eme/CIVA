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

        return ExportDSPdf::buildFileName($file_export->getDocument(), true, false);
    }

    protected function getFileNameForMatch($file_export) {

        return "^".str_replace(".pdf", "", ExportDSPdf::buildFileName($file_export->getDocument(), false, false));
    }

}