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

        $context = sfContext::createInstance($this->configuration);

        $routing = $context->getRouting();
        $_options = $routing->getOptions();
        $_options['context']['prefix'] = ""; // "/frontend_dev.php" for dev; or "" for prod
        $_options['context']['host'] = str_replace(['https://', 'http://'], '', sfConfig::get('app_base_url'));
        $routing->initialize($this->dispatcher, $routing->getCache(), $_options);
        $context->getConfiguration()->loadHelpers('Partial');
        $context->set('routing', $routing);

        $queue = VracTousView::getInstance()->findAll();

        foreach ($queue as $doc_result) {
            if ($doc_result->key[3] !== Vrac::STATUT_PROJET_ATTENTE_TRANSMISSION) {
                continue;
            }

            $vrac = null;
            $vrac = VracClient::getInstance()->find($doc_result->id);

            if (! $vrac) {
                echo $doc_result->id . " non trouvé.".PHP_EOL;
                continue;
            }

            try {
                $vrac->validate();
                $vrac->save();
                VracMailer::getInstance()->sendMailsByStatutsChanged($vrac);
            } catch (\Exception $e) {
                echo $doc_result->_id.":".$e->getMessage().PHP_EOL;
                continue;
            }
        }
    }
}
