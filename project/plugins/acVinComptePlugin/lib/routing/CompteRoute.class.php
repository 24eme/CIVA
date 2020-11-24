<?php

class CompteRoute extends sfObjectRoute implements InterfaceCompteRoute {

    protected $compte = null;

    protected function getObjectForParameters($parameters = null) {
      $this->compte = CompteClient::getInstance()->find("COMPTE-".$parameters['identifiant']);

      self::autoSignin($this->compte);
      return $this->compte;
    }

    public static function autoSignin($compte) {
         if(!sfContext::getInstance()->getUser()->hasCredential(CompteSecurityUser::CREDENTIAL_ADMIN)) {
             return;
         }

         $societe = $compte->getSociete();

         if($societe->_id == sfContext::getInstance()->getUser()->getCompte()->id_societe) {
             return;
         }

         sfContext::getInstance()->getUser()->signInCompteUsed($compte);
         sfContext::getInstance()->getUser()->signOutTiers();
         sfContext::getInstance()->getUser()->signInTiers($societe);
    }

    protected function doConvertObjectToArray($object = null) {
      $this->compte = $object;
      return array("identifiant" => $object->getIdentifiant());
    }

    public function getCompte() {
      if (!$this->compte) {
           $this->compte = $this->getObject();
      }
      return $this->compte;
    }
}
