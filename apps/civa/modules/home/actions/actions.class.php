<?php

/**
 * home actions.
 *
 * @package    civa
 * @subpackage home
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class homeActions extends sfActions {

    /**
     * Executes index action
     *
     * @param sfRequest $request A request object
     */
    public function executeIndex(sfWebRequest $request) {
        $detail = new DRRecolteAppellationCepageDetail();

        $doc = new DR();
        $doc->load(sfCouchdbManager::getClient()->getDoc('DR-6701800180-2009'));
        //print_r($doc->getData());

        $doc2 = new DR();
        $doc2->load(sfCouchdbManager::getClient()->getDoc('DR-6701800180-2009'));

        $this->form = new DRRecolteAppellationCepageDetailForm($doc2, $doc2->getRecolteDetail('test', 'test', 1));

        if ($request->isMethod(sfWebRequest::POST)) {
            $this->form->bind($request->getParameter($this->form->getName()));

            if ($this->form->isValid()) {
                $this->form->save();
            }
        }
    }

}
