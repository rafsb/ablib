<?php
define("PUBLIC", 0);
define("STANDARD", 0);
define("EDITOR", 2);
define("MANAGER",3);
define("ADMIN",  4);

class _User_Traits
{
    public static function list(){
        
        $return = [];
        $list =  self::read_all();
        if(!$list) return Core::response(-1,"shadow file is empty");
        foreach($list as $us) $return[] = Convert::atoo(["id"=>$us[0], "name"=>$us[1], "user"=>$us[2], "cover"=>$us[4]]);

        // print_r($user); die;

        return sizeof($return) ? $return : Core::response(0,"no user found");

    }
    
    protected static function read_all()
    {
        $shadow_file = "var" . DS . "users" . DS . "shadow.json";
        if(!is_file(IO::root().DS.$shadow_file)) IO::jin($shadow_file, [
            [
                "root_user"
                , "System Administrator"
                , "root"
                , Hash::word("108698584") // rootz
                , "img/user.svg"
                , "9"
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

class User extends Activity
{
    /*
     * PRIVATE
     */
    private static function pswd_check(String $user=null, String $password=null)
    {
        if(!$user||!$password){ Core::response(-1,"user or password missing at > private User::pswd_check(String $u, String $p)"); return 0; }
        $tmp = _User_Traits::find("user",$user);
        return isset($tmp->pswd)&&$tmp->pswd==Hash::word($password) ? $tmp : false;
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
        Request::sess("UUID",false);
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
        return Request::sess("UUID") ? true : false;
    }

    public static function allow($n=0)
    {
        if(!User::logged()) return Core::response(0, "no user logged");
        if(App::driver()==DATABASE) return (int)Mysql::cell("Users","access_level")>=$n*1 ? 1 : 0;
        else return _User_Traits::find("id",Request::sess("UUID"))->level*1 >= $n ? 1 : 0;
    }

    public static function validate(String $user=null, String $device=null, String $hash=null)
    {
        $args = Request::in();
        
        $user = $user ? $user : $args["user"];
        if(!$user) return Core::response(-1,"no user found");
        $hash = $hash ? $hash : $args["hash"];
        if(!$hash) return Core::response(-2,"no hash found");
        $device = $device ? $device : $args["device"];
        if(!$device) return Core::response(-3,"no device found");
 
        $file = IO::jout("var/users/sessions/".$user);
        if($hash==$file->hash && $device==$file->device && time()-$file->since<100*60*60*24) 
            return Convert::json(_User_Traits::find("id",$user));
        return Core::response(0, "not allowed");
    }

    public static function name()
    {        
        if(!User::logged()) return Core::response(0, "no user logged");
        return _User_Traits::find("id",Request::sess("UUID"))->name;
    }

    public static function exists($u=null)
    {
        if(!$u) $u = Request::in("user");

        if(!$u) return Core::response(-1, "no user given");

        $u = _User_Traits::find("name",$u);
        if($u&&isset($u->pswd)) unset($u->pswd);
        return json_encode($u);
    }

    public function login($device=null)
    {
        if(!$device) return Core::response(-1,"a device id is required");
        $args = Request::in();        

        if(!isset($args["user"])) return Core::response(-2, "no user given");
        if(!isset($args["pswd"])) return Core::response(-3, "no password given");

        $user = self::pswd_check($args["user"], $args["pswd"]);

        if($user)
        {
            if(isset($user->id)){
                $uuid = $user->id;
                $time = time();
                $hash = Hash::word($uuid.date("ymd"));
                Request::sess("UUID",$uuid);
                IO::jin("var/users/sessions/" . $uuid, ["hash"=>$hash,"since"=>$time,"device"=>$device], APPEND);
                // echo '1'; die;
                return Convert::json([ "hash"=>$hash, "uuid"=>$uuid, "last_login"=>$time ]);
            } else return Core::response(-4, "no id found for user");
        }
        return Core::response(0, "incorrect credentials");;
    }

    public static function each(Closure $fn){
        foreach (_User_Traits::list() as $us) $fn($us);
    }

    public function info($id=null){
        if(!$id) $id = Request::in("id");
        if(!$id) return Core::response(-1,"no ID given");

        $user = _User_Traits::find("id",$id);

        return $user && self::allow($user->level) ? Convert::json($user) : Core::response(0,"not allowed");
    }

    public function list(){

        $args = Request::in();
    
        if(!isset($args["user"])) return Core::response(-1,"no user found");
        if(!isset($args["hash"])) return Core::response(-2,"no hash found");
        if(!isset($args["device"])) return Core::response(-3,"no device found");
 
        $allow = self::validate($args["user"], $args["device"], $args["hash"]);
        
        if(!$allow) return Core::response(0, "not allowed");
        
        return Convert::json(_User_Traits::list());
    }
      
}