<?php
class sfCASRequiredFilter extends sfBasicSecurityFilter
{
  public function execute ($filterChain)
  {
    if ($this->isFirstCall()) {
        error_reporting(E_ALL);
        require_once(sfConfig::get('sf_lib_dir').'/vendor/phpCAS/CAS.class.php');
        //phpCAS::setDebug();

        phpCAS::client(CAS_VERSION_2_0,$this->getParameter('server_domain'), $this->getParameter('server_port'), $this->getParameter('server_path'), false);

        phpCAS::setNoCasServerValidation();

        $this->getContext()->getLogger()->debug('{sfCASRequiredFilter} about to force auth');
        phpCAS::forceAuthentication();
        $this->getContext()->getLogger()->debug('{sfCASRequiredFilter} auth is good');
        
        $this->getContext()->getUser()->signInWithCas(phpCAS::getUser());
    }

    // Execute next filter in the chain
    $filterChain->execute();

  }
}
?>