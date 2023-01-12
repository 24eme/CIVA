<?php

class ExportSV extends ExportMiseAdispo
{

    protected function getExportNodeName() {

        return 'svs';
    }

    protected function getDocumentFolder() {

        return 'Production';
    }

    protected function getFileExportClassName() {

        return 'FileExportSVPdf';
    }

    protected function getFileName($file_export) {

        return ExportSVPdf::buildFileName($file_export->getDocument(), true, false);
    }

    protected function getFileNameForMatch($file_export) {

        return "^".str_replace(".pdf", "", ExportSVPdf::buildFileName($file_export->getDocument(), false, false));
    }

    protected function findIds($node) {
        $ids = SVClient::getInstance()->startkey_docid(sprintf("SV11-%s-%s", "0000000000", "0000"))
                    ->endkey_docid(sprintf("SV12-%s-%s", "9999999999", "9999"))
                    ->execute(acCouchdbClient::HYDRATE_ON_DEMAND)->getIds();

        return $ids;
    }


}
