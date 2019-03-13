<?php
class Debug {
	public function show(){
		echo "var" . DC . "logs" . DC . User::logged() . "-default.log" . PHP_EOL . IO::fread(IO::root("var" . DC . "logs" . DC . User::logged() . "-default.log"));
	}
}