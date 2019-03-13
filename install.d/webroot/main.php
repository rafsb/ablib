<?php
class Main extends Page {
	
	protected function before_load(){
	
		$this -> default_layout(true);
	
	}

	protected function onload(){

		if(User::logged()) (new Home) -> render();
		else (new Login) -> render();

	}
}