<?php

/**
 * acExceptionNotifierPluginConfiguration represents a configuration for acExceptionNotifierPlugin plugin.
 *
 * @package    acExceptionNotifier
 * @subpackage config
 * @author     Jean-Baptiste Le Metayer <lemetayer.jb@gmail.com>
 * @version    0.1
 */
class acExceptionNotifierPluginConfiguration extends sfPluginConfiguration
{

  /**
   * Initializes acExceptionNotifierPlugin.
   * 
   * This method connects a listener on application exception
   * 
   * @return boolean|null
   */
  public function initialize()
  {
      $this->dispatcher->connect('application.throw_exception', array(sfConfig::get('app_ac_exception_notifier_class'), 'exceptionHandler'));
  }
}