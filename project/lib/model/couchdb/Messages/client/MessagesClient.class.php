<?php

class MessagesClient extends acCouchdbClient {
  public static $messages = null;

  public static function getInstance() {
    
    return acCouchdbManager::getClient("Messages");
  }

  public function retrieveMessages() {
    if (!self::$messages)
      self::$messages = parent::find('MESSAGES');
    return self::$messages;
  }

  public function getMessage($id) {
    try {
      return $this->retrieveMessages()->{$id};
    }catch(Exception $e) {
      return "PAS DE MESSAGE TROUVÉ !!";
    }
  }
}
