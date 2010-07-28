<?php

class RecoltantClient extends sfCouchdbClient {
    public function retrieveByCvi($cvi) {
        return parent::retrieveDocumentById('REC-'.$cvi);
    }
}
