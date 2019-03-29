<?php
class Debug {
	public static function show(){
		echo "var" . DS . "logs" . DS . User::logged() . "-default.log" . PHP_EOL . IO::fread(IO::root("var" . DS . "logs" . DS . User::logged() . "-default.log"));
	}
}