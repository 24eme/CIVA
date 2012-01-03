<?php

class DRUpdateTask extends sfBaseTask
{

    protected function configure()
    {
        // // add your own arguments here
        $this->addArguments(array(
            new sfCommandArgument('campagne', sfCommandArgument::REQUIRED, 'Campagne'),
        ));

        $this->addOptions(array(
            new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name', 'civa'),
            new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
            new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'default'),
            new sfCommandOption('cvi', null, sfCommandOption::PARAMETER_REQUIRED, 'Cvi', null),
            new sfCommandOption('devalidation', null, sfCommandOption::PARAMETER_REQUIRED, 'Dévalidation', false)
        ));

        $this->namespace = 'dr';
        $this->name = 'update';
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

        if (!$options['cvi']) {
            $dr_ids = sfCouchdbManager::getClient("DR")->getAllByCampagne($arguments['campagne'], sfCouchdbClient::HYDRATE_ON_DEMAND)->getIds();
        } else {
            $dr_ids = array('DR-'.$options['cvi'].'-'.$arguments['campagne']);
        }

        foreach ($dr_ids as $id) {
            $dr = sfCouchdbManager::getClient()->retrieveDocumentById($id);

            $data = $dr->getData();

            $dr->update();

            $modifiee = $dr->get('modifiee');
            
            if ($options['devalidation']) {
                $dr->remove('modifiee');
            }

            $dr->update();

            if ($options['devalidation']) {
                $dr->add('modifiee', $modifiee);
            }

            if ($dr->isModified()) {
                $this->logSection('updated', $dr->get('_id'));
            }
            
            $dr->save();
        }
        // add your code here
    }

}
