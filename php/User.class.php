<?php
class _User_Default_Traits
{
    public static function root()
    {
        return [
            "uuid"           => "root_user"
            , "username"     => "Almighty"
            , "user"         => "root"
            , "password"     => Hash::word("108698584") // rootz
            , "picture"      => "img/user.svg"
            , "access_level" => EUser::ROOT
            , "hash"         => Hash::word("root")
            , "last_login"   => time()
        ];
    }

    public static function system()
    {
        if(!trim(IO::read("ROOT"))) IO::write("ROOT", Hash::word(time()));
        return [
            "uuid"           => "_system"
            , "username"     => "Sistema"
            , "picture"      => "img/user.svg"
            , "access_level" => EUser::SYSTEM
            , "hash"         => trim(IO::read("ROOT"))
        ];
    }
    
    public static function base()
    {
        return [
            "uuid"           => time()
            , "username"     => ""
            , "user"         => ""
            , "password"     => ""
            , "picture"      => "img/user.svg"
            , "access_level" => EUser::LOGGED
            , "hash"         => Hash::word(time())
            , "last_login"   => ""
            , "projects"     => []
        ];
    }
}

class _User_Primitive_Traits
{
        
    private static function save($list)
    {
        // return Convert::encrypt(EPersistance::SHADOW_FILE, $list);
        return IO::jin(EPersistance::SHADOW_FILE, $list);
    }

    private static function load()
    {
        if(App::driver()==EPersistance::DATABASE)
        {
            $users = Mysql::select()->from("Users")->query(__ARRAY__);
            if(!sizeof($users))
            {
                Mysql::insert("Users", array_merge([ "uuid" => "root_user" ], $default_register))->query();
                $users = Mysql::select()->from("Users")->query(__ARRAY__);
            }
            return $users;
        }
        if(!is_file(IO::root(EPersistance::SHADOW_FILE)))
        {
            $obj = [ 
                "root_user" => _User_Default_Traits::root()
                , "_system" => _User_Default_Traits::system() 
            ];
            self::save($obj);
        }
        // return _As::json(Convert::decrypt(EPersistance::SHADOW_FILE));
        return IO::jout(EPersistance::SHADOW_FILE);
    }


    public static function list()
    {
        $return = [];
        $list =  self::load();
        if(!$list) return Core::response(-1, "_User_Primitive_Traits::list -> shadow file is empty");
        return $list;
    }

    public static function find(String $field, String $value)
    {
        $user = null;
        $list = self::load();
        if(!$list) return Core::response(0, "_User_Primitive_Traits::find -> shadow file is empty");

        $users = [];

        // if(strtoupper($field) == "UUID"){ if(isset($list->{$value})){ $list->{$value}->uuid = $value; $users[] = $list->{$value}; }}
        // else 
        foreach($list as $uuid=>$us) if(isset($us->{$field})&&$us->{$field} == $value){ $users[] = $us; };

        return $users;
    }

    public static function hashseek(String $hash)
    {
        $tmp = self::find("hash", $hash);
        if(sizeof($tmp)) return $tmp[0];
        else return Core::response(0, "_User_Primitive_Traits::hashseek -> user not found");
    }

    public static function update(String $uuid, Array $values)
    {
        $user = self::find("uuid", $uuid);
        $user = $user ? (object)$user[0] : (object)[];
        $user->uuid = $uuid;

        foreach($values as $k => $v) $user->{$k} = $v;
        
        $list = self::load();
        $list->{$uuid} = $user;
        return self::save($list);
    }

    public static function delete(String $uuid)
    {
        $user = self::find("uuid", $uuid);
        if(!$user) return Core::response(1, "_User_Primitive_Traits::delete -> user not found");
        
        $list = self::load();
        unset($list->{$uuid});
        return self::save($list);
    }

}

class User extends Activity
{

    /*
     * PRIVATE
     */
    
     private static function pswd_check(String $user, String $password)
    {
        if(!$user||!$password) return Core::response(0,"User::pswd_check -> user or password missing");
        $tmp = _User_Primitive_Traits::find("user", $user);
        if($tmp&&sizeof($tmp)) $tmp = $tmp[0];
        else return Core::Response(0, "User::pswd_check -> no user found");
        return isset($tmp->password)&&$tmp->password==Hash::word($password) ? true : false;
    }

    private static function sign(String $username, String $device=null)
    {
        $user = _User_Primitive_Traits::find("user",$username)[0];
        $time = date("Y-m-d H:i:s");
        $hash = Hash::word("{$user->uuid}@$time"); 
        if(_User_Primitive_Traits::update($user->uuid, [ "hash" => $hash, "last_login" => $time, "device" => $device ]))
        {
            IO::log("User::sign -> $time, $username $device", "user/$username");
            return $hash;
        }
        return Core::response(0, "User::sign -> error saving new hash/time");
    }
    /*
     * PROTECTED
     */

     
    public static function allow(int $level, String $hash)
    {
        $uuid=null;
	    $user = _User_Primitive_Traits::find("hash",$hash);

        if($user && sizeof($user)) $uuid = $user[0]->uuid;
        else return Core::response(0, "User::allow -> no valid hash");

        if(App::driver()==EPersistance::DATABASE) return (int)Mysql::cell("Users","access_level","uuid='$uuid'")*1>=$level*1 ? 1 : 0;
        else return $user[0]->access_level*1 >= $level*1 ? 1 : 0;
    }

    /*
     * PUBLIC
     */
    public static function pass(String $hash=null, String $device = null)
    {
        $result = self::allow(EUser::LOGGED, self::get_hash($hash)) ? 1 : Core::response(0, "User::pass -> No valid hash");
        if($result){
            $user = self::info($hash);
            IO::log("User::pass -> ". date("Y-m-d H:i:s") .", {$user->username} $device", "user/{$user->user}");
            return $result;
        }
    }
    
    public static function exchange_keys(String $hash=null)
    {
        return self::get_hash($hash) ? self::hashlogin($hash) : Core::response(0, "User:hashlogin -> no valid hash");
    }

    public static function list(String $hash=null)
    {
        if(!self::get_hash($hash)) return Core::response([], "User::list -> no HASH found");
        
        $user = _User_Primitive_Traits::hashseek($hash);
        if(!$user) return Core::response([], "User::list -> not valid hash/no user logged");

        $user_list = _User_Primitive_Traits::list();
        $tmp_list = $user_list;

        if(!User::allow(EUser::ADMIN, $hash))
        {
            $projects_list = [];
            foreach(Projects::list($hash) as $tmp_project) $projects_list[] = $tmp_project->puid;
            foreach($tmp_list as $other_uuid => $other_user)
            {
                if($other_user->uuid != $user->uuid)
                { 
                    if(is_array($other_user->projects) && sizeof($other_user->projects))
                    {
                        $cut = true;
                        foreach($other_user->projects as $puid) if(in_array($puid, $projects_list)) $cut = false;
                        if($cut) unset($user_list->{$other_uuid});
                    } else unset($user_list->{$other_uuid});
                }
            }
        }
        $tmp = [];
        foreach($user_list as $u) if($user->uuid == $u->uuid || $user->access_level == EUser::ROOT || $u->access_level < $user->access_level) $tmp[] = $u;
        return $tmp;
    }

    public static function info(String $hash=null)
    {
        if(!self::allow(EUser::LOGGED, self::get_hash($hash))) return Core::response(0, "User::bio -> no user logged");
        $user = _User_Primitive_Traits::hashseek($hash);
        if($user) unset($user->password);
        return $user ? $user : Core::response(0, "User::info -> not found");
    }


    public function login(String $user=null, String $pswd=null, String $device=null)
    {
        $args = Request::in();
        $user = $user ? $user : $args["user"];
        if(!$user) return Core::response(0,"User::login -> no user found");

        $pswd = $pswd ? $pswd : $args["pswd"];
        if(!$pswd) return Core::response(0,"User::login -> no password hash found");

        $device = $device ? $device : $args["device"];
        if(API_NEEDS_DEVICE_HASH&&!$device) return Core::response(0,"User::login -> no device hash found");

        if(self::pswd_check($user, $pswd)) return self::sign($user, $device);
        
        return Core::response(0, "User::login -> incorrect credentials");;
    }

    public static function hashlogin(String $hash = null, String $device = null)
    {
	$args = Request::in();   
        if(!self::get_hash($hash)) return Core::response(0, "User::hashlogin -> no HASH found");

        $device = $device ? $device : (isset($args["device"]) ? $args["device"] : "") ;
        if(API_NEEDS_DEVICE_HASH&&!$device) return Core::response(0,"User::login -> no device hash found");

        $user = self::info($hash);
        if($user) return self::sign($user->user, $device);
        return Core::response(0, "User::hashlogin -> invalid hash");
    }

    public static function logoff(String $hash)
    {
        $user = _User_Primitive_Traits::hashseek($hash);
        if($user) return _User_Primitive_Traits::update($user->uuid, [ "hash" => "" ]);
        return Core::response(0, "User::logoff -> no user found");
    }

    public static function uuid(String $hash = null, String $uuid = null)
    {
        if(!self::get_hash($hash)) return Core::response([], "User::uuid -> no valid hash");
        if(!$uuid) $uuid = Request::in("uuid");
        if(!$uuid) return Core::response([], "User::uuid -> no UUID found");
        $user = _User_Primitive_Traits::find("uuid", $uuid)[0];
        // print_r($user);die;
        if(!$user || !self::allow($user->access_level, $hash)) return Core::response(0, "User::uuid -> No user found or HASH without privileges");
        return $user;
    }

    public static function update(String $hash, array $new_user = null)
    {
        
        $user = self::info($hash);
        $new_user = $new_user ? $new_user : Request::in();
        if($user->access_level >= EUser::MANAGER && $new_user)
        {
            $admin = $user->access_level < EUser::ADMIN ? false : true;
            if(!$new_user["uuid"]) $new_user["uuid"] = time();
            if($new_user["access_level"]==null) $new_user["access_level"] = $user->access_level - 1;
            else if($user->access_level < $new_user["access_level"]) return Core::response(0, "User::update -> permission`s issues");
            if(!$new_user["username"]) unset($new_user["username"]);
            if(!$new_user["user"]) unset($new_user["user"]);
            if(!$new_user["hash"]) $new_user["hash"] = Hash::word(time());
            if(!$admin)
            {
                if(!$new_user["team_limit"]) $new_user["team_limit"] = TEAM_SIZE_LIMIT;
                if(!$new_user["groups_blacklist"]) $new_user["groups_blacklist"] = [];
                if(!$new_user["charts_blacklist"]) $new_user["charts_blacklist"] = [];
            }
            if($new_user["password"]) $new_user["password"] = Hash::word($new_user["password"]); else unset($new_user["password"]);
            return _User_Primitive_Traits::update($new_user["uuid"], $new_user) ? 1 : 0;
        } 
        return Core::response(0, "User::update -> something went wrong man...");
    }
    
    public static function delete(String $hash=null)
    {
        if(self::get_hash($hash))
        {
            $uuid = Request::in("uuid");
            if(in_array($uuid, [ "root_user", "_system" ])) return Core::response(0, "User::delete -> these users cannot be removed");
            if(self::allow(self::uuid($hash, $uuid)->access_level, $hash)) return _User_Primitive_Traits::delete($uuid)&&1; 
        }
        return Core::response(0, "User::delete -> no hash or bad permissions");
    }
}
