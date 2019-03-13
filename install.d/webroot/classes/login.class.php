<?php
class Login extends Page {
	public function check_if_user_exists(){
		$user = Request::in( "user" );
		$query = "SELECT * FROM Users WHERE user='$user'";
		if($user && Mysql::count($query)) echo Mysql::cell("Users", "id", "user='$user'");
		if( DEBUG ) echo PHP_EOL . ( $user ? "" : "NO USER DEFINED" );
	}
	public function check_if_pswd_matches(){
		echo User::passwd_check($this -> args("user"), $this -> args("pswd"));
	}

	public function logOff(){
		User::logoff();
	}

	public function onload(){
		$this -> view("login");
	}
}
