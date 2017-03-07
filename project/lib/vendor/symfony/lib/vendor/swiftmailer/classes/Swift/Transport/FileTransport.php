<?php

/**
 * Pretends messages have been sent, but just ignores them.
 * @package Swift
 */
class Swift_Transport_FileTransport implements Swift_Transport
{

  /** path where store file */
  private $_path = '';

  /** The event dispatcher from the plugin API */
  private $_eventDispatcher;

  /**
   * Constructor.
   */
  public function __construct(Swift_Events_EventDispatcher $eventDispatcher)
  {
    $this->_eventDispatcher = $eventDispatcher;
  }

  /**
   * Tests if this Transport mechanism has started.
   *
   * @return boolean
   */
  public function isStarted()
  {
    return true;
  }

  /**
   * Starts this Transport mechanism.
   */
  public function start()
  {
  }

  /**
   * Stops this Transport mechanism.
   */
  public function stop()
  {
  }

  /**
   * Set path where store file
   *
   * @param string $params
   */
  public function setPath($path)
  {
    $this->_path = $path;
    return $this;
  }

  /**
   * Get path where store file
   *
   * @return string
   */
  public function getPath()
  {
    return $this->_path;
  }

  /**
   * Sends the given message.
   *
   * @param Swift_Mime_Message $message
   * @param string[] &$failedRecipients to collect failures by-reference
   *
   * @return int The number of sent emails
   */
  public function send(Swift_Mime_Message $message, &$failedRecipients = null)
  {
    if ($evt = $this->_eventDispatcher->createSendEvent($this, $message))
    {
      $this->_eventDispatcher->dispatchEvent($evt, 'beforeSendPerformed');
      if ($evt->bubbleCancelled())
      {
        return 0;
      }
    }

    if(!is_dir($this->getPath())) {
        throw new Exception(sprintf("The path %s is not a directory or doesn't exist", $this->getPath()));
    }

    file_put_contents(preg_replace("|/$|", "", $this->getPath()) . "/" . date("YmdHis")."_".md5(uniqid()).".eml", $message->toString());

    if ($evt)
    {
      $evt->setResult(Swift_Events_SendEvent::RESULT_SUCCESS);
      $this->_eventDispatcher->dispatchEvent($evt, 'sendPerformed');
    }

    return 1;
  }

  /**
   * Register a plugin.
   *
   * @param Swift_Events_EventListener $plugin
   */
  public function registerPlugin(Swift_Events_EventListener $plugin)
  {
    $this->_eventDispatcher->bindEventListener($plugin);
  }
}
