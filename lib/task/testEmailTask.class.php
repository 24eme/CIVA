<?php

class testEmailTask extends sfBaseTask
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
      new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'default'),
      // add your own options here
    ));

    $this->namespace        = '';
    $this->name             = 'testEmail';
    $this->briefDescription = '';
    $this->detailedDescription = <<<EOF
The [testEmail|INFO] task does things.
Call it with:

  [php symfony testEmail|INFO]
EOF;
  }

  protected function execute($arguments = array(), $options = array())
  {
    // initialize the database connection
    $databaseManager = new sfDatabaseManager($this->configuration);
    $connection = $databaseManager->getDatabase($options['connection'])->getConnection();

    $mess = "test email";
    $obj_email = "Test Objet";

    $message = $this->getMailer()->compose('ne_pas_repondre@civa.fr',
								 'vince.laurent@gmail.com',
								 'CIVA - '.$obj_email,
								 $mess
								 );
			  $this->log(var_dump($this->getMailer()->send($message)));

    // add your code here
  }
}
