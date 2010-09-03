<?php

class MessagesClient extends sfCouchdbClient {
  public static $messages = null;

  public function retrieveMessages() {
    if (!self::$messages)
      self::$messages = parent::retrieveDocumentById('MESSAGES');
    return self::$messages;
  }

  public function getMessage($id) {
    return $this->retrieveMessages()->{$id};
  }
}
