<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of class DRSendMailsTask
 * @author mathurin
 */
class DRSendMailsTask extends sfBaseTask
{

    protected function configure()
    {
        // // add your own arguments here
        $this->addArguments(array(
        ));

        $this->addOptions(array(
            new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name', 'civa'),
            new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'prod'),
            new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'default'),
        ));

        $this->namespace = 'dr';
        $this->name = 'send-mails';
        $this->briefDescription = '';
        $this->detailedDescription = <<<EOF
The [dr:send-mails|INFO] task does things.
Call it with:

  [php symfony dr:send-mails|INFO]
EOF;
    }

    protected function execute($arguments = array(), $options = array())
    {
        $databaseManager = new sfDatabaseManager($this->configuration);
        $connection = $databaseManager->getDatabase($options['connection'])->getConnection();
        sfContext::createInstance($this->configuration);

        $docs_to_send_mail = DRAttenteEnvoiMailView::getInstance()->findAll();
        foreach ($docs_to_send_mail as $doc_result) {
            $doc = $mailer = null;

            switch ($doc_result->key['type']) {
                case DRClient::TYPE_MODEL:
                    $doc = DRClient::getInstance()->find($doc_result->id);
                    $mailer = new RecolteMailingManager(
                        $this->getMailer(), [$this, 'getPartial'], $doc, $doc->getEtablissement(), $doc->campagne
                    );
                    break;
                /* case VracClient::TYPE_MODEL: */
                /*     $doc = VracClient::getInstance()->find($doc_result->id); */
                /*     $mailer = new VracMailingManager( */
                /*         $this->getMailer(), [$this, 'getPartial'], $doc */
                /*     ); */
                /*     break; */
            }

            if (! $doc) {
                echo $doc_result->id . " non trouvé.".PHP_EOL;
                continue;
            }

            if(! $doc->exist('en_attente_envoi') || ! $doc->en_attente_envoi) {
                continue;
            }

            // on regarde si le delai d'attente est passé
            try {
                $date = new DateTimeImmutable($doc->en_attente_envoi);
                if ($date < new DateTimeImmutable()) {
                    continue;
                }
            } catch (\Exception $e) {
                continue; // DR
            }

            try {
                $mailer->sendMail(false);
                if($doc->hasAutorisation(DRClient::AUTORISATION_ACHETEURS)) {
                    $mailer->sendAcheteursMails();
                }
                echo $doc->_id.":Email envoyé à ".$doc->getEtablissement()->getEmailTeledeclaration()."\n";
            } catch(Exception $e) {
                echo $doc->_id.":".$e->getMessage()."\n";
                continue;
            }

            $doc->emailSended();
            $doc->save();
        }
    }

    public function getPartial($templateName, $vars = null) {
        $this->configuration->loadHelpers('Partial');
        $vars = null !== $vars ? $vars : $this->varHolder->getAll();
        return get_partial($templateName, $vars);
    }

}
