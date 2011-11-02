<?php
/**
 * acException allows you to render the application exception
 *
 * @package    acExceptionNotifier
 * @subpackage exception
 * @author     Jean-Baptiste Le Metayer <lemetayer.jb@gmail.com>
 * @version    0.1
 */
class acException extends sfException
{
	protected $exception;
	protected $format;
	
	public function __construct($exception, $format = 'html')
	{
		$this->setException($exception);
		$this->setFormat($format);
	}
	public function setException($exception)
	{
		$this->exception = $exception;
	}
	public function getException()
	{
		return $this->exception;
	}
	public function setFormat($format)
	{
		$this->format = $format;
	}
	public function getFormat()
	{
		return $this->format;
	}
	public function getExceptionInformations()
	{
		$exception = $this->getException();
		$informations = array();
   		$informations[] = '<strong>500 | Internal Server Error | '.get_class($exception).'</strong>';
   		$message = (null === $this->getException()->getMessage()) ? 'n/a' : $exception->getMessage();
		$informations[] = '<span style="display: block; background-color: #EEEEEE; border-radius: 10px 10px 10px 10px; margin: 10px 0px; padding: 10px;">'.$message.'</span>';
   		return $informations;
	}
	public function getExceptionTraces()
	{
		return self::getTraces($this->getException(), $this->getFormat());
	}
	public function getDebugTraces()
	{
		$traces = array();
	    if (class_exists('sfContext', false) && sfContext::hasInstance())
	    {
	      $context = sfContext::getInstance();
	      $traces[] = '<strong>Symfony settings :</strong>';
	      $traces[] = $settingsTable = self::formatArrayAsHtml(sfDebug::settingsAsArray());
	      $traces[] = '<strong>Request :</strong>';
	      $traces[] = $requestTable  = self::formatArrayAsHtml(sfDebug::requestAsArray($context->getRequest()));
	      $traces[] = '<strong>Response :</strong>';
	      $traces[] = $responseTable = self::formatArrayAsHtml(sfDebug::responseAsArray($context->getResponse()));
	      $traces[] = '<strong>User :</strong>';
	      $traces[] = $userTable     = self::formatArrayAsHtml(sfDebug::userAsArray($context->getUser()));
	      $traces[] = '<strong>Global vars :</strong>';
	      $traces[] = $globalsTable  = self::formatArrayAsHtml(sfDebug::globalsAsArray());
	    }
	    return $traces;
	}
	static protected function getTraces($exception, $format = 'html')
  	{
	    $traceData = $exception->getTrace();
	    array_unshift($traceData, array(
	      'function' => '',
	      'file'     => $exception->getFile() != null ? $exception->getFile() : null,
	      'line'     => $exception->getLine() != null ? $exception->getLine() : null,
	      'args'     => array(),
	    ));
	
	    $traces = array();
	    if ($format == 'html')
	    {
	      $lineFormat = 'at <strong>%s%s%s</strong>(%s)<br />in <em>%s</em> line %s<br /><ul class="code" id="%s" style="display: %s">%s</ul>';
	    }
	    else
	    {
	      $lineFormat = 'at %s%s%s(%s) in %s line %s';
	    }
	
	    for ($i = 0, $count = count($traceData); $i < $count; $i++)
	    {
	      $line = isset($traceData[$i]['line']) ? $traceData[$i]['line'] : null;
	      $file = isset($traceData[$i]['file']) ? $traceData[$i]['file'] : null;
	      $args = isset($traceData[$i]['args']) ? $traceData[$i]['args'] : array();
	      $traces[] = sprintf($lineFormat,
	        (isset($traceData[$i]['class']) ? $traceData[$i]['class'] : ''),
	        (isset($traceData[$i]['type']) ? $traceData[$i]['type'] : ''),
	        $traceData[$i]['function'],
	        self::formatArgs($args, false, $format),
	        self::formatFile($file, $line, $format, null === $file ? 'n/a' : sfDebug::shortenFilePath($file)),
	        null === $line ? 'n/a' : $line,
	        'trace_'.$i,
	        'block',
	        self::fileExcerpt($file, $line)
	      );
	    }
	
	    return $traces;
  	}
}