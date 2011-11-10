<?php
/**
 * acExceptionNotifierPluginConfiguration represents the configuration for acExceptionNotifierPlugin plugin.
 *
 * @package    acExceptionNotifier
 * @subpackage config
 * @author     Jean-Baptiste Le Metayer <lemetayer.jb@gmail.com>
 * @author     Vincent Laurent <vince.laurent@gmail.com>
 * @version    0.1
 */
class acExceptionNotifierPluginConfiguration extends sfPluginConfiguration
{
  /**
   * Initializes acExceptionNotifierPlugin.
   * 
   * This method connects a listener on application exceptions
   * @access public
   * 
   */
	public function initialize()
	{
		if (sfConfig::get('app_ac_exception_notifier_enabled')) {
			$this->dispatcher->connect('application.throw_exception', array(sfConfig::get('app_ac_exception_notifier_class'), 'exceptionHandler'));
		}
	}
}