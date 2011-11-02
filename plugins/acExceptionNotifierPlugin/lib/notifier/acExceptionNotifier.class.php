<?php
/**
 * acExceptionNotifier allows you to  handling the application exception
 *
 * @package    acExceptionNotifier
 * @subpackage lib
 * @author     Jean-Baptiste Le Metayer <lemetayer.jb@gmail.com>
 * @version    0.1
 */
abstract class acExceptionNotifier
{
	protected static function renderTraces(acException $acException)
	{
		$traces  = implode('<br />', $acException->getExceptionInformations());
		$traces .= implode('<br />', $acException->getExceptionTraces());
		$traces .= '<hr />';
		$traces .= implode('<br />', $acException->getDebugTraces());
		return $traces;
	}
}