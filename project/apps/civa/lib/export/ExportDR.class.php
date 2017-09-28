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

    public function exportStatsCSV() {
        foreach($this->_campagnes as $campagne) {
            $this->exportStatsCSVByCampagne($campagne);
        }
    }

    public function exportStatsCSVByCampagne($campagne) {
        $csv_path = sprintf("%s/%s/%s_RECAPITULATIF_APPELLATION_CEPAGE_%s.csv", $this->_file_dir, $campagne, $campagne, $this->_export->identifiant);

        if (file_exists($csv_path) && $this->getHashMd5($campagne) == $this->getHashMd5FileFromFile($campagne)) {
            return;
        }

		$this->mkdirUnlessFolder($this->_file_dir.'/'.$campagne);

        $export = new ExportDRStatsCsv($this->getIds(), $campagne);

        $f = fopen($csv_path, 'w');
        fwrite($f, "\xef\xbb\xbf");
        fwrite($f, $export->export());
        fclose($f);

        if ($this->_debug) {
            echo sprintf("-- generation csv stats : %s\n", $csv_path);
        }
    }

}
