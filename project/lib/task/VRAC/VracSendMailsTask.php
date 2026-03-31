<?php

class VracSendMailsTask extends sfBaseTask
{
    protected function configure()
    {
        $this->addArguments([]);

        $this->addOptions([
            new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name', 'civa'),
            new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'prod'),
            new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'default'),
        ]);

        $this->namespace = 'vrac';
        $this->name = 'send-mails';
        $this->briefDescription = 'Envoi les mails des contrats en attente de transmission';
        $this->detailedDescription = <<<EOF
The [vrac:send-mails|INFO] task does things.
Call it with:

  [php symfony vrac:send-mails|INFO]
EOF;
    }

    protected function execute($arguments = array(), $options = array())
    {
        $databaseManager = new sfDatabaseManager($this->configuration);
        $connection = $databaseManager->getDatabase($options['connection'])->getConnection();
        sfContext::createInstance($this->configuration);

        $queue = VracContratsView::getInstance()->findByStatut(Vrac::STATUT_PROJET_ATTENTE_TRANSMISSION);

        foreach ($queue as $doc_result) {
            $vrac = null;
            $vrac = VracClient::getInstance()->find($doc_result->id);

            if (! $vrac) {
                echo $doc_result->id . " non trouvé.".PHP_EOL;
                continue;
            }

            try {
                $vrac->validate();
                VracMailer::getInstance()->sendMailsByStatutsChanged($vrac);
            } catch (\Exception $e) {
                echo $doc_result->_id.":".$e->getMessage().PHP_EOL;
                continue;
            }
        }
    }
}
