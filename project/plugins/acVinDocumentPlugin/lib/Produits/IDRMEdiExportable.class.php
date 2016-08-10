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
    public function getDRMEdiProduitRows(DRMExportCsvEdi $edi);

    public function getDRMEdiMouvementRows(DRMExportCsvEdi $edi);
}
