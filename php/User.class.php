<?php
class _User_traits
{
    
    private static function read_all()
    {
        $shadow_file = "var" . DS . "users" . DS . "shadow.json";
        if(!is_file(IO::root().DS.$shadow_file)) IO::jin($shadow_file, [
            [
                "rootuser"
                ,"System Administrator"
                ,"root"
                ,"b004a78f85f05acdc1eed219f14ee3128f9c9288b4391cfc85eed24a6a1f44c6f75aece4fc6425c5ea39a6ef42daa39a4cfdc18f7476e322d3a582e0736151ad"
                ,"src\\\/img\\\/user.svg"
                ,"9"
            ]
            , [
                "pubuser"
                ,"System Tester"
                ,"public"
                ,"ae66422aaeefe66a59cee8f28b8cbafb945b13e13f9a5bee7216401ead8c817a2844971fc0191a7e2d9486fd831b4349bd3b26b07366ecd2531d6a989e75947d"
                ,"src\\\/img\\\/user.svg"
                ,"0"
            ]
        ]);
        return IO::jout($shadow_file);
    }

    public static function find($field,$value)
    {
        //echo $value; die;

        $user = null;
        $list =  self::read_all();
        // print_r($list);die;

        if(!$list) return Core::response(-1,"shadow file is empty");

        $field = array_search($field,["id","name","user","pswd","cover","level"]);
        // echo $list[0][$field]; die;

        if($field===false) return Core::response(-2, "field doesn't exists in context");

        // foreach($list as $us) echo "<pre>\n".$us[$field] ."\n". $value . "\n\n"; print_r($list); die;
        foreach($list as $us) if($us[$field] == $value) $user = Convert::atoo(["id"=>$us[0], "name"=>$us[1], "user"=>$us[2], "pswd"=>$us[3], "cover"=>$us[4], "level"=>$us[5]]);

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
        $tmp = _User_traits::find("user",$user);
        // print_r($tmp);die;
        // echo "<pre>" . $password . "\n" . $tmp->pswd . "\n" . hash(App::$hash_algo,$password); die;
        $tmp = isset($tmp->pswd)&&$tmp->pswd==hash(App::$hash_algo,$password) ? $tmp : false;
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
        Request::sess("USER",false);
        Request::cook("USER",false);
        Request::cook("ACTIVE",false);
        @\setcookie("USER","",0,"/");
        @\setcookie("ACTIVE","",0,"/");
        @\session_start();
        @\session_unset();
        @\session_destroy();
        @\session_write_close();
        @\setcookie(\session_name(),"",0,"/");
        @\session_regenerate_id(true);
        // @\header("Refresh:0");
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
        if(!User::logged()) return Core::response(0, "no user logged");
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

    public function signin($hash=null)
    {
        $hash = Convert::base($hash ? $hash : Request::in("hash"));

        if(!isset($hash->user)) return Core::response(-1, "no user given");        
        if(!isset($hash->pswd)) return Core::response(-2, "no password given");

        $user = self::pswd_check($hash->user, $hash->pswd);
        
        if($user)
        {
            if(isset($user->id)){
                $user = $user->id;
                Request::sess("USER",$user);
                Request::cook("USER",$user);
                Request::cook("ACTIVE","true",time()+3600);
                return 1;
            } else return Core::response(-3, "no id found for user");
        }
        return Core::response(0, "incorrect credentials");;
    }
    
}