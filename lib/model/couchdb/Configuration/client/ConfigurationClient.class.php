<?php

class ConfigurationClient extends sfCouchdbClient {
    public static function retrieveConfiguration() {
      return parent::retrieveDocumentById('CONFIGURATION');
    }
}
