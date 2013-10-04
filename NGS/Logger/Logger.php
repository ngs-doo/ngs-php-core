<?php
namespace NGS;

class Logger
{
	const INFO = 0;
	const NOTICE = 1;
	const WARNING = 2;
	const ERROR = 3;
	const FATAL = 4;

	protected $plugins = array();
	protected $level = self::WARNING;

	protected function setPlugins($plugins = null)
	{
		if ($plugins === null) {
			$plugins = array();
		}

		$this->plugins = $plugins;
		return $this;
	}

	protected function setLevel($level)
	{
		if ($level !== null)
			$this->level = $level;

		return $this;
	}

	public function __construct($plugins = null, $level = null)
	{
		$this->setPlugins($plugins);
		$this->setLevel($level);
	}

	protected static $instance = null;
	public static function instance($instance = null)
	{
		if ($instance !== null)
			self::$instance = $instance;

		return self::$instance;
	}

	public function log($msg, $level)
	{
		if ($this->level <= $level)
			foreach ($this->plugins as $child)
				$child->dump($msg, $level);

		return $this;
	}


	public function info($msg)
	{
		return $this->log($msg, self::INFO);
	}

	public function notice($msg)
	{
		return $this->log($msg, self::NOTICE);
	}

	public function warning($msg)
	{
		return $this->log($msg, self::WARNING);
	}

	public function error($msg)
	{
		return $this->log($msg, self::ERROR);
	}

	public function fatal($msg)
	{
		return $this->log($msg, self::FATAL);
	}
}

Logger::instance(new Logger());

