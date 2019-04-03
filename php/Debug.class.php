<?php
class Debug {
	public static function show(){
		echo "<pre>";
		echo "var" . DS . "logs" . DS . User::logged() . "-default.log";
		echo PHP_EOL;
		echo PHP_EOL;
		echo PHP_EOL;
		echo IO::fread(IO::root("var" . DS . "logs" . DS . User::logged() . "-default.log"));
		echo PHP_EOL;
		echo PHP_EOL;
		echo PHP_EOL;
		echo Request::sess("DEBUG");
	}
}