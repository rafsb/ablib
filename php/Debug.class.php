<?php
class Debug {
	public static function show(){
		echo "<br/><pre class='-left -content-left'>";
		echo "var" . DS . "logs" . DS . User::logged() . "-default.log";
		echo PHP_EOL;
		echo PHP_EOL;
		echo PHP_EOL;
		echo IO::read("var" . DS . "logs" . DS . User::logged() . "-default.log");
		echo PHP_EOL;
		echo PHP_EOL;
		echo PHP_EOL;
		echo print_r(Request::sess("DEBUG"));
	}
}