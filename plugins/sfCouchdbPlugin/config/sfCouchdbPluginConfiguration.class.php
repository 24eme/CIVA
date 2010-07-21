<?php

class sfCouchDbPluginConfiguration extends sfPluginConfiguration
{
  public function initialize()
  {
      sfConfig::set('sf_orm', 'couchdb');
  }
}
