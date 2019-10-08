<?php
class DefaultInitiator extends IO
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
		$argv = array_merge($argv,Request::in());
		$this->argv = $argv;
		$this->argc = sizeof($argv);
	}

}
