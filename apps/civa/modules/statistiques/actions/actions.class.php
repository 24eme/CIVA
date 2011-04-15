<?php

/**
 * statistiques actions.
 *
 * @package    civa
 * @subpackage statistiques
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class statistiquesActions extends sfActions {
    /**
     * Executes index action
     *
     * @param sfRequest $request A request object
     */
    public function executeIndex(sfWebRequest $request) {
        ini_set('memory_limit', '512M');

        $this->nbInscrit = 0;
        $this->etapeDrValidee=0;
        $this->etapeValidation = 0;
        $this->etapeRecolte = 0;
        $this->etapeExploitation = 0;
        $this->etapeDrNonValidee = 0;
        $this->etapeNoDr = 0;
        $this->nbInscritGamma = 0;

        $tiers = sfCouchdbManager::getClient("Tiers")->getAllCvi(sfCouchdbClient::HYDRATE_JSON);

        foreach ($tiers as $item) {
            if ($item->cvi == "7523700100") {
                continue;
            }
            if ($item->recoltant == 1 && $item->cvi != "7523700100") {
                if (substr($item->mot_de_passe, 0, 6) !== "{TEXT}") {
                    $this->nbInscrit++;
                    try {
                        $dr = sfCouchdbManager::getClient()->getDoc('DR-'.$item->cvi.'-2010');
                        if(!isset($dr->validee) || !$dr->validee) {
                            if(isset($dr->etape) && $dr->etape=="validation") $this->etapeValidation++;
                            if(isset($dr->etape) && $dr->etape=="recolte") $this->etapeRecolte++;
                            if(isset($dr->etape) && $dr->etape=="exploitation") $this->etapeExploitation++;
                            $this->etapeDrNonValidee++;
                        }elseif(isset($dr->validee) && $dr->validee) {
                            $this->etapeDrValidee++;
                        }
                    }catch (Exception $e) {
                        $this->etapeNoDr++;
                    }
                }
            }
            if (isset($item->gamma) && $item->gamma == 'INSCRIT') {
                $this->nbInscritGamma++;
            }
        }
    }
}
