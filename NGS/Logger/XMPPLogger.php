<?php
namespace NGS;
require_once 'ILoggerPlugin.php';

class XMPPLogger implements ILoggerPlugin
{
	protected $url;

	public function __construct($url)
	{
		$this->url = $url;
	}

	public function dump($msg, $level)
	{
		if (is_array($msg))
			$msg = implode(PHP_EOL, $msg);

		$msg .= PHP_EOL . PHP_EOL;
		file_get_contents($this->url . '/?m=' . rawurlencode($msg));
	}
}
