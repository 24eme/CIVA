<?php

class acExceptionEmailNotifier extends acExceptionNotifier
{
	protected static function notify($message)
	{
		return acEmailNotifier::exceptionEmailNotifier($message);
	}
}