<?php
class Debug extends Activity 
{
	public static function show(String $hash=null)
	{
		if(!self::get_hash($hash)) return Core::response(0, "Debug::show -> no hash given");
		echo PHP_EOL;
		echo PHP_EOL;
		echo "================================================" . PHP_EOL;
		echo "Debug::show -> started...". PHP_EOL;
		echo "var".DS."logs".DS."debug.log" . PHP_EOL;
		echo IO::read("var".DS."logs".DS."debug.log") . PHP_EOL;
		echo "var" . DS . "logs" . DS . "error.log" . PHP_EOL;
		echo IO::read("var" . DS . "logs" . DS . "error.log") . PHP_EOL;
		IO::write("var".DS."logs".DS."debug.log","");
		IO::write("var".DS."logs".DS."error.log","");
		echo "Debug::show -> finished..." . PHP_EOL;
	}

	public static function render()
	{
		if(!DEBUG) return;
		echo "<pre>";
		self::show();
	}
}