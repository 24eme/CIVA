<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 *
 * @author mathurin
 */
interface IDRMEdiExportable {
    public function getDRMEdiProduitRows(DRMGenerateCSV $drmGenerateCSV);

    public function getDRMEdiMouvementRows(DRMGenerateCSV $drmGenerateCSV);
}
