<?php
class drmActions extends sfActions {


  /**
   * Route de création de DRM et de fichier EDI venant alimenter la DRM
   * @param sfWebRequest $request
   */
  public function executeCreateEdiFileFromDocuments(sfWebRequest $request) {

      set_time_limit(0);
      ini_set('memory_limit', '512M');
      $allowIps = array("127.0.0.1", "localhost", "::1");
      if(!in_array($this->getRequest()->getHttpHeader('addr','remote'), $allowIps) && !$this->getUser()->hasCredential(CompteSecurityUser::CREDENTIAL_ADMIN)) {
        throw new sfException("Accès interdit");
      }

      $identifiant = $request->getParameter('identifiant');
      $periode = $request->getParameter('periode');
      $numero_accise = "";

      $ediFileContent = "";

      /**
      * Cas d'initialisation : on récupère les informations CATALOGUE produit depuis le doc le plus récent entre DS et DR
      */
      $drmGenerateCSV = new DRMGenerateCSV($identifiant, $numero_accise, $periode, (bool) $request->getParameter('aggregate', false));
      $documentRepriseInfos = $drmGenerateCSV->getDocumentsForRepriseCatalogue();
      foreach ($documentRepriseInfos as $documentRepriseInfo) {
        $ediFileContent.= $this->createReprise($documentRepriseInfo,$drmGenerateCSV);
      }
      if(count($documentRepriseInfos) > 1){
        $doubleDs = true;
        foreach ($documentRepriseInfos as $documentRepriseInfo) {
          if($documentRepriseInfo->docType != "DS"){
            $doubleDs = false;
          }
        }
        if($doubleDs){
          $ediFileContent = $this->fusionStocks($ediFileContent);
        }
      }
       /**
        * Dans tout les cas de figure, après la DRM crée, on récupère des mouvements/stock :
        *  - Si la DRM est juste après la DR, on récupère les mouvements d'entrées récolte
        *  - Si la DRM est juste après une DS, on récupère les stocks début de mois
        *  - Dans tout les cas, on récupère les mouvements de sortie contrat en phase d'enlèvement
        */
      $repriseMvtInfos = $drmGenerateCSV->getDocumentsForRepriseMouvements();
      foreach ($repriseMvtInfos as $repriseMvtInfo) {
        $ediFileContent.= $this->createReprise($repriseMvtInfo,$drmGenerateCSV);
      }
      if($ediFileContent !== false){
          $this->response->setContentType('text/csv');

          echo $ediFileContent;
          exit;

        }else{
          return $this->redirect('drm');
        }
    }

    private function createReprise($documentRepriseInfo,$drmGenerateCSV){
      $docTypeClientName = $documentRepriseInfo->docType.'Client';
      $docTypeClient = $docTypeClientName::getInstance();
      $doc = $docTypeClient->find($documentRepriseInfo->idDoc);

      $ediFileUpdate = "";
      if(!$documentRepriseInfo->viewResult){
        switch ($documentRepriseInfo->repriseType) {
          case DRMGenerateCSV::REPRISE_TYPE_CATALOGUE :
          $ediFileUpdate.=$doc->getDRMEdiProduitRows($drmGenerateCSV);
          break;
          case DRMGenerateCSV::REPRISE_TYPE_MOUVEMENT :
          $ediFileUpdate.=$doc->getDRMEdiMouvementRows($drmGenerateCSV);
          break;
        }
      }else{
        $viewresult = $documentRepriseInfo->viewResult;
        $produitHash = $viewresult->value[DRMRepriseMvtsView::VALUE_PRODUIT_HASH];
        $catMouvement = $viewresult->value[DRMRepriseMvtsView::VALUE_CAT_MVT];
        $typeMouvement = $viewresult->value[DRMRepriseMvtsView::VALUE_TYPE_MVT];
        $volume = $viewresult->value[DRMRepriseMvtsView::VALUE_VOLUME];
        $cepageNode = $doc->getOrAdd($produitHash);
        foreach ($cepageNode->getProduitsDetails()  as $key => $produitsDetail) {
          foreach ($produitsDetail->getRetiraisons() as $retiraison) {
            if(substr(str_replace("-",'',$retiraison->getDate()),0,6) == $drmGenerateCSV->getPeriode()){
              $ediFileUpdate .= $drmGenerateCSV->createRowMouvementProduitDetail($produitsDetail, $catMouvement,$typeMouvement,$volume,$documentRepriseInfo->idDoc);
            }
          }
        }
      }
      return $ediFileUpdate;
    }

    private function fusionStocks($fileCsvStocks){
        $csvRows = explode("\n", $fileCsvStocks);
        $stocksUniq = array();
        foreach ($csvRows as $line) {
          $csvFields = str_getcsv($line,';');
          if(count($csvFields) <= 1){ continue; }
          $key = "";
          for ($i=0; $i < 15; $i++) {
            $key.= $csvFields[$i].";";
          }
          if(array_key_exists($key,$stocksUniq)){
            $csvOldFields = str_getcsv($stocksUniq[$key],';');
            $stocksUniq[$key] = str_replace($csvOldFields[16],$csvOldFields[16]+$csvFields[16],$stocksUniq[$key]);
          }else{
            $stocksUniq[$key] = $line;
          }
        }
        $newFileCsvStocks = "";
        foreach ($stocksUniq as $key => $line) {
           $newFileCsvStocks.=$line."\n";
        }
        return $newFileCsvStocks;
    }

}
