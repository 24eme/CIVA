<?php

class DRChangeDateTask extends sfBaseTask
{

    protected function configure()
    {
        $this->addOptions(array(
            new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name', 'civa'),
            new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
            new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'default'),
            new sfCommandOption('date', null, sfCommandOption::PARAMETER_REQUIRED, 'Date to be changed', null)
        ));

        $this->namespace = 'dr';
        $this->name = 'changeDate';
        $this->briefDescription = '';
        $this->detailedDescription = <<<EOF
The [maintenance:DRVT|INFO] task does things.
Call it with:

  [php symfony maintenance:DRVT|INFO]
EOF;
    }

    protected function execute($arguments = array(), $options = array())
    {

        // initialize the database connection
        $databaseManager = new sfDatabaseManager($this->configuration);
        $connection = $databaseManager->getDatabase($options['connection'])->getConnection();

	$date = $options['date'];
	if(!preg_match('/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/', $date)) {
	  throw new sfException("wrong date format");
	  }

        $dr_ids = sfCouchdbManager::getClient()->group(true)
	  ->group_level(2)
	  ->startkey(array(true, true))
	  ->endkey(array(true, true, array()))
	  ->getView("STATS", "DR");

        foreach ($dr_ids as $id) {
            $dr = sfCouchdbManager::getClient()->retrieveDocumentById($id);

	    if (!$dr->isValidee())
	      continue;

	    if (!$dr->validee >= $date)
	      continue;

	    echo $dr->_id." changed\n";
	    $dr->validee = $date;
	    $dr->save();
        }
    }

}

