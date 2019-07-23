<?php
class _User_traits
{
    
    private static function read_all()
    {
        return IO::jout("users" . DS . "shadow.json");
    }

    public static function find($field,$value)
    {
        $user = null;
        $list =  self::read_all();
        
        if(!$list) return Core::response(-1,"Shadow file is empty");

        $fields = ["id","name","pswd","cover","level"];
        $field = array_search($field,$fields);

        if($field===false) return Core::response(-2, "The field doesn't exists in context");

        foreach($list as $us) if($us[$field] == $value) $user = Convert::atoo(["id"=>$us[0],"name"=>$us[1], "pswd"=>$us[2], "cover"=>$us[3], "level"=>$us[4]]);

        // print_r($user); die;

        return $user ? $user : Core::response(0,"No user found with name: $name");
    }

}

class User
{
    /*
     * PRIVATE
     */


    /*
     * PROTECTED
     */
    protected static function pswd_check($user=null,$password=null)
    {        
        // echo $user . $password; die;

        if(!$user||!$password) return Core::response(-1,"User or password missing");

        if(APP::driver()==DATABASE) $tmp = Mysql::count("SELECT * FROM Users WHERE user='$user' and pswd='".hash("sha512",$password)."'") ? 1 : 0;
        else $tmp = _User_traits::find("name",$user)->pswd==hash("sha512",$password) ? 1 : 0;
        
        // echo "<pre>";
        // echo hash("sha512",$password) . " \n\n " .  _User_traits::find("name",$user)->pswd;
        // die;

        return $tmp ? 1 : 0;
    }


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
        return Request::sess("USER") ? 1 : 0;
    }

    public static function level($n=0)
    {
        if(!User::logged()) return Core::response(-1, "No user logged");
        if(App::driver()==DATABASE) return (int)Mysql::cell("Users","alvl")>=$n*1?1:0;
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
        echo $u ? $u->cover : 0; // return picture path
    }

    public function signin($name=null, $pswd=null)
    {
        if(!$name) $name=Request::in("user");
        if(!$pswd) $pswd=Request::in("pswd");

        if(self::pswd_check($name,$pswd))
        {
            $user = _User_traits::find("name",$name)->id;
            Request::sess("USER",$user);
            Request::cook("USER",$user);
            Request::cook("ACTIVE",$user,time()+3600);
            echo '1';
        }
        echo '0';
    }
}