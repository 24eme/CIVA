<?php

class acExceptionEmailNotifier extends acExceptionNotifier
{
	public static function exceptionHandler(sfEvent $event)
	{	
		if (!sfConfig::get('sf_debug') && is_object($exception = $event->getSubject())) {
			self::notify(self::renderTraces(new acException($exception, sfConfig::get('app_ac_exception_notifier_format'))));
		}
	}
	protected static function notify($message)
	{
		return acEmailNotifier::exceptionEmailNotifier($message);
	}
}