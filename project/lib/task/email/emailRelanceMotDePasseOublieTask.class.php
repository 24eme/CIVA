<?php

class emailRelanceMotDePasseOublieTask extends sfBaseTask
{
  protected function configure()
  {
    // // add your own arguments here
    $this->addArguments(array(
    //   new sfCommandArgument('my_arg', sfCommandArgument::REQUIRED, 'My argument'),
    ));

    $this->addOptions(array(
      new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name', 'civa'),
      new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'prod'),
      new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'default'),
      // add your own options here
    ));

    $this->namespace        = 'email';
    $this->name             = 'relance-mot-de-passe-oublie';
    $this->briefDescription = '';
    $this->detailedDescription = <<<EOF
The [emailRelanceMotDePasseOublieTask|INFO] task does things.
Call it with:

  [php symfony emailRelanceMotDePasseOublieTask|INFO]
EOF;
  }

  protected function execute($arguments = array(), $options = array())
  {
    set_time_limit('240');
    ini_set('memory_limit', '512M');
    
    // initialize the database connection
    $databaseManager = new sfDatabaseManager($this->configuration);
    $connection = $databaseManager->getDatabase($options['connection'])->getConnection();

    $nb_item = 0;
    $nb_email_send = 0;
    $comptes = sfCouchdbManager::getClient("_Compte")->getAll(sfCouchdbClient::HYDRATE_JSON);
    foreach ($comptes as $compte) {
        if ($compte->statut == "MOT_DE_PASSE_OUBLIE") {
            $this->logSection($compte->_id, $compte->email);
            $nb_item++;
            
            try {
              $this->getMailer()->composeAndSend(array("ne_pas_repondre@civa.fr" => "Webmaster Vinsalsace.pro"), 
                                               $compte->email, 
                                               "CIVA - Mot de passe oublié", 
                                               $this->getMessageBody($compte));
              $nb_email_send++;

            } catch (Exception $exc) {
                
            }
        }
    }

    $this->logSection('Emails have been sended', sprintf('%d / %d envoyés', $nb_email_send,  $nb_item));
  }

  protected function getMessageBody($compte) {
      
      $lien = sfConfig::get('app_base_url') . $this->generateUrl("compte_mot_de_passe_oublie_login", array("login" => $compte->login, "mdp" => str_replace("{OUBLIE}", "", $compte->mot_de_passe)));
      $this->log($lien);
      return "Bonjour " . $compte->nom . ",
          
Vous avez effectué il y a peu une demande de mot de passe oublié, mais n'avez pas encore redéfini votre mot de passe.

Il est toujours possible de le faire en cliquant sur le lien suivant : " . $lien . "
    
Cordialement,

Le CIVA";
  }
  
  protected function generateUrl($route, $params = array(), $absolute = false)
  {
    return $this->getRouting()->generate($route, $params, $absolute);
  }
}
