<?php

class ExportDR extends ExportMiseAdispo
{

	 protected function getExportNodeName() {

        return 'drs';
    }

    protected function getDocumentFolder() {

        return 'DR';
    }

    protected function getFileExportClassName() {

        return 'FileExportDRPdf';
    }

    protected function getFileName($file_export) {

        return ExportDRPdf::buildFileName($file_export->getDocument(), true, false);
    }

    protected function getFileNameForMatch($file_export) {

        return "^".str_replace(".pdf", "", ExportDRPdf::buildFileName($file_export->getDocument(), false, false));
    }

}