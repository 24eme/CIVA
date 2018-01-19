<?php

class compte_autocompleteActions extends sfActions
{

    public function executeAll(sfWebRequest $request) {
	    $interpro = $request->getParameter('interpro_id');
	    $q = $request->getParameter('q');
	    $limit = $request->getParameter('limit', 100);
	    $comptes = CompteAllView::getInstance()->findByInterpro($interpro, $q, $limit);
	    $jsonElastic = $this->matchCompte($comptes, $q, $limit);

	    return $this->renderText(json_encode($jsonElastic));
  	}

    protected function matchCompte($view_res, $term, $limit) {
        $json = array();
        foreach ($view_res as $key => $one_row) {
            $text = CompteAllView::getInstance()->makeLibelle($one_row->key);

            if (Search::matchTerm($term, $text)) {
                $json[$one_row->id] = $text;
            }

            if (count($json) >= $limit) {
                break;
            }
        }
        return $json;
    }

}
