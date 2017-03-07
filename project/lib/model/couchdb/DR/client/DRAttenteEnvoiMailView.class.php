<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of class AttenteEnvoiMailView
 * @author mathurin
 */
class DRAttenteEnvoiMailView extends acCouchdbView
{

	const KEY_CAMPAGNE = 0;
	const KEY_CVI = 1;
        const KEY_MAIL = 2;

    public static function getInstance()
    {
        return acCouchdbManager::getView('DR', 'AttenteEnvoiMail', 'Dr');
    }

    public function findAll() 
    {
    	return $this->client->getView($this->design, $this->view)->rows;
    }
}
