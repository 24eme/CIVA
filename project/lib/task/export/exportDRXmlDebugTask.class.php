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

    $xml = new ExportDRXml($dr, array($this, 'getPartial'), $arguments['destinataire']);
    $content = utf8_encode($xml->getContent());
    $content = preg_replace("/(<colonne>)/", "\\1\n\t", $content);
    $content = preg_replace("/(<\/colonne>)/", "\n\\1", $content);
    $content = preg_replace("/(<colonneAss>)/", "\\1\n\t", $content);
    $content = preg_replace("/(<\/colonneAss>)/", "\\1\n\t", $content);
    $content = preg_replace("/(<\/colonne>)/", "\n\\1", $content);
    $content = preg_replace("/(<exploitant>)/", "\\1\n\t", $content);
    $content = preg_replace("/(<\/exploitant>)/", "\\1\n\t", $content);
    $content = preg_replace("/(<\/L[0-9]+>)/", "\\1\n\t", $content);
    $content = preg_replace("/(<L[0-9]+>)/", "\t\\1", $content);

    echo $content;
  }

  public function getPartial($templateName, $vars = null) {
        $this->configuration->loadHelpers('Partial');
        $vars = null !== $vars ? $vars : $this->varHolder->getAll();
        return get_partial($templateName, $vars);
  }
}
