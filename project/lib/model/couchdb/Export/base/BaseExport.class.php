<?php

abstract class BaseExport extends acCouchdbDocument 
{

    public function getDocumentDefinitionModel() {
    	
        return 'Export';
    }

}