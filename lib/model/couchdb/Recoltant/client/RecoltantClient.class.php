<?php

class RecoltantClient extends sfCouchdbClient {
    public function retrieveByCvi($cvi) {
        return parent::retrieveDocById('REC-'.$cvi);
    }
}
