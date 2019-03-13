<?php
class User {
    // clean all site"s data on local and server, even the session conn
    public function logoff()
    {
        @\setcookie("SP-USER","",0,"/");
        @\session_start();
        @\session_unset();
        @\session_destroy();
        @\session_write_close();
        @\setcookie(\session_name(),"",0,"/");
        @\session_regenerate_id(true);
        //@\header("Refresh:0");
    }
    
    ## return the code of a logged user, in a casa there"s no logged one, it return 0
    public function logged(){ return Request::sess("USER"); }

    public function user_name($c=null){ return queries::cell("Users","name","code='".($c?$c:user::logged())."'"); }

    public function exists($u){ if(!$u) return null; return queries::count("Users","code='$u'") ? true : false; }

    ## checks if a given passphrase matches it"s informed user"s ownership
    function passwd_check($user_id,$password, $datasource=DEFAULT_DB){
        //echo hash("sha256",$user_id.$password);
        //echo "SELECT * FROM Users WHERE code='$user_id' and pswd='".hash("sha256",$user_id.$password)."'";
        $tmp = Mysql::count("SELECT * FROM Users WHERE id='$user_id' and pswd='".hash("sha256",$user_id.$password)."'",$datasource) ? 1 : 0;
        if($tmp){
            Request::sess("USER",$user_id);
            Request::cook("USER",$user_id);
            Request::cook("ACTIVE",$user_id,time()+3600);
        }
        return $tmp;
    }

    function level($l=0){ 
        $user=$this->logged();
        if(!$user) return core::response(0,"there's no logged user");
        $a = (int)queries::cell("Users","alvl");
        return ($a>=$l?$a:false);
    }
}