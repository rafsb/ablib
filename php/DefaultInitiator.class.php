<?php
class DefaultInitiator
{

	protected $argv = [];

	protected $argc = 0;

	protected $class_name = "";

	protected $method_name = "";
	
	
	protected function args($all=false)
	{
		return $all ? $this->argv : array_slice($this->argv,3);
	}

	public function __construct($argv=[], $argc=0)
	{
		$argv = is_array(Request::in()) ? array_merge($argv,Request::in()) : $argv;
		$this->argv = $argv;
		$this->argc = sizeof((array)$argv);
	}

}
