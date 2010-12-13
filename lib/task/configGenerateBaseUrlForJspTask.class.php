<?php

class configGenerateBaseUrlForJspTask extends sfBaseTask
{
  protected function configure()
  {
    // // add your own arguments here
    // $this->addArguments(array(
    //   new sfCommandArgument('my_arg', sfCommandArgument::REQUIRED, 'My argument'),
    // ));

    $this->addOptions(array(
      new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name', 'civa'),
      new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
      new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'couchdb'),
      // add your own options here
    ));

    $this->namespace        = 'config';
    $this->name             = 'generate-base-url-for-jsp';
    $this->briefDescription = '';
    $this->detailedDescription = <<<EOF
The [generateBaseUrlForJsp|INFO] task does things.
Call it with:

  [php symfony generateBaseUrlForJsp|INFO]
EOF;
  }

  protected function execute($arguments = array(), $options = array())
  {
    
    // add your code here
    $base_url = sfConfig::get('app_base_url');
    $fichier_conf = sfConfig::get('app_tomcat_dir').'/base_url.conf';
    $fp = fopen($fichier_conf,"w");
    if($fp){
        fputs($fp, $base_url);
        fclose($fp);
    }
    

   

  }
}
