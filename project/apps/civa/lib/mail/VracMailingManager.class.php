<?php

class VracMailingManager
{
    protected $mailer = null;
    protected $partial_function = null;
    protected Vrac $vrac;

    public function __construct($mailer, $partial_fct, $vrac)
    {
        $this->mailer = $mailer;
        $this->partial_function = $partial_fct;
        $this->vrac = $vrac;
    }

    public function sendMail($visualisation = true)
    {
        $message = $this->getMail($visualisation);
        $this->mailer->send($message);

        return true;
    }

    public function getMail($visualisation = true)
    {
        $subject = 'CIVA - Validation de votre contrat ' . $this->vrac->getTypeArchive();
        $body = $this->getMessageValidation($this->vrac);

        $message = Swift_Message::newInstance()
                ->setFrom(array(sfConfig::get('app_email_from') => sfConfig::get('app_email_from_name')))
                ->setTo('test@test.fr')
                ->setSubject($subject)
                ->setBody($body);

        return $message;
    }

    protected function getMessageValidation()
    {
        return <<<EOM
Contrat pluriannuel {$this->vrac->getTypeArchive()} du {$this->vrac->valide->date_saisie}
Campagnes d'application : {$this->vrac->getCampagne()}

Vendeur : {$this->vrac->vendeur->raison_sociale}
Acheteur : {$this->vrac->acheteur->raison_sociale}

Un projet de contrat a été créé par {$this->vrac->vendeur->raison_sociale} et attend votre validation.

Pour le visualiser et le valider, cliquez sur le lien suivant : url

Pour toutes questions, veuillez contacter l'interlocuteur commercial, responsable du contrat.

--
L'application de télédéclaration des contrats du CIVA
EOM;
    }

    protected function getPartial($templateName, $vars = null) {
      return call_user_func_array($this->partial_function, array($templateName, $vars));
    }
}
