<?php

class acExceptionAttachedEmailNotifier extends acExceptionNotifier
{
	protected static function notify($message)
	{
		return acEmailNotifier::exceptionAttachedEmailNotifier($message);
	}
}