<?php

class acExceptionAttachedEmailNotifier extends acExceptionNotifier
{
	public static function exceptionHandler(sfEvent $event)
	{	
		//!sfConfig::get('sf_debug') && 
		if (is_object($exception = $event->getSubject())) {
			self::notify(self::renderTraces(new acException($exception, sfConfig::get('app_ac_exception_notifier_class'))));
		}
	}
	protected static function notify($message)
	{
		return acEmailNotifier::exceptionAttachedEmailNotifier($message);
	}
}