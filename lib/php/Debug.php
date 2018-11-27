<?php
class Debug {
	public function show(){
		print_r(IO::fread(IO::root("var" . DIRECTORY_SEPARATOR . "logs" . DIRECTORY_SEPARATOR . User::logged() . "-default.log")));
	}
}