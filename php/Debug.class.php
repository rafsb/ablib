<?php
class Debug {
	public static function show(){
		echo PHP_EOL;
		echo PHP_EOL;
		echo "<pre style='text-align:left'>";
		echo PHP_EOL;
		echo PHP_EOL;
		print_r(Request::sess("DEBUG"));
		echo PHP_EOL;
		echo PHP_EOL;
		echo PHP_EOL;
		echo "var".DS."logs".DS.(User::logged() ? User::logged() : "default").".log";
		echo PHP_EOL;
		echo IO::read("var".DS."logs".DS.(User::logged() ? User::logged() : "default").".log");
		echo PHP_EOL;
		echo PHP_EOL;
		echo "var" . DS . "logs" . DS . "ng-error.log";
		echo PHP_EOL;
		echo IO::read("var" . DS . "logs" . DS . "ng-error.log");

		IO::write("var".DS."logs".DS.(User::logged() ? User::logged() : "default").".log","");
		IO::write("var".DS."logs".DS."ng-error.log","");
		
		die(PHP_EOL . "Debug::show result displayed...");
	}
}