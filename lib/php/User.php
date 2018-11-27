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
    function passwd_check($c,$p){
        return queries::count("Users","code='".$c."' and pswd='".hash("sha256",$c.$p)."'") ? true : false;
    }


    ## usualy called on login screens
    ## checks if a given passphrase matches it"s informed user"s ownership
    ## keep the user logged using cookie if $k (stands for keep) is true (1)
    function signin($u,$p,$k=0){
        if(!($u && $p)) return -1;
        $o = queries::out("Users","user='$u'",AB___OBJECT);
        if(!$o->status()) return $o;
        if((int)core::conf("mail_check") && !(int)$o->data()->mchk) return ["status">-4,"data"=>"user's email is not checked up"];
        if($this->passwd_check($o->data()->code,$p)>0){
            Request::sess("USER",$o->data()->code);
            if($k) Request::cook("USER",$o->data()->code);
            queries::up("Users","last='".(new Date())->computable()."'","code='".$o->data()->code."'");
            return core::response(1,"signed");
        }else return core::response(0,"password doesn't match");
    }

    function level($l=0){ 
        $user=$this->logged();
        if(!$user) return core::response(0,"there's no logged user");
        $a = (int)queries::cell("Users","alvl");
        return ($a>=$l?$a:false);
    }
}