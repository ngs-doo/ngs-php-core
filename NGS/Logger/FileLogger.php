<?php
namespace NGS;
require_once 'ILoggerPlugin.php';

class FileLogger implements ILoggerPlugin
{
	protected $filename;

	public function __construct($filename)
	{
		$this->filename = $filename;
	}

	public function dump($msg, $level)
	{
		if (is_array($msg))
			$msg = implode(PHP_EOL, $msg);

		$msg = '[' . strftime("%c") . '] ' . $msg . PHP_EOL;
		$fp = fopen($this->filename, 'a');
		fwrite($fp, $msg);
		fclose($fp);
	}
}
