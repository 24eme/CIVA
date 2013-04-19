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
      
      $strdate = $options['date'];
      if(!preg_match('/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/', $strdate)) {
	throw new sfException("wrong date format");
      }
      $date = strtotime($strdate);
      
      $drs = acCouchdbManager::getClient()->reduce(false)->getView("STATS", "DR")->rows;
      
      foreach ($drs as $obj) {
	
	if (!$obj->key[0])
	  continue;
	$dr_id = $obj->id;
	$dr_date = $obj->key[2];
	
	if (strtotime($dr_date) <= $date)
	  continue;
	
	$dr = acCouchdbManager::getClient()->find($dr_id);
	
	echo $dr->_id." changed (previous date $dr_date)\n";
	$dr->validee = $strdate;
	$dr->save();
      }
    }
    
}

