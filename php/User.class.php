<?php
class _User_traits
{
    
    private static function read_all()
    {
        $shadow_file = "var" . DS . "users" . DS . "shadow.json";
        if(!is_file(IO::root().DS.$shadow_file)) IO::jin($shadow_file,[
            [
                "spume_rootuserid"
                , "admin"
                , "f10437341ee391ba992146453fc8fbbb226db4bacfe08503d04f89fe7404834b71f92178f0e5129ccf28f5dd185035f733278c2df825abf6da99bfb45aa66d0f"
                , "src/img/user.svg"
                , "9"
            ]
        ]);
        return IO::jout($shadow_file);
    }

    public static function find($field,$value)
    {
        $user = null;
        $list =  self::read_all();
        
        if(!$list) return Core::response(-1,"shadow file is empty");

        $fields = ["id","name","pswd","cover","level"];
        $field = array_search($field,$fields);

        if($field===false) return Core::response(-2, "field doesn't exists in context");

        foreach($list as $us) if($us[$field] == $value) $user = Convert::atoo(["id"=>$us[0],"name"=>$us[1], "pswd"=>$us[2], "cover"=>$us[3], "level"=>$us[4]]);

        // print_r($user); die;

        return $user ? $user : Core::response(0,"no user found");
    }

}

class User
{
    /*
     * PRIVATE
     */
    private static function pswd_check($user=null,$password=null)
    {        
        // echo $user . $password; die;

        if(!$user||!$password){ Core::response(-1,"user or password missing"); return 0; }

        if(APP::driver()==DATABASE) $tmp = Mysql::count("Users","user='$user' AND pswd='".hash("sha512",$password)."'") ? 1 : 0;
        else {
            $tmp = _User_traits::find("name",$user);
            $tmp = isset($tmp->pswd)&&$tmp->pswd==hash(App::$hash_algo,$password) ? 1 : 0;
            // echo "<pre>";
            // echo hash("sha512",$password) . " \n\n " .  _User_traits::find("name",$user)->pswd;
            // die;
        }
        return $tmp;
    }

    /*
     * PROTECTED
     */

    /*
     * PUBLIC
     */
    public static function logoff()
    {
        if(!User::logged()) return;
        request::sess("USER",false);
        request::cook("USER",false);
        request::cook("ACTIVE",false);
        @\setcookie("USER","",0,"/");
        @\setcookie("ACTIVE","",0,"/");
        @\session_start();
        @\session_unset();
        @\session_destroy();
        @\session_write_close();
        @\setcookie(\session_name(),"",0,"/");
        @\session_regenerate_id(true);
        @\header("Refresh:0");
    }

    ## return the code of a logged user, in a casa there"s no logged one, it return 0
    public static function logged()
    { 
        return Request::sess("USER") ? true : false;
    }

    public static function level($n=0)
    {
        if(!User::logged()) return Core::response(-1, "No user logged");
        if(App::driver()==DATABASE) return (int)Mysql::cell("Users","access_level")>=$n*1?1:0;
        else return _User_traits::find("id",Request::sess("USER"))->level*1 >= $n ? 1 : 0;
    }

    public static function name()
    {        
        if(!User::logged()) return Core::response(-1, "no user logged");
        return _User_traits::find("id",Request::sess("USER"))->name;
    }

    public static function exists($u=null)
    {
        if(!$u) $u = Request::in("user");

        // print_r(Request::in());
        // echo $u;

        if(!$u) return Core::response(-1, "no user given");

        $u = _User_traits::find("name",$u);
        if($u&&isset($u->pswd)) unset($u->pswd);
        return json_encode($u);
    }

    public function signin($name=null, $pswd=null)
    {
        if(!$name) $name=Request::in("user");
        if(!$pswd) $pswd=Request::in("pswd");

        if(self::pswd_check($name,$pswd))
        {
            $user = _User_traits::find("name",$name);
            if(isset($user->id)){
                $user = $user->id;
                Request::sess("USER",$user);
                Request::cook("USER",$user);
                Request::cook("ACTIVE",$user,time()+3600);
                return 1;
            } else return 0;
            Debug::show();
        }
        return 0;
    }
}