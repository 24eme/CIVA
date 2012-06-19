<?php

abstract class BaseExport extends sfCouchdbDocument 
{

    public function getDocumentDefinitionModel() {
    	
        return 'Export';
    }

}