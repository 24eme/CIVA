<?php

/*
 * This file is part of SwiftMailer.
 * (c) 2004-2009 Chris Corbyn
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

//@require 'Swift/Mime/SimpleMessage.php';
//@require 'Swift/MimePart.php';
//@require 'Swift/DependencyContainer.php';

/**
 * The Message class for building emails.
 * @package Swift
 * @subpackage Mime
 * @author Chris Corbyn
 */
class Swift_Message extends Swift_Mime_SimpleMessage
{
  
  /**
   * Create a new Message.
   * Details may be optionally passed into the constructor.
   * @param string $subject
   * @param string $body
   * @param string $contentType
   * @param string $charset
   */
  public function __construct($subject = null, $body = null,
    $contentType = null, $charset = null)
  {
    call_user_func_array(
      array($this, 'Swift_Mime_SimpleMessage::__construct'),
      Swift_DependencyContainer::getInstance()
        ->createDependenciesFor('mime.message')
      );
    
    if (!isset($charset))
    {
      $charset = Swift_DependencyContainer::getInstance()
        ->lookup('properties.charset');
    }
    $this->setSubject($subject);
    $this->setBody($body);
    $this->setCharset($charset);
    if ($contentType)
    {
      $this->setContentType($contentType);
    }
  }
  
  /**
   * Create a new Message.
   * @param string $subject
   * @param string $body
   * @param string $contentType
   * @param string $charset
   * @return Swift_Mime_Message
   */
  public static function newInstance($subject = null, $body = null,
    $contentType = null, $charset = null)
  {
    return new self($subject, $body, $contentType, $charset);
  }
  
  /**
   * Add a MimePart to this Message.
   * @param string|Swift_OutputByteStream $body
   * @param string $contentType
   * @param string $charset
   */
  public function addPart($body, $contentType = null, $charset = null)
  {
    return $this->attach(Swift_MimePart::newInstance(
      $body, $contentType, $charset
      ));
  }

  public function __wakeup()
  {
    Swift_DependencyContainer::getInstance()->createDependenciesFor('mime.message');
  }

  public function copy() {
      $message = self::newInstance($this->getSubject(), $this->getBody(), $this->getContentType(), $this->getCharset());
      $message->setFrom($this->getFrom());
      $message->setTo($this->getTo());
      $message->setCc($this->getCc());
      $message->setBcc($this->getBcc());
      $message->setReplyTo($this->getReplyTo());
      $message->setSender($this->getSender());
      $message->setChildren($this->getChildren());

      return $message;
  }

}
