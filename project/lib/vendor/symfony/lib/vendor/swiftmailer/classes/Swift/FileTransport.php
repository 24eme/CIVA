<?php

/**
 * Store mail in a file instead of send it
 * @package Swift
 */
class Swift_FileTransport extends Swift_Transport_FileTransport
{
  /**
   * Create a new FileTransport.
   */
  public function __construct($path = "")
  {
      call_user_func_array(
        array($this, 'Swift_Transport_FileTransport::__construct'),
        Swift_DependencyContainer::getInstance()
          ->createDependenciesFor('transport.file')
        );

        $this->setPath($path);
  }

  /**
   * Create a new NullTransport instance.
   * @return Swift_NullTransport
   */
  public static function newInstance()
  {
    return new self($this->getPath());
  }
}
