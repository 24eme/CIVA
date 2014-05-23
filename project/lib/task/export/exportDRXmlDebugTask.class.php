<?php

class exportDRXmlDebugTask extends sfBaseTask
{
  protected function configure()
  {
    // // add your own arguments here
    $this->addArguments(array(
       new sfCommandArgument('doc_id', sfCommandArgument::REQUIRED, 'ID du document'),
       new sfCommandArgument('destinataire', sfCommandArgument::REQUIRED, 'Destinataire'),
    ));

    $this->addOptions(array(
      new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name', 'civa'),
      new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'prod'),
      new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'default'),
      // add your own options here
      
    ));

    $this->namespace        = 'export';
    $this->name             = 'dr-xml-debug';
    $this->briefDescription = '';
    $this->detailedDescription = <<<EOF
The [exportDRXml|INFO] task does things.
Call it with:

  [php symfony exportDRXml|INFO]
EOF;
  }

  protected function execute($arguments = array(), $options = array())
  {
    // initialize the database connection
    $databaseManager = new sfDatabaseManager($this->configuration);
    $connection = $databaseManager->getDatabase($options['connection'])->getConnection();

    sfContext::createInstance($this->configuration);

    if (!in_array($arguments['destinataire'], array("Civa", "Douane"))) {
        throw new sfCommandException("Le destinataire est invalide !");
    }


    $dr = acCouchdbManager::getClient("DR")->find($arguments['doc_id']);
    $config = ConfigurationClient::getConfiguration($dr->campagne);
    $produits = $config->getProduitsLibellesByCodeDouane();
    $produits_preg = array();
    foreach($produits as $code => $libelle) {
      $produits_preg["/<!--".$code."[ ]*-->/"] = "<!--".$libelle."-->";
    }

    $xml = new ExportDRXml($dr, array($this, 'getPartial'), $arguments['destinataire']);
    $content = utf8_encode($xml->getContent());

    $content = preg_replace("/(<colonne>)/", "\\1\n", $content);
    $content = preg_replace("/(<\/colonne>)/", "\\1", $content);
    $content = preg_replace("/(<mentionVal>)/", "\t\\1", $content);
    $content = preg_replace("/(<\/mentionVal>)/", "\\1\n", $content);
    $content = preg_replace("/(<colonneAss>)/", "\t\\1\n", $content);
    $content = preg_replace("/(<\/colonneAss>)/", "\t\\1\n", $content);
    $content = preg_replace("/(<\/colonne>)/", "\n\\1", $content);
    $content = preg_replace("/(<exploitant>)/", "\t\\1\n", $content);
    $content = preg_replace("/(<\/exploitant>)/", "\t\\1\n", $content);
    $content = preg_replace("/(<\/L[0-9]+>)/", "\\1\n", $content);
    $content = preg_replace("/(<L[0-9]+>)/", "\t\\1", $content);

    $content = preg_replace("/<L1>([0-9A-Z ]+)<\/L1>/", "\\0<!--\\1-->", $content);
    $content = preg_replace(array_keys($produits_preg), array_values($produits_preg), $content);

    $content = preg_replace("/<\/L4>/", "\\0<!--Superficie-->", $content);
    $content = preg_replace("/<\/L5>/", "\\0<!--Volume total-->", $content);
    $content = preg_replace("/<\/L6>/", "\\0<!--Volume negoces-->", $content);
    $content = preg_replace("/<\/L7>/", "\\0<!--Volume mouts-->", $content);
    $content = preg_replace("/<\/L8>/", "\\0<!--Volume coopératives-->", $content);
    $content = preg_replace("/<\/L9>/", "\\0<!--Volume sur place-->", $content);
    $content = preg_replace("/<\/L10>/", "\\0<!--Volume non négoce-->", $content);
    $content = preg_replace("/<\/L11>/", "\\0<!--HS-->", $content);
    $content = preg_replace("/<\/L12>/", "\\0<!--HS-->", $content);
    $content = preg_replace("/<\/L13>/", "\\0<!--HS-->", $content);
    $content = preg_replace("/<\/L14>/", "\\0<!--HS-->", $content);
    $content = preg_replace("/<\/L15>/", "\\0<!--Volume revendiqué cave et sur place-->", $content);
    $content = preg_replace("/<\/L16>/", "\\0<!--Usages industriels-->", $content);
    $content = preg_replace("/<\/L17>/", "\\0<!--HS-->", $content);
    $content = preg_replace("/<\/L18>/", "\\0<!--HS-->", $content);
    $content = preg_replace("/<\/L19>/", "\\0<!--HS-->", $content);

    echo $content;
  }

  public function getPartial($templateName, $vars = null) {
        $this->configuration->loadHelpers('Partial');
        $vars = null !== $vars ? $vars : $this->varHolder->getAll();
        return get_partial($templateName, $vars);
  }
}
